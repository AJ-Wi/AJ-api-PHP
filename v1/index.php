<?php
include_once '../helpers/error.php';
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       $Date:$ Nov-2015
 * @Version:    $Rev:$ 1.1
 * @Developer:  Federico Guzman (federicoguzman@gmail.com)
 * @Modified:   $Date:$ MAY-2021
 * @Developer:  Wladimir Perez (tropaguararia28@gmail.com)
 **/

/* Los headers permiten acceso desde otro dominio (CORS) a nuestro REST API o desde un cliente remoto via HTTP
 * Removiendo las lineas header() limitamos el acceso a nuestro RESTfull API a el mismo dominio
 * Nótese los métodos permitidos en Access-Control-Allow-Methods. Esto nos permite limitar los métodos de consulta a nuestro RESTfull API
 * Mas información: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 **/
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); 

//Importamos la gestion de mensajes de error.
include_once '../helpers/response.php';


/* Aqui incluimos los archivos que se encargaran de manejar la base de datos y procesar la logica de:
*   recuperacion
*   guardado
*   actualizacion
*   eliminacion
**/
include_once '../class/DaBalones.php';

/** inicializando la libreria Slim **/
require '../libs/Slim/Slim.php'; 
\Slim\Slim::registerAutoloader(); 
$app = new \Slim\Slim();

/* Usando GET para traer todos los balones */
$app->get('/balones', function() {    
    //recuperamos todos los registros del manejador de balones.
    $response = DaBalones::recuperarTodos();
    //Imprimimos la respuesta.
    echoResponse(200, $response);  
});

/* Usando GET con parametro para traer el registro de un balon */
$app->get('/balones/:serial', function ($serial) {
    $response = DaBalones::buscarPorSerial($serial);
    echoResponse(200, $response);
});

/* Usando POST para crear un balon */
$app->post('/balones', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('serial', 'capacidad', 'tulipa', 'marca', 'estado', 'operacion'));
    //capturamos los parametros recibidos y los decodificamos a un nuevo array
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    //creamos una nueva instancia de la clase DaBalones con los datos capturados
    $balon = new DaBalones($param['serial'], $param['capacidad'], $param['tulipa'], $param['marca'], $param['estado'], $param['operacion']);
    $response = $balon->guardar();
    echoResponse(201, $param);
});

/* Usando PUT para actualizar un balon */
$app->put('/balones/:serial', 'authenticate', function($serial) use ($app) {
    //capturamos los parametros recibidos y los almacenamos como un nuevo array
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    
    $bal = DaBalones::buscarPorSerial($serial);
    //chequeamos si hay un error en la llamada a buscar el registro
    if(isset($bal['result']['error_id'])){
        $response = $bal;
    }else{
        //seleccionamos los datos a pasar como parametros.
        $capacidad = isset($param['capacidad'])?$param['capacidad']:$bal['capacidad'];
        $tulipa = isset($param['tulipa'])?$param['tulipa']:$bal['tulipa'];
        $marca = isset($param['marca'])?$param['marca']:$bal['marca'];
        $estado = isset($param['estado'])?$param['estado']:$bal['estado'];
        $operacion = isset($param['operacion'])?$param['maroperacionca']:$bal['operacion'];
        //creamos una nueva instancia de la clase DaBalones con los datos seleccionados en el paso anterior
        $balon = new DaBalones($serial, $capacidad, $tulipa, $marca, $estado, $operacion);
        $response = $balon->guardar();
    }
    echoResponse(201, $param);
});

/* Usando DELETE para eliminar un registro de un balon */
$app->delete('/balones/:serial', function ($serial) {
    $response = DaBalones::eliminar($serial);
    echoResponse(200, $response);
});

/* corremos la aplicación */
$app->run();

/*********************** USEFULL FUNCTIONS **************************************/

/**
 * Verificando los parametros requeridos en el metodo o endpoint
 */
function verifyRequiredParams($required_fields) {
    $_respuestas = new respuestas();
    $error = false;
    $error_fields = "";
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $app = \Slim\Slim::getInstance();
        $request_params = json_decode($app->request()->getBody(), true);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty ';        
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
/**
 * Validando parametro email si necesario; un Extra ;)
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
/**
 * Mostrando la respuesta en formato json al cliente o navegador
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    if(isset($response["result"]["error_id"])){
        $app->status($response["result"]["error_id"]);
    }else{
        $app->status($status_code);
    } 
    // setting response content type to json
    $app->contentType('application/json'); 
    echo json_encode($response);
}

/**
 * Agregando un leyer intermedio y autenticación para uno o todos los metodos, usar segun necesidad
 * Revisa si la consulta contiene un Header "Authorization" para validar
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        //$db = new DbHandler(); //utilizar para manejar autenticacion contra base de datos
 
        // get the api key
        $token = $headers['Authorization'];
        define('API_KEY','3d524a53c110e4c22463b10ed32cef9d');
        // validating api key
        if (!($token == API_KEY)) { //API_KEY declarada en Config.php
            
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Acceso denegado. Token inválido " . $token;
            echoResponse(401, $response);
            
            $app->stop(); //Detenemos la ejecución del programa al no validar
            
        } else {
            //procede utilizar el recurso o metodo del llamado
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Falta token de autorización";
        echoResponse(400, $response);
        
        $app->stop();
    }
}
?>

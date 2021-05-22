<?php
/**
 * AJ-dev-api - a micro PHP API
 *
 * @author      Wladimir Perez <tropaguararia28@gmail.com>
 * @copyright   2021 wladimir perez
 * @link        https: //github.com/cvcell/AJ-dev-api.git
 * @license     https: //github.com/cvcell/AJ-dev-api/blob/master/LICENSE
 * @version     0.1.0
 * @package     AJ-dev-api
 *
 */

date_default_timezone_set('America/Lima');
require_once '../class/DaUsuarios.php';
require_once '../class/Response.php';

class Auth
{
    public static function login($param)
    {

        //verificamos que lleguen las variables requeridas.
        if (!isset($param['user']) || !isset($param["password"])) {
            return Response::message_400();
        }

        //obtenemos los datos del usuario a loguearse
        $paramOld = DaUsuarios::getByUser($param['user']);

        //verificamos que el usuario exista
        if ($paramOld['status_id'] != "200") {
            return $paramOld;
        }

        //verificamos el password coincide con la BBDD.
        if (!password_verify($param['password'], $paramOld['response']['password'])) {
            return Response::message_400("Password invalido");
        }

        //verificamos si el estado del usuario esta activo.
        if ($paramOld['response']['estado'] == "inactivo") {
            return Response::message_400("El usuario esta inactivo");
        }

        //Generamos el nuevo token para el usuario.
        $response = self::generateToken($paramOld['response']['dni']);

        //verificamos que no haya error en la creacion del token.
        if ($response['status_id'] != "201") {
            return Response::message_500("Error interno, No hemos podido guardar");
        }

        //retornamos el token al cliente.
        return $response;
    }

    private static function generateToken($num)
    {
        $param = array(
            "token" => bin2hex(openssl_random_pseudo_bytes(16, $val)),
            "date" => date('Y-m-d H:i'),
        );
        $response = DaUsuarios::updateToken($num, $param);
        return $response;
    }
}

/**
 * Agregando una autenticación para uno o todos los metodos, usar segun necesidad
 * Revisa si la consulta contiene un Header "Authorization" para validar
 */
function authenticate(\Slim\Route $route)
{
    // Getting request headers
    $headers = apache_request_headers();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $token = $headers['Authorization'];
        //Manejando autenticacion contra base de datos
        $response = DaUsuarios::getByToken($token);

        //verificamos que el token exista y este verificado
        if ($response['status_id'] != "200") {
            // api key is not present in users table
            Response::echoResponse(Response::message_401("Acceso denegado. Token inválido: " . $token));
            //Detenemos la ejecución del programa al no validar
            $app->stop();
        }
    } else {
        // api key is missing in header
        Response::echoResponse(Response::message_401("Falta token de autorización"));
        $app->stop(); //Detenemos la ejecución del programa al no validar
    }
}

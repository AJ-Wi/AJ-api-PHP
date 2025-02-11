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

/* Los headers permiten acceso desde otro dominio (CORS) a nuestro API o desde un cliente remoto via HTTP
 * Removiendo las lineas header() limitamos el acceso a nuestro API a el mismo dominio
 * Nótese los métodos permitidos en Access-Control-Allow-Methods. Esto nos permite limitar los métodos de consulta a nuestro API
 * Mas información: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 **/
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

/********************** Importaciones de scripts necesarios ********************************
 *   Slim.php        libreria encargada de manejar las rutas o endpoint.
 *   errorDev.php    script para mostrar errores de php en el explorador o cliente api.
 *   auth.php        script para autenticar a los usuarios con acceso a la api.
 *   Da*.php         acceso a datos encargados de realizar los CRUD por cada tabla de la BBDD
 **/
require '../libs/Slim/Slim.php';
require_once '../class/Response.php';
include_once '../helpers/error.php';
include_once '../security/auth.php';
include_once '../class/DaBalones.php';
include_once '../class/DaClientes.php';
include_once '../class/DaMovimientos.php';
include_once '../class/DaUsuarios.php';

/** inicializando la libreria Slim **/
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->get('/', function () use ($app) {$app->redirect('../', 301);});

/********* Controlador de autenticacion *******************
 *  @method     Usando POST para autenticar usuario
 *  @user
 *  @password
 * **/
$app->post('/auth', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = Auth::login($param);
    Response::echoResponse($response);
});

/********* Controlador para balones *************************/
/* Usando GET para traer todos los balones */
$app->get('/balones', 'authenticate', function () {
    $response = DaBalones::getAllTank();
    Response::echoResponse($response);
});

/* Usando GET con parametro para traer el registro de un balones */
$app->get('/balones/:id', 'authenticate', function ($id) {
    $response = DaBalones::getTankById($id);
    Response::echoResponse($response);
});

/* Usando POST para crear un balones */
$app->post('/balones', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaBalones::saveTank($param);
    Response::echoResponse($response);
});

/* Usando PUT para actualizar un balones */
$app->put('/balones/:id', 'authenticate', function ($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaBalones::updateTank($id, $param);
    Response::echoResponse($response);
});

/* Usando DELETE para eliminar un registro de un balon */
$app->delete('/balones/:id', 'authenticate', function ($id) {
    $response = DaBalones::deleteTank($id);
    Response::echoResponse($response);
});

/********* controlador para clientes **********************************/
$app->get('/clientes', 'authenticate', function () {
    $response = DaClientes::getAll();
    Response::echoResponse($response);
});

$app->get('/clientes/:id', 'authenticate', function ($id) {
    $response = DaClientes::getById($id);
    Response::echoResponse($response);
});

$app->post('/clientes', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaClientes::save($param);
    Response::echoResponse($response);
});

$app->put('/clientes/:id', 'authenticate', function ($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaClientes::update($id, $param);
    Response::echoResponse($response);
});

$app->delete('/clientes/:id', 'authenticate', function ($id) {
    $response = DaClientes::delete($id);
    Response::echoResponse($response);
});

/********* controlador para Usuarios **********************************/
$app->get('/usuarios', 'authenticate', function () {
    $response = DaUsuarios::getAll();
    Response::echoResponse($response);
});

$app->get('/usuarios/:id', 'authenticate', function ($id) {
    $response = DaClientes::getById($id);
    Response::echoResponse($response);
});

$app->post('/usuarios', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaUsuarios::save($param);
    Response::echoResponse($response);
});

$app->put('/usuarios/:id', 'authenticate', function ($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaUsuarios::update($id, $param);
    Response::echoResponse($response);
});

$app->delete('/usuarios/:id', 'authenticate', function ($id) {
    $response = DaUsuarios::delete($id);
    Response::echoResponse($response);
});

/********* controlador para Movimientos *******************************/
$app->get('/movimientos', 'authenticate', function () {
    $response = DaMovimientos::getAll();
    Response::echoResponse($response);
});

$app->get('/movimientos/:id', 'authenticate', function ($id) {
    $response = DaMovimientos::getSend($id);
    Response::echoResponse($response);
});

$app->post('/movimientos', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaMovimientos::save($param);
    Response::echoResponse($response);
});

$app->post('/movimientos/relationship', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaMovimientos::saveRelationship($param);
    Response::echoResponse($response);
});

$app->post('/movimientos/recepcion', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaMovimientos::saveRecepcion($param);
    Response::echoResponse($response);
});

$app->put('/movimientos/:id', 'authenticate', function ($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaMovimientos::update($id, $param);
    Response::echoResponse($response);
});

$app->delete('/movimientos/:id', 'authenticate', function ($id) {
    $response = DaMovimientos::delete($id);
    Response::echoResponse($response);
});

/********* controlador para pagos *************************************/
$app->get('/pagos', 'authenticate', function () {
    $response = DaPagos::getAll();
    Response::echoResponse($response);
});

$app->get('/pagos/:id', 'authenticate', function ($id) {
    $response = DaPagos::getById($id);
    Response::echoResponse($response);
});

$app->post('/pagos', 'authenticate', function () use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaPagos::save($param);
    Response::echoResponse($response);
});

$app->put('/pagos/:id', 'authenticate', function ($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaPagos::update($id, $param);
    Response::echoResponse($response);
});

$app->delete('/pagos/:id', 'authenticate', function ($id) {
    $response = DaPagos::delete($id);
    Response::echoResponse($response);
});

/* corremos la aplicación */
$app->run();

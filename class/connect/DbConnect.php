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

require_once dirname(__FILE__, 2) . "/Response.php";
include_once dirname(__FILE__) . '/config.php';

class DbConnect extends PDO
{

    public function __construct()
    {
        //Recupero la configuracion de la conexion.
        //include_once dirname(__FILE__) . '/config.php';
        //Sobreescribo el método constructor de la clase PDO.
        try {
            parent::__construct(DB_TYPE . ":dbname=" . DB_NAME . ";host=" . DB_HOST . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            Response::echoResponse(
                Response::message_500('Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage())
            );
            exit;
        }
    }

    public static function encrypt($pass)
    {
        return password_hash($pass, PASSWORD_DEFAULT);
    }

}

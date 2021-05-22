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

require_once "Response.php";

/**
 *Ejecuta las consulta a la BBDD
 *
 * @package AJ-dev-api
 * @author  wladimir perez
 * @since   0.1.0
 *
 * @method  "getRow", "getAllRow", "saveRow", "updateRow", "deleteRow", "rowExists"
 *
 */

class DbHandler
{

    public static function query($sql, $method = "getAllRow", $param = null)
    {
        try {
            $sql->execute();

            if ($method == "getRow") {$response = self::responseValid($sql->fetch(PDO::FETCH_ASSOC), $sql->rowCount());}
            if ($method == "getAllRow") {$response = self::responseValid($sql->fetchAll(PDO::FETCH_ASSOC), $sql->rowCount());}
            if ($method == "saveRow") {$response = self::responseValid($param, $sql->rowCount());}
            if ($method == "updateRow") {$response = self::responseValid($param, $sql->rowCount());}
            if ($method == "deleteRow") {$response = self::responseValid("Registro eliminado.", $sql->rowCount());}
            if ($method == "rowExists") {$response = $sql->rowCount();}

            return $response;

        } catch (PDOException $e) {
            Response::echoResponse(
                Response::message_500('Ha surgido un error y no se puede ejecutar la consulta a la base de datos. Detalle: ' . $e->getMessage())
            );
            exit;
        }
    }

    private static function responseValid($response, $count)
    {
        if ($count) {
            return Response::message_200($response);
        } else {
            return Response::message_400();
        }
    }

    public static function paramRequired($params, $fields)
    {
        foreach (array_keys($fields, "required") as $field) {
            if (!isset($params[$field])) {return false;}
        }
        return true;
    }

    public static function updateParams($params, $paramOld, $fields)
    {
        foreach (array_keys($fields) as $field) {
            if (!isset($params[$field])) {
                $params[$field] = $paramOld["response"][$field];
            }
        }
        return $params;
    }

    public static function bindFields($sql, $params, $fields)
    {
        foreach (array_keys($fields) as $field) {
            $sql->bindParam(":" . $field, $params[$field]);
        }
        return $sql;
    }
}

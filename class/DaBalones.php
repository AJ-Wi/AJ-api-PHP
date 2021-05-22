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
 * @file        Clase Modelo
 *
 */

require_once "connect/DbConnect.php";
require_once "DbHandler.php";
require_once "Response.php";

class DaBalones
{
    const TABLA = "balones";
    private static $fields = [
        "serial" => "optional",
        "capacidad" => "required",
        "tulipa" => "required",
        "marca" => "required",
        "operacion" => "required",
    ];

    private static function conn()
    {
        return new DbConnect();
    }

    public static function getAllTank()
    {
        $sql = self::conn()->prepare(
            "SELECT * FROM " . self::TABLA . " ORDER BY serial"
        );
        return DbHandler::query($sql);
    }

    public static function getTankById($id)
    {
        $sql = self::conn()->prepare(
            "SELECT * FROM " . self::TABLA . " WHERE serial = :serial"
        );
        $sql->bindParam(":serial", $id);
        return DbHandler::query($sql, "getRow");
    }

    public static function saveTank($params)
    {
        if (self::existsTank($params["serial"])) {return Response::message_405();}
        if (!DbHandler::paramRequired($params, self::$fields)) {return Response::message_400();}
        $sql = self::conn()->prepare(
            "INSERT INTO " .
            self::TABLA .
            " (serial, capacidad, tulipa, marca, operacion) VALUES(:serial, :capacidad, :tulipa, :marca, :operacion)"
        );
        $sql = DbHandler::bindFields($sql, $params, self::$fields);
        return DbHandler::query($sql, "saveRow", $params);
    }

    public static function updateTank($id, $params)
    {
        $paramOld = self::getTankById($id);
        if ($paramOld['status_id'] == "400") {return $paramOld;}
        $newParams = DbHandler::updateParams($params, $paramOld, self::$fields);
        $sql = self::conn()->prepare(
            "UPDATE  " .
            self::TABLA .
            " SET capacidad = :capacidad, tulipa = :tulipa, marca = :marca, operacion = :operacion WHERE serial = :serial"
        );
        $sql = DbHandler::bindFields($sql, $newParams, self::$fields);
        return DbHandler::query($sql, "updateRow", $newParams);
    }

    public static function deleteTank($id)
    {
        $sql = self::conn()->prepare(
            "DELETE FROM " . self::TABLA . " WHERE serial = :serial"
        );
        $sql->bindParam(":serial", $id);
        return DbHandler::query($sql, "deleteRow");
    }

    public static function existsTank($id)
    {
        $sql = self::conn()->prepare(
            "SELECT serial FROM " .
            self::TABLA .
            " WHERE serial = :serial LIMIT 1"
        );
        $sql->bindParam(":serial", $id);
        return DbHandler::query($sql, "rowExists");
    }
}

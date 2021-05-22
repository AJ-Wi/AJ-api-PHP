<?php
require_once 'connect/DbConnect.php';
require_once 'Response.php';

class DaPagos
{
    const TABLA = 'pagos';

    public static function getAll()
    {
        $conn = new DbConnect();
        $_response = new Response();
        $sql = $conn->prepare('SELECT * FROM ' . self::TABLA . ' ORDER BY id');
        try {
            $sql->execute();
            return $_response->message_200($sql->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return $_response->message_400("Registros NO recuperados: " . $e->getMessage());
        } finally {
            $conn = null;
        }
    }

    public static function getById($num)
    {
        $conn = new DbConnect();
        $_response = new Response();
        $sql = $conn->prepare('SELECT * FROM ' . self::TABLA . ' WHERE id = :id');
        $sql->bindParam(':id', $num);
        try {
            $sql->execute();
            $response = $sql->fetch(PDO::FETCH_ASSOC);
            if (!$response) {return $_response->message_400("Registro NO encontrado");}
            return $_response->message_200($response);
        } catch (PDOException $e) {
            return $_response->message_400("Registro NO encontrado: " . $e->getMessage());
        } finally {
            $conn = null;
        }
    }

    public static function save($param)
    {
        $conn = new DbConnect();
        $_response = new Response();
        $paramRequired = isset($param['pago']) ? true : false;
        $paramRequired = isset($param['monto']) ? true : false;
        $paramRequired = isset($param['fecha']) ? true : false;
        if (!$paramRequired) {return $_response->message_400();}
        $sql = $conn->prepare('INSERT INTO ' . self::TABLA . ' (idmovimiento, pago, monto, fecha) VALUES(:idmovimiento, :pago, :monto, :fecha)');
        $sql->bindParam(':idmovimiento', $param['idmovimiento']);
        $sql->bindParam(':pago', $param['pago']);
        $sql->bindParam(':monto', $param['monto']);
        $sql->bindParam(':fecha', $param['fecha']);
        try {
            $sql->execute();
            return $_response->message_201($param);
        } catch (PDOException $e) {
            return $_response->message_400("Registro NO guardado: " . $e->getMessage());
        } finally {
            $conn = null;
        }
    }

    public static function update($num, $param)
    {
        $conn = new DbConnect();
        $_response = new Response();
        $paramOld = self::getById($num);
        if ($paramOld['status_id'] == "200") {
            $param['id'] = $num;
            if (!isset($param['idmovimiento'])) {$param['idmovimiento'] = $paramOld['response']['idmovimiento'];}
            if (!isset($param['pago'])) {$param['pago'] = $paramOld['response']['pago'];}
            if (!isset($param['monto'])) {$param['monto'] = $paramOld['response']['monto'];}
            if (!isset($param['fecha'])) {$param['fecha'] = $paramOld['response']['fecha'];}
            $sql = $conn->prepare('UPDATE  ' . self::TABLA . ' SET pago = :nombre, pago = :pago, monto = :monto, fecha = :fecha WHERE id = :id');
            $sql->bindParam(':id', $param['id']);
            $sql->bindParam(':idmovimiento', $param['idmovimiento']);
            $sql->bindParam(':pago', $param['pago']);
            $sql->bindParam(':monto', $param['monto']);
            $sql->bindParam(':fecha', $param['fecha']);
            try {
                $sql->execute();
                return $_response->message_201($param);
            } catch (PDOException $e) {
                return $_response->message_400("Registro NO encontrado: " . $e->getMessage());
            } finally {
                $conn = null;
            }
        } else {
            return $paramOld;
        }
    }

    public static function delete($num)
    {
        $conn = new DbConnect();
        $_response = new Response();
        $sql = $conn->prepare('DELETE FROM ' . self::TABLA . ' WHERE id = :id');
        $sql->bindParam(':id', $num);
        try {
            $sql->execute();
            return $_response->message_200('Registro eliminado.');
        } catch (PDOException $e) {
            return $_response->message_400("Registro NO eliminado: " . $e->getMessage());
        } finally {
            $conn = null;
        }
    }

    private static function existsId($num)
    {
        $conn = new DbConnect();
        $_response = new Response();
        $sql = $conn->prepare('SELECT id FROM ' . self::TABLA . ' WHERE id = :id LIMIT 1');
        $sql->bindParam(':id', $num);
        try {
            $sql->execute();
            return $sql->rowCount();
        } catch (PDOException $e) {
            return $_response->message_400("Registro NO encontrado: " . $e->getMessage());
        } finally {
            $conn = null;
        }
    }

}

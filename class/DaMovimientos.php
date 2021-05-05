<?php
 require_once 'connect/DbConnect.php';
 require_once '../helpers/response.php'; 

 class DaMovimientos{
    const TABLA = 'movimientos';

   public static function getAll(){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' ORDER BY id');
      try {
         $sql->execute();   
         return $_response->message_200($sql->fetchAll(PDO::FETCH_ASSOC));
      } catch (PDOException $e) {
         return $_response->message_400("Registros NO recuperados: " . $e->getMessage());
      } finally{
         $conn = null;
      }
   }

   public static function getById($num){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' WHERE id = :id');
      $sql->bindParam(':id', $num);
      try {
         $sql->execute();
         $response = $sql->fetch(PDO::FETCH_ASSOC);
         if(!$response){return $_response->message_400("Registro NO encontrado");}
         return $_response->message_200($response);
      } catch (PDOException $e) {
         return $_response->message_400("Registro NO encontrado: " . $e->getMessage());
      }finally{
         $conn = null;
      }
   }

   public static function save($param){
      $conn = new DbConnect();
      $_response = new Response();
      $response = self::existsId($param['id']);
      if($response == 0){
         $paramRequired = isset($param['dnicliente'])? true : false;
         $paramRequired = isset($param['dniusuario'])? true : false;
         $paramRequired = isset($param['serial'])? true : false;
         $paramRequired = isset($param['fecha'])? true : false;
         $paramRequired = isset($param['operacion'])? true : false;
         $paramRequired = isset($param['estado'])? true : false;
         if(!$paramRequired){return $_response->message_400();}
         $sql = $conn->prepare('INSERT INTO ' . self::TABLA .' (id, dnicliente, dniusuario, serial, fecha, operacion, estado) VALUES(:id, :dnicliente, :dniusuario, :serial, :fecha, :operacion, :estado)');
         $sql->bindParam(':id', $param['id']);
         $sql->bindParam(':dnicliente', $param['dnicliente']);
         $sql->bindParam(':dniusuario', $param['dniusuario']);
         $sql->bindParam(':serial', $param['serial']);
         $sql->bindParam(':fecha', $param['fecha']);
         $sql->bindParam(':operacion', $param['operacion']);
         $sql->bindParam(':estado', $param['estado']);
         try {
            $sql->execute();
            return $_response->message_201($param);
         } catch (PDOException $e) {
            return $_response->message_400("Registro NO guardado: " . $e->getMessage());
         }finally{
            $conn = null;
         }
      }else{
         return $response;
      }
   }

   public static function update($num, $param){
      $conn = new DbConnect();
      $_response = new Response();
      $paramOld = self::getById($num);
      if($paramOld['status_id'] == "200"){
         $param['id'] = $num;
         if(!isset($param['dnicliente'])){$param['dnicliente'] = $paramOld['response']['dnicliente'];}
         if(!isset($param['dniusuario'])){$param['dniusuario'] = $paramOld['response']['dniusuario'];}
         if(!isset($param['serial'])){$param['serial'] = $paramOld['response']['serial'];}
         if(!isset($param['fecha'])){$param['fecha'] = $paramOld['response']['fecha'];}
         if(!isset($param['operacion'])){$param['operacion'] = $paramOld['response']['operacion'];}
         if(!isset($param['estado'])){$param['estado'] = $paramOld['response']['estado'];}
         $sql = $conn->prepare('UPDATE  ' . self::TABLA .' SET dnicliente = :dnicliente, dniusuario = :dniusuario, serial = :serial, fecha = :fecha, operacion = :operacion, estado = :estado WHERE id = :id');
         $sql->bindParam(':id', $param['id']);
         $sql->bindParam(':dnicliente', $param['dnicliente']);
         $sql->bindParam(':dniusuario', $param['dniusuario']);
         $sql->bindParam(':serial', $param['serial']);
         $sql->bindParam(':fecha', $param['fecha']);
         $sql->bindParam(':operacion', $param['operacion']);
         $sql->bindParam(':estado', $param['estado']);
         try {
            $sql->execute();
            return $_response->message_201($param);
         } catch (PDOException $e) {
            return $_response->message_400("Registro NO encontrado: " . $e->getMessage());
         }finally{
            $conn = null;
         }
      }else{
         return $paramOld;
      }
   }

   public static function delete($num){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('DELETE FROM ' . self::TABLA .' WHERE id = :id');
      $sql->bindParam(':id', $num);
      try {
         $sql->execute();
         return $_response->message_200('Registro eliminado.');
      } catch (PDOException $e) {
         return $_response->message_400("Registro NO eliminado: ". $e->getMessage()); 
      } finally{
         $conn = null;
      }
   }

   private static function existsId($num){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT id FROM ' . self::TABLA .' WHERE id = :id LIMIT 1');
      $sql->bindParam(':id', $num);
      try {
         $sql->execute();
         return $sql->rowCount();
      } catch (PDOException $e) {
         return $_response->message_400("Registro NO encontrado: " . $e->getMessage());
      }finally{
         $conn = null;
      }
   }

 }
?>
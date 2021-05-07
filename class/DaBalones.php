<?php
 require_once 'connect/DbConnect.php';
 require_once '../helpers/response.php'; 

 class DaBalones{
    const TABLA = 'balones';

   public static function getAll(){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' ORDER BY serial');
      try {
         $sql->execute();   
         return $_response->message_200($sql->fetchAll(PDO::FETCH_ASSOC));
      } catch (PDOException $e) {
         return $_response->message_400("Registros NO recuperados: " . $e->getMessage());
      } finally{
         $conn = null;
      }
   }

   public static function getSend(){
      $conn = new DbConnect();
      $_response = new Response();
      //SELECT `balones`.`serial`, `clientes`.`dni`, `clientes`.`nombre`, `balones`.`estado`  FROM `balones` JOIN `movimientos` ON `balones`.`operacion` = "recepcion" AND `movimientos`.`serial` = `balones`.`serial` JOIN `clientes` ON `clientes`.`dni` = `movimientos`.`dniclientes`
      $sql = $conn->prepare('SELECT balones.serial, clientes.dni, clientes.nombre, balones.estado FROM ' . self::TABLA .' JOIN movimientos ON balones.operacion = "recepcion" AND movimientos.serial = balones.serial JOIN clientes ON clientes.dni = movimientos.dniclientes');
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
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' WHERE serial = :serial');
      $sql->bindParam(':serial', $num);
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
      $response = self::existsId($param['serial']);
      if($response == 0){
         $paramRequired = isset($param['capacidad'])? true : false;
         $paramRequired = isset($param['tulipa'])? true : false;
         $paramRequired = isset($param['marca'])? true : false;
         $paramRequired = isset($param['estado'])? true : false;
         $paramRequired = isset($param['operacion'])? true : false;
         if(!$paramRequired){return $_response->message_400();}
         $sql = $conn->prepare('INSERT INTO ' . self::TABLA .' (serial, capacidad, tulipa, marca, estado, operacion) VALUES(:serial, :capacidad, :tulipa, :marca, :estado, :operacion)');
         $sql->bindParam(':serial', $param['serial']);
         $sql->bindParam(':capacidad', $param['capacidad']);
         $sql->bindParam(':tulipa', $param['tulipa']);
         $sql->bindParam(':marca', $param['marca']);
         $sql->bindParam(':estado', $param['estado']);
         $sql->bindParam(':operacion', $param['operacion']);
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
         $param['serial'] = $num;
         if(!isset($param['capacidad'])){$param['capacidad'] = $paramOld['response']['capacidad'];}
         if(!isset($param['tulipa'])){$param['tulipa'] = $paramOld['response']['tulipa'];}
         if(!isset($param['marca'])){$param['marca'] = $paramOld['response']['marca'];}
         if(!isset($param['estado'])){$param['estado'] = $paramOld['response']['estado'];}
         if(!isset($param['operacion'])){$param['operacion'] = $paramOld['response']['operacion'];}
         $sql = $conn->prepare('UPDATE  ' . self::TABLA .' SET capacidad = :capacidad, tulipa = :tulipa, marca = :marca, estado = :estado, operacion = :operacion WHERE serial = :serial');
         $sql->bindParam(':serial', $param['serial']);
         $sql->bindParam(':capacidad', $param['capacidad']);
         $sql->bindParam(':tulipa', $param['tulipa']);
         $sql->bindParam(':marca', $param['marca']);
         $sql->bindParam(':estado', $param['estado']);
         $sql->bindParam(':operacion', $param['operacion']);
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
      $sql = $conn->prepare('DELETE FROM ' . self::TABLA .' WHERE serial = :serial');
      $sql->bindParam(':serial', $num);
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
      $sql = $conn->prepare('SELECT serial FROM ' . self::TABLA .' WHERE serial = :serial LIMIT 1');
      $sql->bindParam(':serial', $num);
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
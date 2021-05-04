<?php
 require_once 'connect/DbConnect.php';
 require_once '../helpers/response.php'; 

 class DaClientes{
    const TABLA = 'clientes';

   public static function getAll(){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' ORDER BY DNI');
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
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' WHERE DNI = :dni');
      $sql->bindParam(':dni', $num);
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
      $response = self::existsId($param['dni']);
      if($response == 0){
         $paramRequired = isset($param['nombre'])? true : false;
         $paramRequired = isset($param['telefono'])? true : false;
         $paramRequired = isset($param['autorizador'])? true : false;
         if(!$paramRequired){return $_response->message_400();}
         $sql = $conn->prepare('INSERT INTO ' . self::TABLA .' (DNI, nombre, telefono, autorizador) VALUES(:dni, :nombre, :telefono, :autorizador)');
         $sql->bindParam(':dni', $param['dni']);
         $sql->bindParam(':nombre', $param['nombre']);
         $sql->bindParam(':telefono', $param['telefono']);
         $sql->bindParam(':autorizador', $param['autorizador']);
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
         $param['dni'] = $num;
         if(!isset($param['nombre'])){$param['nombre'] = $paramOld['response']['nombre'];}
         if(!isset($param['telefono'])){$param['telefono'] = $paramOld['response']['telefono'];}
         if(!isset($param['autorizador'])){$param['autorizador'] = $paramOld['response']['autorizador'];}
         $sql = $conn->prepare('UPDATE  ' . self::TABLA .' SET nombre = :nombre, telefono = :telefono, autorizador = :autorizador WHERE DNI = :dni');
         $sql->bindParam(':dni', $param['dni']);
         $sql->bindParam(':nombre', $param['nombre']);
         $sql->bindParam(':telefono', $param['telefono']);
         $sql->bindParam(':autorizador', $param['autorizador']);
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
      $sql = $conn->prepare('DELETE FROM ' . self::TABLA .' WHERE DNI = :dni');
      $sql->bindParam(':dni', $num);
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
      $sql = $conn->prepare('SELECT DNI FROM ' . self::TABLA .' WHERE DNI = :dni LIMIT 1');
      $sql->bindParam(':dni', $dni);
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
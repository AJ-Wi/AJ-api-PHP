<?php
 require_once 'connect/DbConnect.php';
 require_once '../helpers/response.php'; 

 class DaUsuarios{
    const TABLA = 'usuarios';

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
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' WHERE dni = :dni');
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

   public static function getByUser($user){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT * FROM ' . self::TABLA .' WHERE usuario = :usuario');
      $sql->bindParam(':usuario', $user);
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

   public static function getByToken($value){
      $conn = new DbConnect();
      $_response = new Response();
      $sql = $conn->prepare('SELECT token FROM ' . self::TABLA .' WHERE token = :token');
      $sql->bindParam(':token', $value);
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
         $paramRequired = isset($param['usuario'])? true : false;
         $paramRequired = isset($param['password'])? true : false;
         $paramRequired = isset($param['privilegio'])? true : false;
         $paramRequired = isset($param['estado'])? true : false;
         if(!$paramRequired){return $_response->message_400();}
         $sql = $conn->prepare('INSERT INTO ' . self::TABLA .' (dni, nombre, usuario, password, privilegio, estado) VALUES(:dni, :nombre, :usuario, :password, :privilegio, :estado)');
         $sql->bindParam(':dni', $param['dni']);
         $sql->bindParam(':nombre', $param['nombre']);
         $sql->bindParam(':usuario', $param['usuario']);
         $sql->bindParam(':password', DbConnect::encrypt($param['password']));
         $sql->bindParam(':privilegio', $param['privilegio']);
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
         $param['dni'] = $num;
         if(!isset($param['nombre'])){$param['nombre'] = $paramOld['response']['nombre'];}
         if(!isset($param['usuario'])){$param['usuario'] = $paramOld['response']['usuario'];}
         if(!isset($param['password'])){$param['password'] = $paramOld['response']['password'];}else{$param['password'] = DbConnect::encrypt($param['password']);}
         if(!isset($param['privilegio'])){$param['privilegio'] = $paramOld['response']['privilegio'];}
         if(!isset($param['estado'])){$param['estado'] = $paramOld['response']['estado'];}
         $sql = $conn->prepare('UPDATE  ' . self::TABLA .' SET nombre = :nombre, usuario = :usuario, password = :password, privilegio = :privilegio, estado = :estado WHERE dni = :dni');
         $sql->bindParam(':dni', $param['dni']);
         $sql->bindParam(':nombre', $param['nombre']);
         $sql->bindParam(':usuario', $param['usuario']);
         $sql->bindParam(':password', $param['password']);
         $sql->bindParam(':privilegio', $param['privilegio']);
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

   public static function updateToken($num, $param){
      $conn = new DbConnect();
      $_response = new Response();
      $paramOld = self::getById($num);
      if($paramOld['status_id'] == "200"){
         $sql = $conn->prepare('UPDATE  ' . self::TABLA .' SET token = :token, fecha = :fecha WHERE dni = :dni');
         $sql->bindParam(':dni', $num);
         $sql->bindParam(':token', $param['token']);
         $sql->bindParam(':fecha', $param['date']);
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
      $sql = $conn->prepare('DELETE FROM ' . self::TABLA .' WHERE dni = :dni');
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
      $sql = $conn->prepare('SELECT dni FROM ' . self::TABLA .' WHERE dni = :dni LIMIT 1');
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
<?php
 require_once 'connect/DbConnect.php';
 require_once '../helpers/response.php';
 class DaBalones{
   private $serial;
   private $capacidad;
   private $tulipa;
   private $marca;
   private $estado;
   private $operacion;
   const TABLA = 'balones';
   public function getSerial() {
      return $this->serial;
   }
   public function getCapacidad() {
      return $this->capacidad;
   }
   public function getTulipa() {
      return $this->tulipa;
   }
   public function getMarca() {
      return $this->marca;
   }
   public function getEstado() {
      return $this->estado;
   }
   public function getOperacion() {
      return $this->operacion;
   }
   public function setCapacidad($capacidad) {
      $this->capacidad = $capacidad;
   }
   public function setTulipa($tulipa) {
      $this->tulipa = $tulipa;
   }
   public function setMarca($marca) {
      $this->marca = $marca;
   }
   public function setEstado($estado) {
      $this->estado = $estado;
   }
   public function setOperacion($operacion) {
      $this->operacion = $operacion;
   }
   public function __construct($serial, $capacidad, $tulipa, $marca, $estado, $operacion) {
      $this->serial = $serial;
      $this->capacidad = $capacidad;
      $this->tulipa = $tulipa;
      $this->marca = $marca;
      $this->estado = $estado;
      $this->operacion = $operacion;
   }
   public function guardar(){  
      $conexion = new DbConnect();
      $_respuestas = new respuestas();
      if(!$this->existeSerial($this->serial)){
         $sql = /*GUARDAR*/ $conexion->prepare('INSERT INTO ' . self::TABLA .' (serial, capacidad, tulipa, marca, estado, operacion) VALUES(:serial, :capacidad, :tulipa, :marca, :estado, :operacion)');
         $response = $_respuestas->error_200('Los datos fueron guardados correctamente en la base de datos.'); 
      }else{
         $sql = /*ACTUALIZAR*/ $conexion->prepare('UPDATE  ' . self::TABLA .' SET serial = :serial, capacidad = :capacidad, tulipa = :tulipa, marca = :marca, estado = :estado, operacion = :operacion WHERE serial = :serial');
         $response = $_respuestas->error_200('Los datos ingresados ya se encontraban en la base de datos, sin embargo el registro fue actualizadoo con los nuevos datos.');   
      }
      $sql->bindParam(':serial', $this->serial);
      $sql->bindParam(':capacidad', $this->capacidad);
      $sql->bindParam(':tulipa', $this->tulipa);
      $sql->bindParam(':marca', $this->marca);
      $sql->bindParam(':estado', $this->estado);
      $sql->bindParam(':operacion', $this->operacion);
      $sql->execute();
      $conexion = null;
      return $response;
   }

   //comprobar que el balon exista o no en la base de datos
   private function existeSerial($num){
      $conexion = new DbConnect();
      $sql = $conexion->prepare('SELECT serial FROM ' . self::TABLA .' WHERE serial = :serial LIMIT 1');
      $sql->bindParam(':serial', $num);
      $sql->execute();
      $count = $sql->rowCount();
      $conexion = null;
      return $count;
   }

   public static function buscarPorSerial($num){
      $conexion = new DbConnect();
      $_respuestas = new respuestas();
      $sql = $conexion->prepare('SELECT * FROM ' . self::TABLA .' WHERE serial = :serial');
      $sql->bindParam(':serial', $num);
      $sql->execute();
      $registro = $sql->fetch(PDO::FETCH_ASSOC);
      $conexion = null;
      return ($registro)?$registro:$_respuestas->error_400();
   }

   public static function recuperarTodos(){
      $conexion = new DbConnect();
      $_respuestas = new respuestas();
      $sql = $conexion->prepare('SELECT * FROM ' . self::TABLA .' ORDER BY serial');
      $sql->execute();
      $registro = $sql->fetchAll(PDO::FETCH_ASSOC);
      $conexion = null;
      return ($registro)?$registro:$_respuestas->error_400();
   }

   public static function eliminar($num){
      $conexion = new DbConnect();
      $_respuestas = new respuestas();
      $sql = $conexion->prepare('DELETE FROM ' . self::TABLA .' WHERE serial = :serial');
      $sql->bindParam(':serial', $num);
      $sql->execute();
      $conexion = null;
      return $_respuestas->error_200('Los datos fueron Eliminados correctamente en la base de datos.');
   }

 }
?>
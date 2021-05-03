<?php 
 class DbConnect extends PDO { 
    
   public function __construct() {
      //Recupero la configuracion de la conexion.
      include_once dirname(__FILE__) . '/config.php';
      //Sobreescribo el método constructor de la clase PDO.
      try{
         parent::__construct(DB_TYPE . ":dbname=" . DB_NAME . ";host=" . DB_HOST . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
      }catch(PDOException $e){
         echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
         exit;
      }
   } 

   public static function encriptar ($cadena){
      return password_hash($cadena, PASSWORD_DEFAULT);
   }

 } 
?>
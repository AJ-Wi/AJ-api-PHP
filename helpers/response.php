<?php 
class Response{

    public  $response = [
        'status' => "ok",
        "status_id" => "200",
        "response" => ""
    ];

    public function message_200($value){
        $this->response['status_id'] = "200";
        $this->response['status'] = "Ok";
        $this->response['response'] = $value;
        return $this->response;
    }

    public function message_201($value){
        $this->response['status_id'] = "201";
        $this->response['status'] = "El registro se ha guardado con exito.";
        $this->response['response'] = $value;
        return $this->response;
    }

    public function message_400($value = "Datos enviados incompletos o con formato incorrecto"){
        $this->response['status_id'] = "400";
        $this->response['status'] = "error";
        $this->response['response'] = $value;
        return $this->response;
    }

    public function message_401($value = "No autorizado"){
        $this->response['status_id'] = "401";
        $this->response['status'] = "error";
        $this->response['response'] = $value;
        return $this->response;
    }

    public function message_405(){
        $this->response['status_id'] = "405";
        $this->response['status'] = "Ok";
        $this->response['response'] = "Metodo no permitido";
        return $this->response;
    }

    public function message_500($value = "Error interno del servidor"){
        $this->response['status_id'] = "500";
        $this->response['status'] = "error";
        $this->response['response'] = $value;
        return $this->response;
    }   
}

?>
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

require_once '../libs/Slim/Slim.php';

/**
 *Formatea y manda la respuesta al cliente.
 *
 * @package AJ-dev-api
 * @author  wladimir perez
 * @since   0.1.0
 *
 */

class Response
{

    /**
     * Mostrando la respuesta en formato json al cliente o navegador
     */
    public static function echoResponse($response)
    {
        $app = \Slim\Slim::getInstance();
        // setting response content type to json
        $app->contentType('application/json');
        // Http response code
        $app->status($response["status_id"]);
        http_response_code($response["status_id"]);
        // encoding response to json
        echo json_encode($response);
    }

    protected static $messages = array(
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        226 => '226 IM Used',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        426 => '426 Upgrade Required',
        428 => '428 Precondition Required',
        429 => '429 Too Many Requests',
        431 => '431 Request Header Fields Too Large',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported',
        506 => '506 Variant Also Negotiates',
        510 => '510 Not Extended',
        511 => '511 Network Authentication Required',
    );

    public static function message_200($value)
    {
        $response['status_id'] = "200";
        $response['status'] = "Ok";
        $response['response'] = $value;
        return $response;
    }

    public static function message_201($value)
    {
        $response['status_id'] = "201";
        $response['status'] = "Ok";
        $response['response'] = $value;
        return $response;
    }

    public static function message_202($value)
    {
        $response['status_id'] = "202";
        $response['status'] = "Ok";
        $response['response'] = $value;
        return $response;
    }

    public static function message_400($value = "Datos enviados incompletos o con formato incorrecto")
    {
        $response['status_id'] = "400";
        $response['status'] = "error";
        $response['response'] = $value;
        return $response;
    }

    public static function message_401($value = "Unauthorized")
    {
        $response['status_id'] = "401";
        $response['status'] = "error";
        $response['response'] = $value;
        return $response;
    }

    public static function message_405()
    {
        $response['status_id'] = "405";
        $response['status'] = "Ok";
        $response['response'] = "Method Not Allowed";
        return $response;
    }

    public static function message_500($value = "Internal Server Error")
    {
        $response['status_id'] = "500";
        $response['status'] = "error";
        $response['response'] = $value;
        return $response;
    }

}

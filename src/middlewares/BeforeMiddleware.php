<?php
namespace App\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use \Firebase\JWT\JWT;

class BeforeMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {    
        /**
         * VALIDAR JWT
         */
        //obtengo headers
        $headers = getallheaders();
        //verifico token JWT
        $token = $headers['token'] ?? '';
        $key = "example_key";
        $verifica = false;
        if ($token == '') {
            // $response = new Lresponse();
            $rta['data'] = 'error , token incorrecto';
        }
        else{
            try {
                //code...
                $decoded = JWT::decode($token, $key, array('HS256'));
                $rta['status'] = 'succes';
                $verifica = true;
                // print_r($decoded);
            } catch (\Throwable $th) {
                //throw $th;
                $rta['data'] = 'error al decodificar token';
            }
        }
        if($verifica){
            $response = $handler->handle($request);
            $existingContent = (string) $response->getBody();
            $resp = new Response();
            $resp->getBody()->write($existingContent);
            return $resp;
        }   
        else{
            $response = new Response();
            throw new \Slim\Exception\HttpForbiddenException($request);
            return $response->withStatus(403);
        }
    }
}

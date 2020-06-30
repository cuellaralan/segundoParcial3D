<?php
//slim framework
use Slim\Factory\AppFactory;
// use Illuminate\Database\Schema\Builder as Schema;
use Config\Capsula;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
// require './config/capsule.php';
// require '../models/usuarios.php';

//capsula
new Capsula();

$app = AppFactory::create();
$app->setBasePath('/segundoparcial/public');

// $errorMiddleware = $app->addErrorMiddleware(true, true, true);

// // Set the Not Found Handler
// // $errorMiddleware->setErrorHandler(
// //     HttpNotFoundException::class,
// //     function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
// //         $response = new Response();
// //         $response->getBody()->write('404 NOT FOUND');

// //         return $response->withStatus(404);
// //     });

// // Set the Not Allowed Handler
// $errorMiddleware->setErrorHandler(
//     HttpMethodNotAllowedException::class,
//     function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
//         $response = new Response();
//         $response->getBody()->write('405 NOT ALLOWED');

//         return $response->withStatus(405);
//     });

//registramos rutas
(require_once __DIR__ .'/../config/routes.php')($app);

//REGISTRAR MIDDLEWARES
(require_once __DIR__ .'/../config/middlewares.php')($app);

//retornamos server
return $app;
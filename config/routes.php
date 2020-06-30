<?php
//illuminate
// use Illuminate\Database\Capsule\Manager as Capsule;
//prxy
use Slim\Routing\RouteCollectorProxy;
//class
use App\controllers\UsuarioController;
//MIDDLEWARES
use App\middlewares\BeforeMiddleware;
//rutas
// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;

return function($app)
{
    $app->post('/registro', UsuarioController::class.':logup');
    $app->post('/login', UsuarioController::class.':login');
    $app->post('/tipo_mascota', UsuarioController::class.':addType');
    $app->post('/mascota', UsuarioController::class . ':addPet');

    $app->group('/turnos', function(RouteCollectorProxy $group){
        $group->post('/mascota', UsuarioController::class . ':addTurn');
        $group->get('/[id]', UsuarioController::class . ':getTurns');
        $group->post('/mascota/[id_mascota]', UsuarioController::class . ':addTurn');
    });
    // $app->group('/usuarios', function(RouteCollectorProxy $group){
    //     // $group->get('[/]', UsuarioController::class . ':getAll')->add(new BeforeMiddleware());
    //     $group->post('/time', UsuarioController::class . ':addTime');
    //     // $group->post('/registro', UsuarioController::class . ':logup');
    //     // $group->post('/login', UsuarioController::class . ':login');
    //     $group->post('/mascota', UsuarioController::class . ':addPet')->add(new BeforeMiddleware());
    //     $group->post('/turnos/mascota', UsuarioController::class . ':addTurn');
    //     $group->get('/turnos/[id]', UsuarioController::class . ':getTurns');
    //     $group->get('/mascota/[id_mascota]', UsuarioController::class . ':addPet')->add(new BeforeMiddleware());
    //     // $group->get('/turnos{id_mascota}', UsuarioController::class . ':login');

    // });



};

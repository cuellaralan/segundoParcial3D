<?php

use Slim\App;
use App\middlewares\BeforeMiddleware;
use App\middlewares\AfterMiddleware;

return function(App $app){
    // $app->addBodyParsingMiddleware();

    // $app->add(new BeforeMiddleware());
    $app->add(new AfterMiddleware());
};

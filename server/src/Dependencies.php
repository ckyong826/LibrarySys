<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // PDO
    $container->set(\PDO::class, function() use ($container) {
        $c = $container->get('settings')['db'];
        $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['dbname']};charset={$c['charset']}";
        return new \PDO($dsn, $c['user'], $c['pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);
    });

    // Controllers
    $container->set(\App\Controllers\BookController::class, function($c) {
        return new \App\Controllers\BookController($c->get(\PDO::class));
    });

    $container->set(\App\Controllers\AuthorController::class, function($c) {
        return new \App\Controllers\AuthorController($c->get(\PDO::class));
    });

    $container->set(\App\Controllers\LoanController::class, function($c) {
        return new \App\Controllers\LoanController($c->get(\PDO::class));
    });

    $container->set(\App\Controllers\AuthController::class, function($c) {
        return new \App\Controllers\AuthController($c->get(\PDO::class));
    });

    $container->set(\App\Middleware\AuthMiddleware::class, function($c) {
        return new \App\Middleware\AuthMiddleware($c->get(\PDO::class));
    });
};

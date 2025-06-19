<?php

use Slim\App;

return function (App $app) {
  // PUBLIC ROUTES (no authentication required)
  $app->post('/api/auth/register', \App\Controllers\AuthController::class . ':register');
  $app->post('/api/auth/login', \App\Controllers\AuthController::class . ':login');

  // PROTECTED ROUTES (authentication required)
  $app->group('/api', function ($group) {
    // AUTH ROUTES
    $group->get('/auth/me', \App\Controllers\AuthController::class . ':me');
    $group->post('/auth/logout', \App\Controllers\AuthController::class . ':logout');

    // BOOKS
    $group->get('/books', \App\Controllers\BookController::class . ':index');
    $group->get('/books/{id}', \App\Controllers\BookController::class . ':show');
    $group->post('/books', \App\Controllers\BookController::class . ':create');
    $group->put('/books/{id}', \App\Controllers\BookController::class . ':update');
    $group->delete('/books/{id}', \App\Controllers\BookController::class . ':delete');

    // AUTHORS
    $group->get('/authors', \App\Controllers\AuthorController::class . ':index');
    $group->get('/authors/{id}', \App\Controllers\AuthorController::class . ':show');
    $group->post('/authors', \App\Controllers\AuthorController::class . ':create');
    $group->put('/authors/{id}', \App\Controllers\AuthorController::class . ':update');
    $group->delete('/authors/{id}', \App\Controllers\AuthorController::class . ':delete');

    // LOANS
    $group->get('/loans', \App\Controllers\LoanController::class . ':index');
    $group->get('/loans/{id}', \App\Controllers\LoanController::class . ':show');
    $group->post('/loans', \App\Controllers\LoanController::class . ':create');
    $group->put('/loans/{id}', \App\Controllers\LoanController::class . ':update');
    $group->delete('/loans/{id}', \App\Controllers\LoanController::class . ':delete');
  })->add(\App\Middleware\AuthMiddleware::class);
};

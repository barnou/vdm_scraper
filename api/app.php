<?php

use \App\Controller\PostController;

date_default_timezone_set("UTC");

require __DIR__ . "/vendor/autoload.php";

$app = new \Slim\App([
    "settings" => [
        "displayErrorDetails" => false
    ]
]);


// require __DIR__ . "/config/dependencies.php";
require __DIR__ . "/config/middleware.php";

$app->get("/", function ($request, $response, $arguments) {
    print "Thank you for testing this API! :-D";
});

$app->get("/posts",  PostController::class . ':all');

$app->get("/post/{id}", PostController::class . ':single');

$app->run();
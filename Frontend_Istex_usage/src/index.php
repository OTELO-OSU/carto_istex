<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require '../vendor/autoload.php';
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

$app->get('/', function (Request $req,Response $responseSlim) {
$loader = new Twig_Loader_Filesystem('istex/frontend/templates');
$twig = new Twig_Environment($loader);
echo $twig->render('accueil.html.twig');

});

$app->run();


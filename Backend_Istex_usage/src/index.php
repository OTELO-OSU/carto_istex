<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \istex\backend\controller\RequestController as RequestApi;
use \istex\backend\controller\CountryController as Country;
use \istex\backend\controller\AuthorController as Author;
use \istex\backend\controller\LaboratoryController as Laboratory;
require '../vendor/autoload.php';
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

$app->post('/getcountrys', function (Request $req,Response $responseSlim) {
    $request = new RequestApi();
	$query  = $req->getparam('query');
	$response = $request->Request_alldoc_querypagebypage($query);
	$country = new Country();
	$sortcountry = $country->get_name($response);   
	$sortcountry=$country->Sort_by_country($sortcountry,$response);
	$responseSlim->withHeader('Content-Type', 'application/json');
   	return json_encode($sortcountry);
   	
});

$app->post('/getlaboratorys', function (Request $req,Response $responseSlim) {
	$request = new RequestApi();
	$query  = $req->getparam('query');
	$response = $request->Request_alldoc_querypagebypage($query);
    $Sortbylaboratory = new Laboratory;
	$laboratory=$Sortbylaboratory->Sort_by_laboratory($response);
	$responseSlim->withHeader('Content-Type', 'application/json');
    return json_encode($laboratory);
});

$app->post('/getauthors', function (Request $req,Response $responseSlim) {
	$request = new RequestApi();
	$query  = $req->getparam('query');
	$response = $request->Request_alldoc_querypagebypage($query);
    $Sortbyauthor = new Author;
	$author=$Sortbyauthor->Sort_by_author($response);
	$responseSlim->withHeader('Content-Type', 'application/json');
    return json_encode($author);
});

$app->run();

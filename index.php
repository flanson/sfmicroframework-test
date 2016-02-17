<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 11/02/16
 * Time: 15:00
 */
use App\App;
use Symfony\Component\HttpFoundation\Request;


$loader = require __DIR__.'/vendor/autoload.php';

$kernel = new App('dev', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

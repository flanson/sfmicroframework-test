<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 11/02/16
 * Time: 15:00
 */
use App\App;
use Symfony\Component\ClassLoader\ApcClassLoader;


$loader = require __DIR__.'/vendor/autoload.php';
$loader->set('App\\', __DIR__.'/src');

$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);

$app = new App();
// @TODO add request management in order to get GET parameters
$app->run();

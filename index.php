<?php
/**
 * API for itslit.uk
 *
 * @copyright 2017 Marc Towler (www.marctowler.co.uk)
 * @author Marc Towler <marc@marctowler.co.uk>
 */
namespace API;

include_once('vendor/autoload.php');

use API\Library;

$router = new Library\Router();

$con = '\\API\\Controllers\\' . $router->getController();

$controller = new $con();


echo $controller->{$router->getMethod()}();
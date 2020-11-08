<?php
require_once('../config/bootstrap.php');

$router = App\Component\SymfonyRouter::createYamlRouter(
	ROUTES_PATH,
	'web.yaml'
);
$router->routeRequest();

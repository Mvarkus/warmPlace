<?php
error_reporting(E_ALL);

define('ROOT_PATH', realpath(__DIR__.'/..'));
define('ROUTES_PATH', ROOT_PATH.'/routes');
define('VIEWS_PATH', ROOT_PATH.'/views');
define('VIEWS_CACHE_PATH', ROOT_PATH.'/views/cached_views');
define('CONFIG_PATH', __DIR__);
define('PUBLIC_PATH', ROOT_PATH.'/public');
define('PROPERTY_IMAGES_PATH', ROOT_PATH.'/public/images/property_images');
define('PROPERTY_IMAGES_WEB_PATH', '/images/property_images');

require_once ROOT_PATH.'/vendor/autoload.php';

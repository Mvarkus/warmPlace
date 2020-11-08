<?php

namespace App\Component;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * Class renders response strings
 */
class View
{
	/**
	 * Renders html using Twig template engine
	 *
	 * @param string $filename  file name of desired template to be rendered
	 * @param array  $variables variables to be passed to the template
	 * @return string
	 */
	public static function renderHTML(
    	string  $filename,
    	array   $variables,
    	bool    $cache = false
  	): string {
    	if ($cache) {
        	$twig = new Environment(
  				new FilesystemLoader(VIEWS_PATH),
            	[
                	'cache' => VIEWS_CACHE_PATH
            	]
  			);
      	} else {
          	$twig = new Environment(
  			    new FilesystemLoader(VIEWS_PATH)
  		  	);
      	}
		return $twig->render($filename, $variables);
	}

	/**
	 * Encodes given array to json format
	 *
	 * @param array
	 * @return string
	 */
	public static function renderJSON(array $array): string
	{
		return json_encode($array);
	}
}

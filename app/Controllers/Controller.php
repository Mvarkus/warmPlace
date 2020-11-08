<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 * Base class for controllers.
 */
abstract class Controller
{
	/**
     * @var Symfony\Component\HttpFoundation\Request
     */
	protected $request;
	/**
     * @var Symfony\Component\HttpFoundation\Response
     */
	protected $response;

	/**
	 * Sets protected properties
	 * 
	 * @param Symfony\Component\HttpFoundation\Request $request 
	 * @param Symfony\Component\HttpFoundation\Response $response 
	 */
	public function __construct(
		Request  $request,
		Response $response
	) {
		$this->request  = $request;
		$this->response = $response;
	}

	/**
	 * Sends response to the user usign Symfony\Component\HttpFoundation\Response object.
	 * 
     * @param string $content response body
     * @param array  $headers HTTP headers
	 */
	protected function sendResponse(
		string $content = '',
		array  $headers = []
	) {
		$this->response->setContent($content);

    	foreach ($headers as $headerName => $headerValue) {
    		$this->response->headers->set(
    			$headerName, $headerValue
    		);
    	}

    	$this->response->prepare($this->request);
    	$this->response->send();
		exit;
	}

	/**
	 * Returns namespace of base controller.
	 * Used for determining base folder of all controllers.
	 *
	 * @return string
	 */
    public static function getNamespace()
    {
    	return __NAMESPACE__;
    }

    /**
     * Shows 404 error page.
     *
     * @param Request  $request Symphony request object
     * @param Response $response Symphony response object
     */
    public static function pageNotFound($request, $response)
    {
        $twig = new Environment(
            new FilesystemLoader(VIEWS_PATH),
            [
                'cache' => VIEWS_CACHE_PATH
            ]
        );

        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->setContent($twig->render('404.html.twig'));

    	$response->prepare($request);
    	$response->send();
		exit;
    }

    /**
     * Shows main page of a controller
     */
    public abstract function showMainPage();

	/**
	 * Redirects user to a given url
	 *
	 * @param string $url the url where user should be redicrected
	 */
	protected function redirectTo(string $url)
    {
        $response = new RedirectResponse($url);
        $response->send();
        exit;
    }
}

<?php

namespace App\Component;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Config\Loader\LoaderInterface;
use Psr\Log\LoggerInterface;

use App\Controllers\Controller;

/**
 * The class wraps up basic sympfony router usage.
 */
class SymfonyRouter extends Router
{
    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;
    /**
     * @var Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * Extends parent constructor
     *
     * {@inheritDoc}
     */
    public function __construct(
        LoaderInterface $loader,
                        $resource,
        array           $options = [],
        RequestContext  $context = null,
        LoggerInterface $logger = null,
        Request         $request,
        Response        $response
    ) {
        parent::__construct($loader, $resource, $options, $context, $logger);

        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Finds matched route, creates assigned controllers instance and calls given action.
     */
    public function routeRequest()
    {
        /**
         * If route has been found, variable will contain array:
         * 
         * [
         *     'controller'    =>  "ControllerName::actionName",
         *     'id'            => 3
         * ]
         *
         * If not, it will contain null.
         *
         * @var array|null
         */
        $matchResult = $this->matchRequest($this->request);

        $controller = array_shift($matchResult);

        if ($matchResult === null) {
            \App\Controllers\Controller::pageNotFound($this->request, $this->response);
        }

        list($controllerName, $actionName) = explode('::', $controller);
        $controllerName = Controller::getNamespace().'\\'.$controllerName;

        call_user_func_array(
            [new $controllerName($this->request, $this->response), $actionName],
            $matchResult
        );
    }

    /**
     * Creates symfony router usign yaml routes type.
     *
     * @param  string $routesFilename yaml routes filename
     * @return SymfonyRouter
     */
    public static function createYamlRouter(string $routesLocation, string $routesFilename): SymfonyRouter
    {
        $fileLocator    = new FileLocator($routesLocation);
        $requestContext = new RequestContext();
        $request = Request::createFromGlobals();
        $requestContext->fromRequest($request);

        return new SymfonyRouter(
            new YamlFileLoader($fileLocator),
            $routesFilename,
            [],
            $requestContext,
            null,
            $request,
            new Response()
        );
    }
}

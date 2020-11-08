<?php

namespace App\Controllers\AdminControllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\Controller;
use App\User;

abstract class AdminController extends Controller
{
    public function __construct(
        Request $request,
        Response $response
    ) {
        parent::__construct($request, $response);

        if (!User::IsLogged()) {
            $this->redirectTo('/a05022019pdmi/login');
        }
    }
}

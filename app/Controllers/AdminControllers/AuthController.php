<?php

namespace App\Controllers\AdminControllers;

use App\Component\View;
use App\User;
use App\Controllers\Controller;

class AuthController extends Controller
{
    public function showMainPage()
    {
        if (User::IsLogged()) {
            $this->redirectUser('/a05022019pdmi/create');
        }

    	$this->sendResponse(
            View::renderHtml(
                'admin_panel/login.html.twig',
                [],
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function loginUser()
    {
        if (User::IsLogged()) {
            $this->redirectUser('/a05022019pdmi/create');
        }

        if ($user = User::getUserByUsername($_POST['username'])) {

            if ( password_verify($_POST['pass'], $user['pass']) ) {
                $this->createUserSession($user);

                $this->redirectTo('/a05022019pdmi/create');
            } else {
                $this->sendResponse(
                    View::renderHtml(
                        'admin_panel/login.html.twig',
                        ['message' => 'The details are incorect'],
                        false
                    ),
                    [
                        'Content-Type' => 'text/html'
                    ]
                );
            }
        }
    }

    public function logoutUser()
    {
        session_start();
        session_destroy();

        $this->redirectTo('/a05022019pdmi/login');
    }

    public function createUserSession(array $userData)
    {
        $_SESSION['isLogged'] = true;
        $_SESSION['userId']   = $userData['user_id'];
        $_SESSION['username'] = $userData['username'];
    }
}

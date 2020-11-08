<?php

namespace App\Controllers\AdminControllers;

use App\Component\View;
use App\Controllers\Controller;
use App\Property;
use App\User;

class CreateController extends AdminController
{

    public function showMainPage(array $data = [])
    {
    $types = Property::getPropertyTypes();
        
        $data = array_merge(
            $data,
            [
                'username'   => $_SESSION['username'],
                'todaysDate' => date('Y-m-d', time()),
                'types'      => $types
            ]
        );

        $this->sendResponse(
            View::renderHtml(
                'admin_panel/create.html.twig',
                $data,
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function createProperty()
    {
        $result = Property::createProperty(
            $this->request->request->all(),
            $this->request->files->get('images')
        );

        $this->showMainPage($result);
    }
}

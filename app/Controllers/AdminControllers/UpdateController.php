<?php

namespace App\Controllers\AdminControllers;

use App\Component\View;
use App\Controllers\Controller;
use App\Property;
use App\User;

class UpdateController extends AdminController
{
    public function showMainPage(array $data = [])
    {
        $websiteSettings = require_once CONFIG_PATH.'/website_settings.php';

        $ticketsData = Property::getTicketsData(
            [
                'order-by' => 'newest',
                'type'     => 0,
                'location' => 0,
                'bedrooms' => 0
            ],
            1,
            $websiteSettings['showPerPage'],
            false
        );

        $data = array_merge(
            $data,
            [
                'username'   => $_SESSION['username'],
                'tickets' => $ticketsData['tickets'],
                'success' => $_GET['success'] ?? NULL
            ],
            [
                'showPerPage'  => $websiteSettings['showPerPage'],
                'tickets'      => $ticketsData['tickets'],
                'totalTickets' => $ticketsData['totalAmount']
            ]
        );

        $this->sendResponse(
            View::renderHtml(
                'admin_panel/update.html.twig',
                $data,
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function showUpdateForm(int $id, array $data = [])
    {
        $property = Property::getPropertyById($id, false);
        $types = Property::getPropertyTypes();

        $data = array_merge(
            $data,
            [
                'username'  => $_SESSION['username'],
                'property'  => $property[0],
                'types'     => $types
            ]
        );

        $this->sendResponse(
            View::renderHtml(
                'admin_panel/updateProperty.html.twig',
                $data,
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function updateProperty(int $id)
    {
        $result = Property::updateProperty(
            $id,
            $this->request->request->all(),
            $this->request->files->get('images')
        );

        if ($result['success']) {
            $this->redirectTo('/a05022019pdmi/update?success=true');
        } else {
            $this->redirectTo('/a05022019pdmi/update?success=false');
        }
    }
}

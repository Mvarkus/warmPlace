<?php

namespace App\Controllers\AdminControllers;

use App\Component\View;
use App\Controllers\Controller;
use App\Property;
use App\Image;
use App\User;

class DeleteController extends AdminController
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
                'tickets' => $ticketsData['tickets']
            ],
            [
                'showPerPage'  => $websiteSettings['showPerPage'],
                'tickets'      => $ticketsData['tickets'],
                'totalTickets' => $ticketsData['totalAmount']
            ]
        );

        $this->sendResponse(
            View::renderHtml(
                'admin_panel/delete.html.twig',
                $data,
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function showDeletePage(int $propertyId)
    {
        $property = Property::getPropertyById($propertyId);

        if (!$property) {
            self::showMainPage();
        }

        $this->sendResponse(
            View::renderHtml(
                'admin_panel/deleteProperty.html.twig',
                [
                    'property' => $property[0],
                    'username' => $_SESSION['username']
                ],
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function deleteProperty(int $propertyId)
    {
        $result['success'] = Property::deleteProperty($propertyId);

        if ($result['success']) {
            $this->redirectTo('/a05022019pdmi/delete?success=true');
        } else {
            $this->redirectTo('/a05022019pdmi/delete?success=false');
        }
    }

    public function deleteImageById(int $imageId)
    {
        $result['success'] = Image::removeImageById($imageId);

        if ($result['success']) {
            $this->redirectTo('/a05022019pdmi/delete?success=true');
        } else {
            $this->redirectTo('/a05022019pdmi/delete?success=false');
        }
    }
}

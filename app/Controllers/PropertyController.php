<?php

namespace App\Controllers;

use App\Component\View;
use App\Property;
/**
 * summary
 */
class PropertyController extends Controller
{
    public function showMainPage()
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
            $websiteSettings['showPerPage']
        );

        $data = [
            'showPerPage'  => $websiteSettings['showPerPage'],
            'tickets'      => $ticketsData['tickets'],
            'totalTickets' => $ticketsData['totalAmount']
        ];

	    $this->sendResponse(
            View::renderHtml(
                'index.html.twig',
                $data,
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    public function showProperty(int $id)
    {
        $ticket = Property::getPropertyById($id);

        if ($ticket === null) {
            Controller::pageNotFound($this->request, $this->response);
            exit;
        }

        $this->sendResponse(
            View::renderHtml(
                'single.html.twig',
                [
                    'ticket' => $ticket[0]
                ],
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }

    /**
     * Retrieves tickets from database, fromats $ticketsData array and sends json data to the client
     */
    public function getJsonTicketsData()
    {
        if (!$this->request->isXmlHttpRequest()) {
            Controller::pageNotFound($this->request, $this->response);
            exit;
        } else {
            $websiteSettings = require_once CONFIG_PATH.'/website_settings.php';

            $ticketsData = Property::getTicketsData(
                $_GET['filter'],
                $_GET['page'],
                $websiteSettings['showPerPage']
            );

            $this->sendResponse(
                View::renderJSON(
                    [
                        $ticketsData['tickets'],
                        $ticketsData['totalAmount'],
                        $websiteSettings['showPerPage']
                    ]
                ),
                ['Content-Type', 'application/json']
            );
        }
    }
}

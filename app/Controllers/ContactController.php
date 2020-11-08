<?php

namespace App\Controllers;

use App\Component\View;

/**
 * summary
 */
class ContactController extends Controller
{
    public function showMainPage()
    {
    	$this->sendResponse(
            View::renderHtml(
                'contact.html.twig',
                [],
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }
}

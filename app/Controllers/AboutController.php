<?php

namespace App\Controllers;

use App\Component\View;

/**
 * summary
 */
class AboutController extends Controller
{
    public function showMainPage()
    {
    	$this->sendResponse(
            View::renderHtml(
                'about.html.twig',
                [],
                false
            ),
            [
                'Content-Type' => 'text/html'
            ]
        );
    }
}

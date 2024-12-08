<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $template = 'home';
        $data = [
            
            'title' => ''
        ];
        $output = $this->render($template, $data);
        return $output;
    }
}
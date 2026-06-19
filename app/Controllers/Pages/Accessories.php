<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

class Accessories extends BaseController
{
    public function __construct()
    {
        if (!session()->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }

    public function index()
    {
        return view('pages/accessories', [
            'title' => 'Accessories'
        ]);
    }
}
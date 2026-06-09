<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function index()
    {   
        echo view('auth/header');
        echo view('auth/login');
        echo view('auth/footer');
    }

     
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}
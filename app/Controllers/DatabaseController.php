<?php

namespace App\Controllers;
use App\Models\userManagementModel;
use App\Models\customModel;

class DatabaseController extends BaseController
{
   public function index()
    {
      $db1 = db_connect();
      $model = new customModel($db1);

      $db2 = db_connect('hris');
      $model2 = new customModel($db2);
    }
}
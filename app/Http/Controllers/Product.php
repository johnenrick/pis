<?php

namespace pis_test\Http\Controllers;

use Illuminate\Http\Request;
use pis_test\Product as DBModel;
// use Illuminate\Support\Facades\DB;

class Product extends APIController
{

    function __construct(){
      $this->model = new DBModel();
      $this->requiredChildren = array(
        "unit",
        "category"
      );

    }
}

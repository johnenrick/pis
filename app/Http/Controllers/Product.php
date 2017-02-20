<?php

namespace pis\Http\Controllers;

use Illuminate\Http\Request;
use pis\Product as DBModel;
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

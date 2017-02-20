<?php

namespace pis\Http\Controllers;

use Illuminate\Http\Request;
use pis\Unit as DBModel;

class Unit extends APIController
{
  function __construct(){
    $this->model = new DBModel();
    $this->validation = [
      "code" => "unique:units",
      "description" => "unique:units"
    ];
    $this->defaultValue = array(
      "multiplier" => 1
    );
  }
}

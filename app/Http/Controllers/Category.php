<?php

namespace pis_test\Http\Controllers;

use Illuminate\Http\Request;
use pis_test\Category as Item;

class Category extends APIController
{
  function __construct(){
    $this->model = new Item();
    $this->validation = [
      "code" => "required|unique:categories",
      "description" => "required|unique:categories",
      "expense_mapping" => "required"
    ];
  }
}

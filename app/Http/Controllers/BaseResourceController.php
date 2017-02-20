<?php

namespace pis_test\Http\Controllers;

use Illuminate\Http\Request;

class BaseResourceController extends Controller
{
    protected $filteredFileds = [];
    protected $model = null;


    public function __construct()
    {
        // dd(request());
        // $params = $request->input('condition');
        // foreach($params AS $columnClause => $value){
        //   $arr = explode('__', $columnClause);
        //   $columnName =
        // }
        // $this->filteredFileds = ["test"];
        // // print_r(response()->json($this->model->all()));
        // echo "sadsad";
        // dd($this->model);
        $this->getCondition();


    }
    public function getCondition()
    {
      // $this->model->select(["id, description"]);
      // $this->model->where('ID', 1);
    }
}

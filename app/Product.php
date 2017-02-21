<?php

namespace pis;

use Illuminate\Database\Eloquent\Model;

use DB;

class Product extends APIModel
{
    public function unit(){
      return $this->belongsTo("pis\Unit")->select(["id", "code", "description"]);
    }
    public function category(){
      return $this->belongsTo("pis\Category");
    }

}

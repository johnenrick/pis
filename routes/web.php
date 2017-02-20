<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
$apiResources = [
  "product",
  "video",
  "unit",
  "category"
];
api_resource($apiResources);
function api_resource($apiReource){
  $apiResources = (is_array($apiReource))? $apiReource : [$apiReource];
  foreach($apiResources as $apiResourceValue){
    $pascalCase = preg_replace_callback("/(?:^|-)([a-z])/", function($matches) {
        return strtoupper($matches[1]);
    }, $apiResourceValue);
    Route::get("api/".$apiResourceValue."/",$pascalCase."@index");
    Route::get("api/".$apiResourceValue."/test",$pascalCase."@test");
    Route::get("api/".$apiResourceValue."/create",$pascalCase."@create");
    Route::get("api/".$apiResourceValue."/retrieve",$pascalCase."@retrieve");
    Route::get("api/".$apiResourceValue."/update",$pascalCase."@update");
    Route::get("api/".$apiResourceValue."/delete",$pascalCase."@delete");
  }
}

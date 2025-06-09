<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('angular_test',function(Request $request){
    $test = $request->query('test','default test');
    return response()->json([
        'msg'=>'test is working ! ',
        'test_msg'=>$test
    ]);
});

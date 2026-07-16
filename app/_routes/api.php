<?php

use Classes\Ctrx;
use Classes\Response;
use Classes\Router;

Router::group(
    ["post"=>"user/login"],
    ["post" => "transaction/add"],
    ["get"=> "customer/add"],
    ["post" => "customer/reg"],
)->run(function(){
    Ctrx::throttle(40);
});

Router::group(
    ["post" => "transaction/getupdate"],
    ["get" => "user/add"],
    ["post" => "user/update"],
    ["post" => "transaction/getrevenue"],
    ["post"=> "product/add"]
)->run(function(){
    if(! Ctrx::has_user_data()){
        Response::code(unauthorized_code)->message("User not authorized")->send(unauthorized_code);
    }
});

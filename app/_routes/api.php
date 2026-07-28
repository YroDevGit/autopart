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
    Ctrx::throttle(10);
});

Router::group(
    ["get" => "user/add"],
    ["post" => "photo/upload"],
    ["delete" => "photo/delete"],
    ["post" => "user/update"],
    ["post" => "transaction/getrevenue"],
    ["post"=> "product/add"],
    ["post" => "transaction/pos"],
    ["post"=> "user/add"]
)->run(function(){
    Ctrx::throttle(7);
    use_middleware("admin");
});


Router::group(
    ["post" => "transaction/getupdate"]
)->run(function(){
    use_middleware("admin");
});
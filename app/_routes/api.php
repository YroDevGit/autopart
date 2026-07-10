<?php

use Classes\Ctrx;
use Classes\Response;
use Classes\Router;

Router::group(
    ["post"=>"user/login"],
    ["post" => "transaction/add"],
    ["post" => "cron/notify"]
);

Router::group(
    ["post" => "transaction/getupdate"],
    ["get" => "user/add"],
    ["post" => "user/update"],
    ["post" => "transaction/getrevenue"]
)->run(function(){
    if(! Ctrx::has_user_data()){
        Response::code(unauthorized_code)->message("User not authorized")->send(unauthorized_code);
    }
});

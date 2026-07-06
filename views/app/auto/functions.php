<?php

use Classes\Ctrx;

if(! function_exists("app_name")){
    function app_name(){
        return env("app_name");
    }
}

if(! function_exists("config")){
    function config(string $title){
        $find = \Tables\Configs::findOne(["title"=> $title]);
        if(! $find) return null;
        return $find['string'] ?? null;
    }
}

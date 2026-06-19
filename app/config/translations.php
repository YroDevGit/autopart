<?php 
/**
 * Welcome to Ctrx Translation tools
 * This is a blocker/Middleware for Translation management access
 * use  Ctrx::use_translate_tools();
 */
use Classes\Ccookie;
use Classes\Ctrx;
use Tables\User;

$id = Ccookie::get("user");

$user = User::findOne(["id"=>$id]);
if($user){
    $role = $user['role'];
    if($role == 2){
        Ctrx::use_translate_tools();
    }
}
Ctrx::forbidden_page();

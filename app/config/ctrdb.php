<?php 
use Classes\Ctrx;
use Classes\Ccookie;
use Tables\User;

$id = Ccookie::get("user");

$user = User::findOne(["id"=>$id]);
if($user){
    $role = $user['role'];
    if($role == 2){
        Ctrx::use_database_tools();
    }
}

Ctrx::forbidden_page();
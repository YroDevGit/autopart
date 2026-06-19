<?php
/**
 * This file blocks users to use db tools
 * make a logic to filter users that can access db tool
 * /ctrxtools/db
 * Ctrx::use_db_tools(); // use this to activate db tools
 */

use Classes\Ccookie;
use Classes\Ctrx;
use Tables\User;

$id = Ccookie::get("user");

$user = User::findOne(["id"=>$id]);
if($user){
    $role = $user['role'];
    if($role == 2){
        Ctrx::use_db_tools();
    }
}

Ctrx::forbidden_page();

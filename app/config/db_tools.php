<?php // ctrxtools/db
/**
 * For Database export/import table data
 ** Ctrx::use_db_tools();
 */
use Classes\Ccookie;
use Classes\Ctrx;
use Tables\User;

$id = Ccookie::get("user");

$user = User::findOne(["id"=>$id]);
if($user){
    $role = $user['role'];
    if($role == 1){
        Ctrx::use_db_tools();
    }
}

Ctrx::forbidden_page();

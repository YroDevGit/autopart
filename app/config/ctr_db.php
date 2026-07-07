<?php // ctrxtools/database
/**
 * For General database management
 ** Ctrx::use_database_management();
 */
use Classes\Ctrx;
use Classes\Ccookie;
use Tables\User;

Ctrx::use_database_management();

$id = Ctrx::get_user_data("id");

$user = User::findOne(["id"=>$id]);
if($user){
    $role = $user['role'];
    if($role == 1){
        Ctrx::use_database_management();
    }
}

Ctrx::forbidden_page();
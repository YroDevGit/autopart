<?php

use Classes\Ccookie;
use Tables\User;
$id = Ccookie::get("user");

$user = User::findOne(["id"=>$id]);
if($user){
    $role = $user['role'];
    if($role == 2){
        redirect("cashier/products");exit;
    }else if($role == 3){
      redirect("rider/welcome");exit;
    }
}
?>
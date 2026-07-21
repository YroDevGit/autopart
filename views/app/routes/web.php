<?php

use Tables\User;
use Classes\Ccookie;
use Classes\Page;
use Classes\Ctrx;

Ctrx::role_filtering();

if(Ctrx::has_user_data()){
  $user = User::findOne(["id" => Ctrx::get_user_data("id")]);
  $fullname = $user['fullname'] ?? "USER";
  if(! $user){
    redirect_logout("login");
  }
  gval("fullname", $fullname);
  gval("role", $user['role']?? null);
  gval("rolename", strtoupper(Ctrx::get_user_role() ?? "USER"));
}
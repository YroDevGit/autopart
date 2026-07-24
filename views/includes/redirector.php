<?php

use Classes\Ccookie;
use Classes\Ctrx;
use Tables\User;

$id = Ctrx::get_user_data();
if ($id) {
  $id = $id['id'];
  $user = User::findOne(["id" => $id]);
  if ($user) {
    $role = $user['role'];
    if ($role == 2) {
      redirect("cashier/pos");
      exit;
    } else if ($role == 3) {
      redirect("rider/welcome");
      exit;
    }
  }
}

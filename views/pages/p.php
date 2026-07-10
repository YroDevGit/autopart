<?php

use Tables\User;

$res = User::fuzzy(["fullname"=>"tyrone emz"]);
print_r($res);exit;
//echo $distance = levenshtein($query, $name);

$distance = levenshtein("trone emz", "tyrone lee emz");
echo $distance;
exit;

echo json_encode($res);exit;
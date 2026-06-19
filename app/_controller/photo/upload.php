<?php //route: photo/upload

//Add codes here...

use Classes\CtrStorage;
use Classes\File;
use Classes\Request;
use Classes\Response;
use Tables\Photo;

$file = Request::file('photo');
$name = Request::post("name");

if(! $name){
    Response::code(401)->message("Name is required")->send();
}

if (! File::is_image($file)) {
    Response::code(401)->message("File is not image")->send();
}

$resp = CtrStorage::upload_file($file, "products");
$size = File::size($file, "kb");
$lastUploaded = CtrStorage::get_last_uploaded();
if ($resp) {
    Photo::insert([
        "path" => $resp,
        "alt" => $name,
        "size" => $size,
        "realpath" => $lastUploaded['file'] ?? null
    ]);
}

Response::code(200)->var(['data' => $resp, 'aw'=>$lastUploaded])->send();

<?php //route: photo/delete

//Add codes here...

use Tables\Photo;
use Classes\Response;
use Classes\Request;

$id = Request::post("id");

if(! $id){
    Response::code(404)->message("id is required")->send();
}

$find = Photo::findOne(["id"=> $id]);

if(! $find){
    Response::code(404)->message("Photo not found")->send();
}

if(! isset($find['realpath'])){
    Response::code(404)->message("Unable to remove this photo")->send();
}

Photo::delete($id);

unlink($find['realpath']);

Response::code(200)->message("OK")->send();
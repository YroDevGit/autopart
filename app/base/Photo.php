<?php 
namespace Tables;
use Classes\BaseTable;

class Photo extends BaseTable {
    
    protected $table = "photo";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
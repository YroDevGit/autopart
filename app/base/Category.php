<?php 
namespace Tables;
use Classes\BaseTable;

class Category extends BaseTable {
    
    protected $table = "category";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
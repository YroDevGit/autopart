<?php 
namespace Tables;
use Classes\BaseTable;

class Translations extends BaseTable {
    
    protected $table = "translations";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
<?php 
namespace Tables;
use Classes\BaseTable;

class Address extends BaseTable {
    
    protected $table = "address";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
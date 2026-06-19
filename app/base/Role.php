<?php 
namespace Tables;
use Classes\BaseTable;

class Role extends BaseTable {
    
    protected $table = "role";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
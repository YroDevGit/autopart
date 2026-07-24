<?php 
namespace Tables;
use Classes\BaseTable;

class Configs extends BaseTable {
    
    protected $table = "configs";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
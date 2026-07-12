<?php 
namespace Tables;
use Classes\BaseTable;

class Supplier extends BaseTable {
    
    protected $table = "supplier";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
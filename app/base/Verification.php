<?php 
namespace Tables;
use Classes\BaseTable;

class Verification extends BaseTable {
    
    protected $table = "verification";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
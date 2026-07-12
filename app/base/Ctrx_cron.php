<?php 
namespace Tables;
use Classes\BaseTable;

class Ctrx_cron extends BaseTable {
    
    protected $table = "ctrx_cron";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
<?php 
namespace Tables;
use Classes\BaseTable;

class Ctrx_cron_logs extends BaseTable {
    
    protected $table = "ctrx_cron_logs";

    protected $primaryKey = "id";

    protected $fillable = [];

    protected $guarded = [];

    protected $hidden = [];

    protected $timestamps = false;
}
?>
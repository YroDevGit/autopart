<?php

use Classes\Ccookie;
use Classes\Ctrql;
use Classes\Ctrx;

Ctrql::disable();
Ccookie::delete("user");
Ctrx::delete_user_data();

redirect("/");
?>


<script>
    localStorage.removeItem("userid");
    localStorage.removeItem("orderCart");
</script>
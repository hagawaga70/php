<?php
    $connection = pg_connect ("host=localhost dbname=fahraway user=parallels password=han!37171?");
    if($connection) {
       echo 'connected';
    } else {
        echo 'there has been an error connecting';
    } 
?>

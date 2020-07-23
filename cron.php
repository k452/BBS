<?php
    exec("echo '59 23 * * * /絶対パス/delete_info.php'|crontab",$x,$y);
    print_r($x);
    echo '<br>';
    var_dump($y);  
?>

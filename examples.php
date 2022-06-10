<?php

require 'src/smartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

?>

<pre>
    <?php

       // print_r( $counter->rawStats() );



       // echo $counter->getSq('50', 'hits');

    ?>
</pre>



<?php

require 'src/SmartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

?>

<h1>SmartCounter exapmles</h1>

<p>Total views <?php echo $counter->views() ?> 
of this page <?php echo $counter->views(true) ?>.

<p>Total visits <?php echo $counter->visits() ?> 
of this page <?php echo $counter->visits(true) ?>

<h2>Current statistics data</h2>

<code>
<?php print_r($counter->rawStats()) ?>
</code>
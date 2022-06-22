<?php

require 'src/SmartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

?>

<h1>SmartCounter exapmles</h1>

<h2>Total views and visits</h2>

<p>Views: <?php echo $counter->views() ?>

<p>Visits: <?php echo $counter->visits() ?>

<h2>This page</h2>

<p>Views: <?php echo $counter->views(true) ?>

<p>Visits: <?php echo $counter->visits(true) ?>

<h2>Current statistics data file</h2>

<pre>
<?php print_r($counter->rawStats()) ?>
</pre>
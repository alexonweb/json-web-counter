<?php

require 'src/SmartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

require 'src/SmartCounterView.php';

$counterview = new FriendlyWeb\SmartCounterView();


?>

<h1>SmartCounter exapmles</h1>

<h2>Total views and visits</h2>

<p>Views: <?php echo $counterview->views() ?>

<p>Visits: <?php echo $counterview->visits() ?>

<h2>This page</h2>

<p>Views: <?php echo $counterview->views(true) ?>

<p>Visits: <?php echo $counterview->visits(true) ?>

<h2>Current statistics data file</h2>

<pre>
<?php print_r($counterview->rawStats()) ?>
</pre>
<?php

require 'src/smartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

$counter->getStats();

?>

<h1>Total stats</h1>

<table style="width: 600px;">
    <thead style="background: #fc0">
        <tr>
            <td></td>
            <td>Day</td>
            <td>Month</td>
            <td>Year</td>
            <td>All</td>
        </tr>
    </thead>
    <tr>
        <td>Hosts</td>
        <td><?php echo $counter->getStats('common', 'hosts', 'd') ?></td>
        <td><?php echo $counter->getStats('common', 'hosts', 'm') ?></td>
        <td><?php echo $counter->getStats('common', 'hosts', 'Y') ?></td>
        <td><?php echo $counter->getStats('common', 'hosts', 'all') ?></td>
    </tr>
    <tr>
        <td>Hits</td>
        <td><?php echo $counter->getStats('common', 'hits', 'd') ?></td>
        <td><?php echo $counter->getStats('common', 'hits', 'm') ?></td>
        <td><?php echo $counter->getStats('common', 'hits', 'Y') ?></td>
        <td><?php echo $counter->getStats('common', 'hits', 'all') ?></td>
    </tr>
</table>
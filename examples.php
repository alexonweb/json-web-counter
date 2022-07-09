<?php

require 'src/SmartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

?>

<table border="2" width="400">
<caption>Stats</caption>
    <thead>
        <tr>
            <th>Pages</th>
            <th>Visits</th>
            <th>Views</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <td>This page</td>
        <td><?php echo $counter->visits(true) ?></td>
        <td><?php echo $counter->views(true) ?></td>
    </tr>
    <tr>
        <td>Total</td>
        <td><?php echo $counter->visits() ?></td>
        <td><?php echo $counter->views() ?></td>
    </tr>
    </tbody>
</table>

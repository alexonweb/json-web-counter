<?php

require 'src/SmartCounter.php';
require 'src/SmartCounterView.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count(); // count this page

$counterview = new FriendlyWeb\SmartCounterView();

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
        <td><?php echo $counterview->visits(true) ?></td>
        <td><?php echo $counterview->views(true) ?></td>
    </tr>
    <tr>
        <td>Total</td>
        <td><?php echo $counterview->visits() ?></td>
        <td><?php echo $counterview->views() ?></td>
    </tr>
    </tbody>
</table>
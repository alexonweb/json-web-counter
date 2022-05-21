<?php

require 'src/smartCounter.php';

$counter = new FriendlyWeb\SmartCounter();

$counter->count();

print_r($counter->rawStats());

?>
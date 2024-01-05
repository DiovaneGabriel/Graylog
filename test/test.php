<?php

use DBarbieri\Graylog\Graylog;

require __DIR__ . '/../vendor/autoload.php';

$graylog = new Graylog("http://graylog", 12201);
$return = $graylog->send(["message" => "novo teste"]);

echo '<pre>';
var_dump($return);
die();

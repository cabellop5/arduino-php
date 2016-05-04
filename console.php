<?php

$ACTION = 1;
$KEY = 2;
$VALUE = 3;
header_remove();
require ('Bridge.php');

if (empty($argv[$ACTION])) {
    echo('No action defined');
    exit();
}
$bridge = new Bridge();
if ('get' === $argv[$ACTION] && empty($argv[$KEY])) {
    echo($bridge->{$argv[$ACTION]}());
    exit();
} elseif (strpos($argv[$KEY], ',') !== false && (!empty($argv[$VALUE]) || '0' === $argv[$VALUE]) ) {
    $keys = explode(',', $argv[$KEY]);
    $values = explode(',', $argv[$VALUE]);
    $res = '';
    for($i = 0; $i < count($keys); $i++) {
        $res .= $bridge->{$argv[$ACTION]}($keys[$i], $values[$i]) . ($i === count($keys)-1) ? '' : '_';
    }
    echo($res);
    exit();
} elseif (!empty($argv[$VALUE]) || '0' === $argv[$VALUE]) {
    echo($bridge->{$argv[$ACTION]}($argv[$KEY], $argv[$VALUE]));
    exit();
}
echo($bridge->{$argv[$ACTION]}($argv[$KEY]));
exit();
?>


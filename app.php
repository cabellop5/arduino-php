<?php

require ('Bridge.php');

if (empty($_GET['action'])) {
    echo('No action defined');
    exit();
}
$bridge = new Bridge();
if ('get' === $_GET['action'] && empty($_GET['key'])) {
    echo($bridge->{$_GET['action']}());
    exit();
} elseif (strpos($_GET['key'], ',') !== false && (!empty($_GET['value']) || '0' === $_GET['value']) ) {
    $keys = explode(',', $_GET['key']);
    $values = explode(',', $_GET['value']);
    $res = '';
    for($i = 0; $i < count($keys); $i++) {
        $res .= $bridge->{$_GET['action']}($keys[$i], $values[$i]) . ($i === count($keys)-1) ? '' : '_';
    }
    echo($res);
    exit();
} elseif (!empty($_GET['value']) || '0' === $_GET['value']) {
    echo($bridge->{$_GET['action']}($_GET['key'], $_GET['value']));
    exit();
}
echo($bridge->{$_GET['action']}($_GET['key']));
exit();
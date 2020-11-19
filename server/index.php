<?php
$a = explode('/', $_SERVER['REQUEST_URI']);

print_r($a);
header('Location: //' . $a[1] . '.com');
exit();

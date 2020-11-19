<?php
$args = explode('/', $_SERVER['REQUEST_URI']);

print_r($args);

if ($args[1] == '') {
    redirect('public');
}

function redirect($url)
{
    header('Location: ' . $url);
    exit();
}

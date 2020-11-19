<?php
require_once('./utils.php');

$key = explode('/', $_SERVER['REQUEST_URI'])[1];

if (empty($key)) {
    redirect('public');
} else {
    // 從Shortened找到對應的original並redirect
    // redirect();
}

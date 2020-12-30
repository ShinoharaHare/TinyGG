<?php

require_once './Router.php';
require_once './DAO.php';
require_once './tools.php';
require_once './utils.php';
require_once './api.php';


if (getenv('PRODUCTION') == 1) {
    DAO::connect('klbcedmmqp7w17ik.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306', 'mvluewsqu2srlrlf', 'pe79kuupmu8j5xr9', 'q1xt0bs1902pt7ra');
} else {
    // DAO::connect("localhost", "root", "wayne1224", "test");
    DAO::connect("localhost", "root", "", "TinyGG");
}


$router = new Router;

$router->mount('/api', $api);

$router->get('/', function () {
    redirect('public');
});

$router->get('/:key', function ($key) {
    DAO::query("SELECT url FROM Shortened, Brief WHERE `original` = `ID` AND `key` = '$key'");
    $result = DAO::getResult();
    if (empty($result)) {
        redirect('public');
    } else {
        $url = $result[0]['url'];
        redirect($url);
    }
});

// 輸出結果內容
$r = $router->dispatch(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/');

if ($r) {
    echo $r();
} else {
    print('Page not found');
}
DAO::disconnect();

?>
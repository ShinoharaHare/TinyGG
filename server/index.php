<?php

require_once './Router.php';
require_once './DAO.php';
require_once './tools.php';
require_once './utils.php';

require_once './api.php';


DAO::connect('klbcedmmqp7w17ik.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306', 'mvluewsqu2srlrlf', 'pe79kuupmu8j5xr9', '');

$router = new Router;

$router->mount('/api', $api);

$router->get('/', function () {
    redirect('public');
});

$router->add('/test', function () {
    // insert 一筆Creator資料 -> 取得該筆資料的ID -> 查看ID
    DAO::query(insertCreatorQuery("IV4", "IV6"));
    DAO::query(getLastID());
    echo json_encode(DAO::getResult()) . "<br />";

    // 查看所有Creator的數量
    DAO::query(testQuery());
    echo json_encode(DAO::getResult()) . "<br />";

    return 'TEST' . "<br />";
});

// $router->post('/create', function(){
//     $key = isset($_POST['key']) ? $_POST['key'] : 'none';
//     // print_r(json_decode(file_get_contents('php://input'), true));
//     return "/create, key=$key";
// });

$router->get('/:key', function ($key) {
    // 跳轉對應網址
});

// 輸出結果內容
$r = $router->dispatch(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/');

if ($r) {
    echo $r();
} else {
    die('Page not found');
}
DAO::disconnect();

?>
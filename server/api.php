<?php

require_once './utils.php';

$api = new RouteSet;

$api->get('/', function () {
    echo 'API';
});

// 新建短網址
$api->post('/shortened', function () {

});

$api->get('/shortened/:key', function ($key) {
    
});

$api->put('/shortened/:key', function ($key) {

});

$api->delete('/shortened/:key', function ($key) {
    DAO::query("SELECT original FROM Shortened WHERE `key` = '$key';");
    $result = DAO::getResult();

    if (empty($result)) {
        http_response_code(404);
        exit;
    } else {
        $briefID = $result[0]['original'];

        DAO::query("DELETE FROM Shortened WHERE `key` = '$key';");
        DAO::query("DELETE FROM Brief WHERE `ID` = '$briefID';");

        http_response_code(204);
    }
});

$api->get('/shortened/search', function () {
    
});

// $api->post('/shortened/:key/', function ($key) {

// });
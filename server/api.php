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

});

// $api->post('/shortened/:key/', function ($key) {

// });
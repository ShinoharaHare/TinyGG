<?php

require_once './utils.php';

$api = new RouteSet;

$api->get('/', function () {
    echo 'API';
});

// 新建短網址
$api->post('/shortened', function () {

});

$api->get('/shortened/:id', function ($id) {

});

$api->put('/shortened/:id', function ($id) {

});

$api->delete('/shortened/:id', function ($id) {

});

// $api->post('/shortened/:id/', function ($id) {

// });
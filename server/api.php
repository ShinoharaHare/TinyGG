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
    // check if exist
    DAO::query("SELECT * FROM shortened WHERE shortened.key='$key';");
    // print_r(DAO::getResult());

    if(empty(DAO::getResult())){  // error
        http_response_code(404);
        return ;
    }

    // update data
    $data = readJSON();
    $target = DAO::getResult()[0];

    $creatorID = $target['creator'];
    $newIPv4 = $data['creator']['IPv4'];
    $newIPv6 = $data['creator']['IPv6'];
    DAO::query("UPDATE creator SET `IPv4`='$newIPv4', `IPv6`='$newIPv6' WHERE `ID`=$creatorID;");
    if(DAO::getError()){  // error
        // echo 1;
        http_response_code(500);/////////////////////////
        return ;
    }

    $briefID = $target['original'];
    $newUrl = $data['brief']['url'];
    $newTitle = $data['brief']['title'];
    $newFavicon = $data['brief']['favicon'];
    $newSummary = $data['brief']['summary'];
    $newCover = $data['brief']['cover'];
    DAO::query("UPDATE brief SET `url`='$newUrl', `title`='$newTitle', `favicon`='$newFavicon', `summary`='$newSummary', `cover`='$newCover' WHERE `ID`=$briefID;");
    if(DAO::getError()){  // error
        // echo 2;
        http_response_code(500);/////////////////////////
        return ;
    }

    $shortenedKey = $target['key'];
    $newKey = $data['key'];
    DAO::query("UPDATE shortened SET `key`='$newKey' WHERE `key`='$shortenedKey';");
    if(DAO::getError()){  // error
        // echo 3;
        http_response_code(409);/////////////////////////
        return ;
    }
    
    DAO::query("select * from shortened s join brief b on s.original=b.ID join creator c on s.creator=c.ID where `key`='$newKey';");
    // print_r(DAO::getResult());

    $newData = DAO::getResult()[0];
    $JSON = array(  'key' => $newData['key'],
                    'original' => array(
                        'ID' => $newData['ID'],
                        'url' => $newData['url'],
                        'title' => $newData['title'],
                        'favicon' => $newData['favicon'],
                        'summary' => $newData['summary'],
                        'cover' => $newData['cover']
                    ),
                    'creator' => array(
                        'ID' => $newData['ID'],
                        'IPv4' => $newData['IPv4'],
                        'IPv6' => $newData['IPv6']
                    ));
    sendJSON($JSON);
    http_response_code(200);
});

$api->delete('/shortened/:key', function ($key) {

});

// $api->post('/shortened/:key/', function ($key) {

// });
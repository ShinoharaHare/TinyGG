<?php

require_once './utils.php';
require_once 'scraper.php';

$api = new RouteSet;

$api->get('/', function () {
    echo 'API';
});

// 新建短網址
$api->get('/shortened', function () {
    $IPv4 = "Iv444";
    $IPv6 = "Iv666";
    $url = "https://www.youtube.com/watch?v=G0o4JUNSeLw&ab_channel=ASMRBakery";
    $key = "822";

    // Check key
    DAO::query("SELECT * FROM `shortened` WHERE `key` = '$key'");

    if(!empty(DAO::getResult())){ // key is repeated
        http_response_code(409);
        return;
    }

    // Check Brief
    DAO::query(findBriefIDQuery($url)); // find Brief ID
    $BriefID = DAO::getResult();
    
    if($BriefID == NULL){ // Brief doesn't exist
        $data = scrape($url);
        $title = $data["title"];
        $favicon = $data["favicon"];
        $summary = $data["summary"];
        $cover = $data["thumbnail"];

        DAO::query(insertBriefQuery($url , $title , $favicon , $summary , $cover)); // add the new brief   
    }

    DAO::query(findBriefIDQuery($url)); // find the brief which just inserted;
    $BriefID = (int)DAO::getResult()[0]["ID"]; // string => integer

    // Check Creator
    DAO::query(findCreatorIDQuery($IPv4 , $IPv6)); // find creator ID
    $CreatorID = DAO::getResult();

    if($CreatorID == NULL){ // Creator doesn't exist
        DAO::query(insertCreatorQuery($IPv4 , $IPv6)); // add the new creator
    }

    DAO::query(findCreatorIDQuery($IPv4 , $IPv6)); // find the creator which just inserted;
    $CreatorID = (int)DAO::getResult()[0]["ID"]; // string => integer

    // Insert Shortened
    DAO::query(insertShortenedQuery($key , $BriefID , $CreatorID)); // add the new shortened

    if(DAO::getResult()){ // insert the new shortened successfully
        $obj = getShortened($key);
        sendJSON($obj);
        http_response_code(201);
    }
});

$api->get('/shortened/:key', function ($key) {    
    $obj = getShortened($key);

    if(!$obj){ // $obj = NULL
        http_response_code(404);
    }
    else{
        http_response_code(200);
    }

    sendJSON($obj);
});

// $api->put('/shortened/:key', function ($id) {

// });

// $api->delete('/shortened/:key', function ($id) {

// });

// $api->post('/shortened/:id/', function ($id) {

// });

function getShortened($key){
    // find shortened
    DAO::query("SELECT * FROM `shortened` WHERE `key` = '$key'"); 
    $Shortened = DAO::getResult();

    if(empty($Shortened)){
        return NULL;
    }

    $BriefID = $Shortened[0]['original'];
    $CreatorID = $Shortened[0]['creator'];
    
    // find brief
    DAO::query("SELECT * FROM `brief` WHERE `ID` = '$BriefID'");
    $Brief = DAO::getResult()[0];

    // find creator
    DAO::query("SELECT * FROM `creator` WHERE `ID` = '$CreatorID'"); 
    $Creator = DAO::getResult()[0];
    
    $obj = array("key" => $key , "original" => $Brief , "creator" => $Creator);

    return($obj);
}
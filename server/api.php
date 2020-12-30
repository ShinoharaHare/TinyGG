<?php

require_once './utils.php';
require_once 'scraper.php';

$api = new RouteSet;

$api->get('/', function () {
    echo 'API';
});

// 新建短網址
$api->post('/shortened', function () {
    $IP = $_SERVER['REMOTE_ADDR'];
    $url = $_POST["original"];
    $key = $_POST["key"];

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
    DAO::query(findCreatorIDQuery($IP)); // find creator ID
    $CreatorID = DAO::getResult();

    if($CreatorID == NULL){ // Creator doesn't exist
        DAO::query(insertCreatorQuery($IP)); // add the new creator
    }

    DAO::query(findCreatorIDQuery($IP)); // find the creator which just inserted;
    $CreatorID = (int)DAO::getResult()[0]["ID"]; // string => integer

    // Insert Shortened
    DAO::query(insertShortenedQuery($key , $BriefID , $CreatorID)); // add the new shortened

    if(DAO::getResult()){ // insert the new shortened successfully
        $obj = getShortened($key);
        sendJSON($obj);
        http_response_code(201);
    }
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
    $newIP = $data['creator']['IP'];
    DAO::query("UPDATE creator SET `IP`='$newIP' WHERE `ID`=$creatorID;");
    if(DAO::getError()){  // error
        // echo 1;
        http_response_code(500);
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
        http_response_code(500);
        return ;
    }

    $shortenedKey = $target['key'];
    $newKey = $data['key'];
    DAO::query("UPDATE shortened SET `key`='$newKey' WHERE `key`='$shortenedKey';");
    if(DAO::getError()){  // error
        // echo 3;
        http_response_code(409);
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
                        'IP' => $newData['IP']
                    ));
    sendJSON($JSON);
    http_response_code(200);
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
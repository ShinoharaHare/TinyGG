<?php

require_once './utils.php';
require_once 'scraper.php';

$api = new RouteSet;

$api->get('/', function () {
    echo 'API'.'<br>';

    echo getIP().'<br>';
});

// 新建短網址
$api->post('/shortened', function () {
    $data = readJSON();
    $IP = getIP();
    $url = $data["original"];
    $key = $data["key"];

    // Check key
    DAO::query("SELECT * FROM `Shortened` WHERE `key` = '$key'");

    if(!empty(DAO::getResult())){ // key is repeated
        http_response_code(409);
        return;
    }

    $data = scrape($url);
    $title = $data["title"];
    $favicon = $data["favicon"];
    $summary = $data["summary"];
    $cover = $data["thumbnail"];

    DAO::query(insertBriefQuery($url , $title , $favicon , $summary , $cover));

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

$api->get('/shortened', function () {
    $ip = getIP();
    DAO::query("SELECT `ID` FROM `Creator` WHERE `IP` = '$ip';");
    $result = DAO::getResult();
    
    if (empty($result)) {
        sendJSON(array());
        return;
    } 
    
    $creatorID = $result[0]['ID'];

    DAO::query("SELECT `key` FROM `Shortened` WHERE `Creator` = '$creatorID'");
    $result = DAO::getResult();
    $arr = array();
    foreach ($result as $item) {
        array_push($arr, getShortened($item['key']));
    }
    sendJSON($arr);
});

$api->get('/shortened/all', function () {
    DAO::query("SELECT `key` FROM `Shortened`;");
    $result = DAO::getResult();
    $arr = array();
    foreach ($result as $item) {
        array_push($arr, getShortened($item['key']));
    }
    sendJSON($arr);
});

// 根據key 列出所有的shortened的內容 包括brief creator內的資訊
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

// 根據target 如果target在url裡 ex: $target = youtube
$api->get('/test/:target' , function ($target) {    
    //$_GET['target'] =
    $result = array();  
    DAO::query("SELECT ID FROM Brief WHERE url LIKE '%$target%'");
    $BriefIDs = DAO::getResult();
    
    foreach ($BriefIDs as $i){
        $BriefID = $i["ID"];
        DAO::query("SELECT * FROM Shortened WHERE original = '$BriefID'");
        $obj = getShortened(DAO::getResult()[0]["key"]);
        array_push($result , $obj);
    }
    
    sendJSON($result);
});

// 回傳creator跟他所創建的短網址的總點擊數
$api->get('/returnSC' , function () {
    DAO::query("SELECT ID , IP , sum(click) as TotalClick FROM `Shortened` , `Creator` WHERE Shortened.creator = Creator.ID GROUP BY ID");
    sendJSON(DAO::getResult());
});  

$api->put('/shortened/:key', function ($key) {
    // check if exist
    DAO::query("SELECT * FROM `Shortened` WHERE Shortened.key = '$key';");
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
    DAO::query("UPDATE `Creator` SET `IP`='$newIP' WHERE `ID`=$creatorID;");
    if(DAO::getError()){  // error
        // echo 1;
        http_response_code(500);
        return ;
    }

    $briefID = $target['original'];
    $newUrl = $data['original']['url'];
    $newTitle = $data['original']['title'];
    $newFavicon = $data['original']['favicon'];
    $newSummary = $data['original']['summary'];
    $newCover = $data['original']['cover'];
    DAO::query("UPDATE `Brief` SET `url`='$newUrl', `title`='$newTitle', `favicon`='$newFavicon', `summary`='$newSummary', `cover`='$newCover' WHERE `ID`=$briefID;");
    if(DAO::getError()){  // error
        // echo 2;
        http_response_code(500);
        return ;
    }

    $shortenedKey = $target['key'];
    $newKey = $data['key'];
    DAO::query("UPDATE `Shortened` SET `key`='$newKey' WHERE `key`='$shortenedKey';");
    if(DAO::getError()){  // error
        // echo 3;
        http_response_code(409);
        return ;
    }
    
    DAO::query("SELECT * FROM `Shortened` s JOIN `Brief` b ON s.original = b.ID JOIN `Creator` c ON s.creator=c.ID WHERE `key`='$newKey';");
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
    DAO::query("SELECT original FROM `Shortened` WHERE `key` = '$key';");
    $result = DAO::getResult();

    if (empty($result)) {
        http_response_code(404);
        return;
    } else {
        $briefID = $result[0]['original'];

        DAO::query("DELETE FROM `Shortened` WHERE `key` = '$key';");
        DAO::query("DELETE FROM `Brief` WHERE `ID` = '$briefID';");

        http_response_code(204);
    }
});

// $api->post('/shortened/:key/', function ($key) {

// });

function getShortened($key){
    // find shortened
    DAO::query("SELECT * FROM `Shortened` WHERE `key` = '$key'"); 
    $Shortened = DAO::getResult();

    if(empty($Shortened)){
        return NULL;
    }

    $BriefID = $Shortened[0]['original'];
    $CreatorID = $Shortened[0]['creator'];
    
    // find brief
    DAO::query("SELECT * FROM `Brief` WHERE `ID` = '$BriefID'");
    $Brief = DAO::getResult()[0];

    // find creator
    DAO::query("SELECT * FROM `Creator` WHERE `ID` = '$CreatorID'"); 
    $Creator = DAO::getResult()[0];
    
    $obj = array("key" => $key , "original" => $Brief , "creator" => $Creator);

    return($obj);
}
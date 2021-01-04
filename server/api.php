<?php

require_once './utils.php';
require_once 'scraper.php';

$api = new RouteSet;

$api->get('/', function () {
    echo 'API'.'<br>';

    echo getIP().'<br>';
});

$api->get('/test', function () {
    // $query = "";
    // DAO::query($query);
    // print_r(DAO::getError());
    // print_r(DAO::getResult());
});

$api->get('/testGetErrorFromAPI', function() {
    echo "<h2> Primary Key Error Test</h2>";
    $query = "SELECT * FROM `Creator` WHERE `ID`=69;";
    echo "<h4>$query</h4>";
    DAO::query($query);
    print_r(DAO::getResult()[0]);
    $query = "INSERT INTO `Creator` VALUES(69, 'forTest');";
    echo "<h4>$query</h4>";
    DAO::query($query);  // primary key error
    echo "<b>Error:   </b>";
    print_r(DAO::getError());

    echo "<hr>";

    echo "<h2> Foreign Key Error Test</h2>";

    $query = "SELECT * FROM `Shortened` WHERE `creator`=15;";
    echo "<h4>$query</h4>";
    DAO::query($query);
    print_r(DAO::getResult()[0]);

    $query = "SELECT * FROM `Creator` WHERE `ID`=15;";
    echo "<h4>$query</h4>";
    DAO::query($query);
    print_r(DAO::getResult()[0]);

    echo "<h4>DELETE FROM `Creator` WHERE `ID`=15;</h4>";
    DAO::query("DELETE FROM `Creator` WHERE `ID`=15;");  // foreign key error
    echo "<b>Error:   </b>";
    print_r(DAO::getError());
    
    http_response_code(500);
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

    if (DAO::getResult()){ // insert the new shortened successfully
        sendJSON(getConvertedByKey($key));
        http_response_code(201);
    }
});

$api->get('/shortened', function () {
    if (isset($_GET['filter'])) {
        switch ($_GET['filter']) {
            case 'shortened':
                filterByShortened($_GET['key'], $_GET['click']);
                return;

            case 'brief':
                filterByBrief($_GET['url'], $_GET['title'], $_GET['summary'], $_GET['min'], $_GET['max']);
                return;

            case 'ip':
                filterByIP(getIP());
                return;

            case 'creator':
                filterByIP($_GET['ip']);
                return;

            case 'complex':
                filterByComplex($_GET);
                return;
        }
    }

    DAO::query(
        "SELECT *
        FROM `Shortened` 
            JOIN `Brief` b ON `original` = b.ID 
            JOIN `Creator` c ON `creator` = c.ID"
    );

    $result = DAO::getResult();
    $arr = array();

    foreach ($result as $item) {
        array_push($arr, convert($item));
    }

    sendJSON($arr);
});
 
// 根據key 列出所有的shortened的內容 包括brief creator內的資訊
$api->get('/shortened/:key', function ($key) {    
    $data = getConvertedByKey($key);
    if (empty($data)) {
        http_response_code(404);
    } else {
        sendJSON($data);
    }
});

// 回傳creator跟他所創建的短網址的總點擊數
$api->get('/rank' , function () {
    DAO::query("SELECT ID , IP , sum(click) as clicks
        FROM Shortened, Creator
        WHERE Shortened.creator = Creator.ID
        GROUP BY ID
        ORDER BY clicks DESC");
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
    $newIP = $data['creator']['ip'];
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
    $newCover = $data['original']['thumbnail'];
    DAO::query("UPDATE `Brief` SET `url`='$newUrl', `title`='$newTitle', `favicon`='$newFavicon', `summary`='$newSummary', `cover`='$newCover' WHERE `ID`=$briefID;");
    if(DAO::getError()){  // error
        // echo 2;
        http_response_code(500);
        return ;
    }

    $shortenedKey = $target['key'];
    $newKey = $data['key'];
    $newClick = $data['click'];
    
    DAO::query("UPDATE `Shortened`
        SET `key`='$newKey', `click`=$newClick
        WHERE `key`='$shortenedKey';");

    if(DAO::getError()){  // error
        // echo 3;
        http_response_code(409);
        return ;
    }

    sendJSON(getConvertedByKey($newKey));

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

use \Firebase\JWT\JWT;
$key = 'ThgBQv7WzWgV5auB';
$password = '12345678';

$api->post('/token', function () {
    global $key, $password;

    $data = readJSON();

    if (empty($data['password']) || $data['password'] != $password) {
        http_response_code(401);
    } else {
        $expire = time() + 10 * 60;
        $jwt = JWT::encode([
            // 'iss' => 'https://tinyygg.herokuapp.com',
            // 'aud' => 'https://tinyygg.herokuapp.com',
            // 'iat' => time(),
            'expire' => $expire
        ], $key);

        setcookie('token', $jwt, $expire);
        http_response_code(200);
    }
});

$api->post('/token/verify', function() {
    global $key, $password;

    if (empty($_COOKIE['token'])) {
        sendJSON(false);
    } else {
        $decoded = (array) JWT::decode($_COOKIE['token'], $key, array('HS256'));
        if (time() < $decoded['expire']) {
            sendJSON(true);
        } else {
            sendJSON(false);
        }
    }
});

function filterByIP($ip) {
    DAO::query(
        "SELECT *
        FROM `Shortened` 
            JOIN `Brief` b ON `original` = b.ID 
            JOIN `Creator` c ON `creator` = c.ID
        WHERE creator IN (
            SELECT ID FROM `Creator`
            WHERE IP LIKE '%$ip%'
        );"
    );
    $result = DAO::getResult();
    
    if (empty($result)) {
        sendJSON(array());
        return;
    }

    $arr = array();
    foreach ($result as $item) {
        array_push($arr, convert($item));
    }
    sendJSON($arr);
}

function filterByShortened($key, $click) {
    $query = 
        "SELECT *
        FROM `Shortened` 
            JOIN `Brief` b ON `original` = b.ID 
            JOIN `Creator` c ON `creator` = c.ID
        WHERE `key` IN (
            SELECT `key` FROM `Shortened`
            WHERE
                `key` LIKE '%$key%'
                AND `click` >= $click
        );";
    DAO::query($query);

    print_r(DAO::getError());
    $result = DAO::getResult();
    $arr = array();
    foreach ($result as $item) {
        array_push($arr, convert($item));
    }
    sendJSON($arr);
}

function filterByBrief($url, $title, $summary, $min, $max) {
    $query = 
        "SELECT *
        FROM `Shortened` 
            JOIN `Brief` b ON `original` = b.ID 
            JOIN `Creator` c ON `creator` = c.ID
        WHERE original IN (
            SELECT ID FROM `Brief`
            WHERE
                url LIKE '%$url%'
                AND title LIKE '%$title%'
                AND summary LIKE '%$summary%'
                AND CHAR_LENGTH(title) >= $min
                AND CHAR_LENGTH(title) <= $max
        );";
            
    DAO::query($query);

    $result = DAO::getResult();
    $arr = array();
    foreach ($result as $item) {
        array_push($arr, convert($item));
    }
    sendJSON($arr);
}

function filterByComplex($params) {
    $key = $params['key'];
    $click = $params['click'];
    $url = $params['url'];
    $title = $params['title'];
    $summary = $params['summary'];
    $min = $params['min'];
    $max = $params['max'];
    $ip = $params['ip'];
    // $key = isset($params['key']) ? $params['key'] : '';
    // $click = isset($params['click']) ? $params['click'] : '';
    // $url = isset($params['url']) ? $params['url'] : '';
    // $title = isset($params['title']) ? $params['title'] : '';
    // $summary = isset($params['summary']) ? $params['summary'] : '';
    // $min = isset($params['min']) ? $params['min'] : '';
    // $max = isset($params['max']) ? $params['max'] : '';
    // $ip = isset($params['ip']) ? $params['ip'] : '';

    $query = 
        "SELECT *
        FROM `Shortened` 
            JOIN `Brief` b ON `original` = b.ID 
            JOIN `Creator` c ON `creator` = c.ID
        WHERE 
            `key` IN (
                SELECT `key` FROM `Shortened`
                WHERE
                    `key` LIKE '%$key%'
                    AND `click` >= $click
            )
            AND
            original IN (
                SELECT ID FROM `Brief`
                WHERE
                    url LIKE '%$url%'
                    AND title LIKE '%$title%'
                    AND summary LIKE '%$summary%'
                    AND CHAR_LENGTH(title) >= $min
                    AND CHAR_LENGTH(title) <= $max
            )
            AND
            creator IN (
                SELECT ID FROM `Creator`
                WHERE IP LIKE '%$ip%'
            );";

    DAO::query($query);

    $result = DAO::getResult();
    $arr = array();
    foreach ($result as $item) {
        array_push($arr, convert($item));
    }
    sendJSON($arr);
}

function getConvertedByKey($key) {
    DAO::query(
        "SELECT *
        FROM `Shortened` 
            JOIN `Brief` b ON `original` = b.ID 
            JOIN `Creator` c ON `creator` = c.ID
        WHERE `key` = '$key';"
    );
    $result = DAO::getResult();
    return empty($result) ? null : convert($result[0]);
}

function convert($table) {
    $original = [
        'id' => $table['original'],
        'url' => $table['url'],
        'title' => $table['title'],
        'summary' => $table['summary'],
        'thumbnail' => $table['cover'],
        'favicon' => $table['favicon']
    ];

    $creator = [
        'id' => $table['creator'],
        'ip' => $table['IP'],
    ];

    return [
        'key' => $table['key'],
        'click' => $table['click'],
        'original' => $original,
        'creator' => $creator
    ];
}

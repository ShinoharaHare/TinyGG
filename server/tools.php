<?php
// tools.php
function insertCreatorQuery($IPv4, $IPv6){
    $IPv4 = $IPv4? "'$IPv4'":'NULL';
    $IPv6 = $IPv6? "'$IPv6'":'NULL';
    $sqlQuery = "INSERT INTO `Creator` VALUES(NULL, $IPv4, $IPv6);";
    return $sqlQuery;
}

function insertBriefQuery($url, $title, $favicon, $summary, $cover){
    $title   = $title?   "'$title'":  'NULL';
    $favicon = $favicon? "'$favicon'":'NULL';
    $summary = $summary? "'$summary'":'NULL';
    $cover   = $cover?   "'$cover'":  'NULL';
    return "INSERT INTO `Brief` VALUES(NULL, '$url', $title, $favicon, $summary, $cover);";
}

function insertShortenedQuery($key, $originalID, $creatorID){
    if(!(isset($key)&&isset($originalID)&&isset($creatorID))){
        die("insertShortenedQuery(): parameters shout BE NOT NULL!!!");
    }
    return "INSERT INTO `` VALUES('$key', $originalID, $creatorID);";
}

function getLastID(){
    return "SELECT LAST_INSERT_ID();";
}

function testQuery(){
    return "select count(*) from creator;";
}
?>
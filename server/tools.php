<?php
// tools.php

// Creator
function insertCreatorQuery($IP){
    $IP = $IP? "'$IP'":'NULL'; // if $IP return '$IP' else return 'NULL'

    $sqlQuery = "INSERT INTO `Creator` VALUES(NULL , $IP);";

    return $sqlQuery;
}

function findCreatorIDQuery($IP){
    $IP = $IP ? "'$IP'" :'NULL'; // if $IP return '$IP' else return 'NULL'

    $sqlQuery = "SELECT `ID` FROM `Creator` WHERE `IP` = $IP";

    return $sqlQuery;
}

// Brief
function insertBriefQuery($url , $title , $favicon , $summary , $cover){
    $title   = $title?   "'$title'":  'NULL';
    $favicon = $favicon? "'$favicon'":'NULL';
    $summary = $summary? "'$summary'":'NULL';
    $cover   = $cover?   "'$cover'":  'NULL';

    $sqlQuery = "INSERT INTO `Brief` VALUES(NULL , '$url' , $title , $favicon , $summary , $cover);";

    return $sqlQuery;
}

function findBriefIDQuery($url){
    $sqlQuery = "SELECT `ID` FROM `Brief` WHERE `url` = '$url'";

    return $sqlQuery;
}

// Shortened
function insertShortenedQuery($key , $BriefID , $CreatorID){
    if(!(isset($key) && isset($BriefID) && isset($CreatorID))){
        die("insertShortenedQuery(): parameters shout BE NOT NULL!!!");
    }

    $sqlQuery = "INSERT INTO `Shortened` VALUES('$key' , $BriefID , $CreatorID);";

    return $sqlQuery;
}

function getLastID(){
    return "SELECT LAST_INSERT_ID();";
}

function testQuery(){
    return "select count(*) from Creator;";
}
?>
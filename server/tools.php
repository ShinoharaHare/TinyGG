<?php
// tools.php

// Creator
function insertCreatorQuery($IPv4 , $IPv6){
    $IPv4 = $IPv4? "'$IPv4'":'NULL'; // if $IPv4 return '$IPv4' else return 'NULL'
    $IPv6 = $IPv6? "'$IPv6'":'NULL';

    $sqlQuery = "INSERT INTO `creator` VALUES(NULL , $IPv4 , $IPv6);";

    return $sqlQuery;
}

function findCreatorIDQuery($IPv4 , $IPv6){
    $IPv4 = $IPv4? "'$IPv4'":'NULL'; // if $IPv4 return '$IPv4' else return 'NULL'
    $IPv6 = $IPv6? "'$IPv6'":'NULL';

    $sqlQuery = "SELECT `ID` FROM `creator` WHERE `IPv4` = $IPv4 AND `IPv6` = $IPv6";

    return $sqlQuery;
}

// Brief
function insertBriefQuery($url , $title , $favicon , $summary , $cover){
    $title   = $title?   "'$title'":  'NULL';
    $favicon = $favicon? "'$favicon'":'NULL';
    $summary = $summary? "'$summary'":'NULL';
    $cover   = $cover?   "'$cover'":  'NULL';

    $sqlQuery = "INSERT INTO `brief` VALUES(NULL , '$url' , $title , $favicon , $summary , $cover);";

    return $sqlQuery;
}

function findBriefIDQuery($url){
    $sqlQuery = "SELECT `ID` FROM `brief` WHERE `url` = '$url'";

    return $sqlQuery;
}

// Shortened
function insertShortenedQuery($key , $BriefID , $creatorID){
    if(!(isset($key) && isset($BriefID) && isset($creatorID))){
        die("insertShortenedQuery(): parameters shout BE NOT NULL!!!");
    }

    $sqlQuery = "INSERT INTO `shortened` VALUES('$key' , $BriefID , $creatorID);";

    return $sqlQuery;
}

function getLastID(){
    return "SELECT LAST_INSERT_ID();";
}

function testQuery(){
    return "select count(*) from creator;";
}
?>
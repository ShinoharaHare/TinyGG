<?php

function readJSON() {
    $str = file_get_contents('php://input');
    return json_decode($str, true);
}

function sendJSON($obj)
{
    header('Content-type: application/json');
    echo json_encode($obj);
}

function checkFields($fields) {

}
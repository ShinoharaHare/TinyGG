<?php
require_once('../utils.php');

$data = readJSON();

if (isset($data['key']) && isset($data['original'])) {
    sendJSON($data);
} else {
    
}


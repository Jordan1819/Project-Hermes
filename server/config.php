<?php

$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'hermes';

$db = new mysqli($host, $user, $password, $db_name);

if($db->connect_error){
    die("Could not connect to db: ". $db->connect_error);
}

$db->set_charset('utf8mb4');

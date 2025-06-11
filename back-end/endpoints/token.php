<?php 
include(__DIR__.'/../config/database.php');
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);


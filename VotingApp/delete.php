<?php
require_once 'functions.php';
session_start();

$pid = $_GET['pid'] ?? -1;
if ($pid != -1) {
    delete($pid);
    redirect('index.php');
}
redirect('index.php');

?>
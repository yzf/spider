#!/usr/bin/php
<?php 
require_once 'functions.php';
//提取关键信息的主程序，利用linux的shell实现多线程
$product_id = (int)$argv[1];
$flag = (int)$argv[2];
get_product_info($product_id);
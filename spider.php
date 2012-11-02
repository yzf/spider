#!/usr/bin/php
<?php
require_once 'functions.php';
//爬虫主程序，利用linux的shell实现多线程
//获取第$page页的源码，
$page = $argv[1];
get_html_code($page);
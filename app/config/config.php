<?php 

// LocalHost but we don't need in this project
define('MYSQL_USER','root');
define('MYSQL_PASSWORD','Pass@1234');
define('MYSQL_HOST','127.0.0.1');
define('MYSQL_DATABASE','dbname');


$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
);

$pdo = new PDO(
    'mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DATABASE,MYSQL_USER,MYSQL_PASSWORD,$options
);

date_default_timezone_set("Asia/Yangon");

$diffWithGMT = 6 * 60 * 60 + 30 * 60; //converting time difference to seconds.
$ygntime     = gmdate("Y-m-d H:i:s", time() + $diffWithGMT);
$ygndate     = gmdate("Y-F-d", time() + $diffWithGMT);
$ygndatetime = gmdate("Y-F-d ( D ) h:i A", time() + $diffWithGMT);
$today       = date("d/n/Y");



function escape($html){
    return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
?>

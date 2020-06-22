<?php
$dsn = 'mysql:host = localhost;dbname=shop';
$user = 'root';
$password = '';
$option = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);

try
{
    $connection = new PDO($dsn,$user,$password,$option);
    $connection ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

}

catch(PDOException $e)
{
    echo "faild to connect". $e->getMessage();
}

?>
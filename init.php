<?php

//Error Repprting
ini_set('display_errors','on');
error_reporting(E_ALL);

include "admin/db_connect.php";

$sessionUser = '';
if(isset($_SESSION['user']))
{
    $sessionUser = $_SESSION['user'];
}

//Routes
$lang = 'includes/languages/';     //languages directory
$func = 'includes/functions/';    //functions directory
$tpl = 'includes/templates/';    //templates directory
$css = 'layout/css/';           //css directory
$js = 'layout/js/';            //js directory



//Includes Important Files
include $func."functions.php";
include $lang. "english.php";
include $tpl."header.php"; 

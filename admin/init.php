<?php

include "db_connect.php";


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

if(!isset($noNavbar)){include $tpl."navbar.php"; }
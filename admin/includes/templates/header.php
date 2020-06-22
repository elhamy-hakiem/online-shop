<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitle();?></title>
    <link rel="icon" href="layout/images/Icons8-Windows-8-Ecommerce-Buy.ico">
    <link href="https://fonts.googleapis.com/css2?family=Changa&family=Do+Hyeon&family=Fredoka+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>all.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap-select.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap-tokenfield.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>admin.css">
</head>
<body <?php if(isset($login_body)){echo "class='login-body'";} ?>>

    

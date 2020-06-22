<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php getTitle();?></title>
    <link rel="icon" href="layout/images/Icons8-Windows-8-Ecommerce-Buy.ico">
    <!-- fonts style  -->
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Narrow:ital,wght@1,500&family=Chicle&family=Oswald:wght@600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Delius&display=swap" rel="stylesheet">
    <!-- bootstrap links style  -->
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>all.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap-select.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap-tokenfield.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>user.css">
</head>
<body>

 <!-- Start Upper Bar  -->
 <div class="navbar navbar-expand-lg navbar-dark bg-dark upper-bar">
    <div class="container">
       <!-- Start Upper Bar Connect Option  -->
        <div class="collapse navbar-collapse upper-bar-connect">
            <ul class="navbar-nav mr-auto">

                <li class="nav-item text-white phone">
                    <i class="fas fa-phone-volume"></i>
                        +20 01149137305
                </li> 
                <li class="nav-item text-white email">
                    <i class="far fa-envelope"></i>
                    onlineshopservices6@gmail.com
                </li>                
            </ul>
        </div>
        <!-- End Upper Bar Connect Option  -->

        <!-- Start Upper Bar Icons  -->
        <div class="navbar-brand mx-auto upper-bar-icon">
            <a class="insta" href="https://www.instagram.com/elhamy_hakiem/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a class="facebook" href="https://www.facebook.com/elhamy.hakiem.96/" target="_blank">
            <i class="fab fa-facebook"></i>
            </a>
            <a class="twitter" href="https://twitter.com/elhamy_hakiem" target="_blank">
                <i class="fab fa-twitter "></i>
            </a>
            <a class="whatsapp" href="https://api.whatsapp.com/send?phone=+201149137305" target="_blank">
                <i class="fab fa-whatsapp "></i>
            </a>
        </div>
        <!-- End Upper Bar Icons  -->

        <!-- Start Upper Bar Languages Options -->
        <div class="collapse navbar-collapse upper-bar-language">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link english" href="">EN <span class="sr-only">(current)</span></a>
                </li>     
                <li class="nav-item">
                    <a class="nav-link arabic" href="">AR <span class="sr-only">(current)</span></a>
                </li>             
            </ul>
        </div>
        <!-- End Upper Bar Languages Options -->

    </div>
</div>
 <!-- End Upper Bar  -->

<!-- Start Navbar 2  -->
<section class="nav2">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="index.php"><img class='img-fluid' src="layout/images/logo4.png" alt=""></a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#app-nav" aria-controls="app-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Start Show Categories  -->
            <div class="collapse navbar-collapse" id="app-nav">
                <ul class="navbar-nav mr-auto">
                    <li class='nav-item'><a class='nav-link active-link' href='index.php'>HOME <span class='sr-only'>(current)</span></a></li>
                    <?php 
                        $categories =getAllFrom('*' , 'categories','','', 'WHERE parent = 0 ', 'AND Visibility = 0', 'ID', 'ASC' , 'LIMIT 6');
                        foreach($categories as $category )
                        {
                            $childCats =  getAllFrom('*' , 'categories', '',  '', 'WHERE parent = '.$category['ID'].'', 'AND Visibility = 0', 'ID');
                            if(empty($childCats))
                            {
                                echo "<li class='nav-item'>";
                                    echo '<a class="nav-link" href="categories.php?catid='.$category["ID"].'">'.$category["Name"] .'<span class="sr-only">(current)</span></a>';
                                echo "</li>";
                            }
                            else
                            { ?>
                                <li class='nav-item show-subCategory'> 
                                    <div class="dropdown">
                                        <a href="#" class="nav-link dropdown-toggle" type="button" id="showSubCategory" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo $category["Name"]; ?>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="showSubCategory">
                                            <?php 
                                                foreach($childCats as $child)
                                                {
                                                    echo "<a class='dropdown-item' href='categories.php?catid=".$child['ID']."'>".$child['Name']."</a>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                               </li>
                      <?php }
                        }
                    ?>
                </ul>
                <?php
                    if(isset($_SESSION['user']))
                    {?>
                       
                        <div class="nav-item dropdown avatar-menue">

                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php
                                    $getAvatar =  $connection ->prepare("SELECT `user_Avatar` FROM users WHERE `UserID` = ?");
                                    $getAvatar ->execute(array($_SESSION['userid']));
                                    $avatar = $getAvatar ->fetch();
                                    if(empty($avatar['user_Avatar']))
                                    {
                                        echo "<img class ='user-avatar' src='admin/uploads/avatars/mask.jpg' alt ='User Avatar'/>";
                                    }
                                    else
                                    {
                                        echo "<img class ='user-avatar' src= 'admin/uploads/avatars/".$avatar['user_Avatar']."' alt='User Avatar'>";
                                    }
                                ?>
                                <span><?php echo $sessionUser;?></span>
                            </a>

                            <div class="dropdown-menu " aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="profile.php?profile">Profile</a>
                                <a class="dropdown-item" href="newad.php">Add Item</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </div>
                       
                    <?php }
                    else
                    {
                        
                        echo "<a href='login.php' class='brn login'>Login / Signup</a>";
                    }
                ?>
             
            </div>
        </div>
    </nav>
</section> 
<!-- End Navbar 2  -->

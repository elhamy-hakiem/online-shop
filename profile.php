<?php 
    ob_start();
    session_start();
    $pageTitle="Profile"; 
    include "init.php";
    if(isset($_GET['profile']))
    {
        if(isset($_SESSION['user']))
        {
            $getUser = $connection ->prepare("SELECT * FROM users WHERE Username = ? ");
            $getUser ->execute(array( $_SESSION['user'] ));
            $userInfo = $getUser ->fetch();         
?>
            <!-- Start Profile Page  -->
            <div class="container">
                <?php if(isset($_GET['edit']) && $userInfo['RegStatus'] == 1 ) { ?>

                    <!-- Start Edit Profile Info  -->
                    <div class="profile-info">
                        <div class="page-header">
                            <h1 class="text-center">Edit Profile</h1>
                        </div>
                        <!-- Start Edit Validation  -->
                        <?php
                            if(isset($_POST['edit_profile'])  &&  $_SERVER['REQUEST_METHOD'] == 'POST')
                            {

                                // Upload Variables 
                                $userAvatar = $_FILES['userAvatar'];
                                $avatarName = $_FILES['userAvatar']['name'];
                                $avatarType = $_FILES['userAvatar']['type'];
                                $avatarTmp  = $_FILES['userAvatar']['tmp_name'];
                                $avatarSize = $_FILES['userAvatar']['size'];
                                // List Of Allowed File Type To Upload 
                                $avatarAllowedExtension = array("jpeg","jpg","png","gif");

                                //Get Avatar Extension
                                $arrayName = explode(".",$avatarName);
                                $avatarExtension = strtolower(end($arrayName));
                                
                                $id        = filter_var($_SESSION['userid'],FILTER_SANITIZE_NUMBER_INT);
                                $email     = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
                                $username  = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
                                $fullname  = filter_var($_POST['fullName'],FILTER_SANITIZE_STRING);
                                $oldPass   = $_POST['oldpassword'];  //This Is my password In Database
                                $newPassword = $_POST['mypassword'];
                                $oldAvatar =   $userInfo['user_Avatar'];

                                    //Validate The Form
                                $formErrors =array();
                                if(empty($email))
                                {
                                    $formErrors[] = "Email Address Cant Be <strong>Empty</strong>";
                                }
                                if(!filter_var($email,FILTER_VALIDATE_EMAIL))
                                {
                                    $formErrors[] = " Email Address  <strong>Not Valid</strong>";
                        
                                }
                                if(empty($fullname))
                                {
                                    $formErrors[] = "Full Name Cant Be <strong>Empty</strong>";
                                }
                                if(strlen($fullname) < 8)
                                {
                                    $formErrors[] = "Full Name Must Be Greater Than  <strong>8 Characters</strong>";
                                }
                                if(empty($username))
                                {
                                    $formErrors[] = "Username Cant Be <strong>Empty</strong>";
                                }
                                if(strlen($username) < 5)
                                {
                                    $formErrors[] = "Username Must Be Greater Than  <strong>5 Characters</strong>";
                                }
                                if(empty($avatarName))
                                {
                                    $avatar =$oldAvatar;
                                }
                                if(! empty($avatarName))
                                {
                                    if(! in_array($avatarExtension,$avatarAllowedExtension))
                                    {
                                        $formErrors[] = "This Extension is Not <strong>Allowed</strong>";
                                    }
                                    if($avatarSize > 3145728)
                                    {
                                        $formErrors[] = "Avatar Can't Be Larger Than <strong>3MB</strong>";
                                    }
                                    else
                                    {
                                        // Edit User Avatar Name Before Insert In Database 
                                        $avatar = rand(0,100000000).'_'.$avatarName;
                                        $destenation = realpath(dirname('admin'));
                                        move_uploaded_file($avatarTmp,$destenation."\admin\uploads\avatars\\".$avatar);
                                    }
                                }
                                if(!empty($newPassword))
                                {
                                    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $newPassword))
                                    {
                                        $formErrors[] = "Password Must Be have at least one number and at least one letter and there have to be 8-12 characters  ";
                                    }
                                    else
                                    {
                                        $newPassword = password_hash($newPassword,PASSWORD_DEFAULT);
                                    }
                                }
                                if(empty($newPassword))
                                {
                                    $newPassword = $oldPass;
                                }                                     
                                if(empty ($formErrors))
                                {
                                    //Check if username is exist or no
                                    $stmt = $connection ->prepare("SELECT * FROM users WHERE Username = ? AND UserID != ? ");
                                    $stmt -> execute (array( $username, $id ));
                                    $count = $stmt ->rowCount();
                                    if($count > 0 )
                                    {
                                        echo "<div class='alert alert-danger text-center'>Username Is Already Exist</div>";
                                    }
                                    else
                                    {
                                        $stmt2 = $connection ->prepare("UPDATE `users` SET  `Email`= ?, `FullName`= ?, `Username`= ?, `password`= ?, `user_Avatar` = ?  WHERE `UserID`= ?");
                                        $stmt2 ->execute(array($email, $fullname, $username, $newPassword, $avatar,$id));
                                        echo "<div class='alert alert-success text-center'> Profile Updated</div>";
                                        // refresh page 
                                        header("refresh:2");
                                    }
                                
                                }
                                else
                                {
                                    // Loop Into Errors And Show Error 
                                    foreach($formErrors as $error)
                                    {
                                        echo "<div class='alert alert-danger text-center'>".$error."</div>";
                                    }
                                }
                                
                            }
                        ?>
                        <!-- End Edit Validation  -->
                        <div class="info-box">
                            <!-- Start Edit Form  -->
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class='row'>

                                    <div class='col-md-3 col-sm-12'>
                                        <div class="user-pic">
                                            <?php
                                                if(empty($userInfo['user_Avatar']))
                                                {
                                                    echo "<img class='img-fluid' src='admin/uploads/avatars/mask.jpg' alt ='User Avatar'/>";
                                                }
                                                else
                                                {
                                                    echo "<img class='img-fluid' src= 'admin/uploads/avatars/".$userInfo['user_Avatar']."' alt='User Avatar'>";
                                                }
                                            ?>
                                        </div>
                                        <!-- Start Upload Avatar  -->
                                        <div class='custom-upload'>
                                            <span>Choose Photo</span>
                                            <input type="file"  name="userAvatar">
                                        </div>
                                        <!-- End Upload Avatar  -->
                                    </div>

                                    <div class='col-md-9 col-sm-12'>
                                        <div class="user-info">

                                            <!-- Start Username Field  -->
                                            <div class="form-group row">
                                                <label for="username" class="col-lg-3 col-sm-4 col-form-label"><i class="fa fa-unlock-alt "></i> Username </label>
                                                <div class="col-lg-9 col-sm-8">
                                                    <input type="text" id="username" class="form-control" name="username" value="<?php echo $userInfo['Username'];?>" autocomplete="off" required = "required">
                                                </div>
                                            </div>
                                            <!-- End Username Field  -->

                                            <!-- Start Email Field  -->
                                            <div class="form-group row">
                                                <label for="Email" class="col-lg-3 col-sm-4 col-form-label"><i class="fa fa-envelope "></i> Email </label>
                                                <div class="col-lg-9 col-sm-8">
                                                    <input type="email"  id ='Email' class="form-control" name="email" value="<?php echo $userInfo['Email'];?>" autocomplete="off" required="required">
                                                </div>
                                            </div>
                                            <!-- End Email Field  -->

                                            <!-- Start Full Name Field  -->
                                            <div class="form-group row">
                                                <label for="Fullname" class="col-lg-3 col-sm-4 col-form-label"><i class="fa fa-user "></i> Full Name </label>
                                                <div class="col-lg-9 col-sm-8">
                                                    <input type="text"  id ='Fullname' class="form-control" name="fullName" value="<?php echo $userInfo['FullName'];?>" autocomplete="off" required="required">
                                                </div>
                                            </div>
                                            <!-- End Full Name Field  -->

                                            <!-- Start Password Field  -->
                                            <div class="form-group row">
                                                <label for="Password" class="col-lg-3 col-sm-4 col-form-label"><i class="fas fa-lock"></i> Password </label>
                                                <div class="col-lg-9 col-sm-8">
                                                    <input type="hidden" value="<?php echo $userInfo['password'];?>"  name="oldpassword">
                                                    <input type="password" class="form-control" name="mypassword" placeholder="Leave Blank If You Dont Want To Change " autocomplete="new-password">
                                                </div>
                                            </div>
                                            <!-- End Password Field  -->

                                            <div class="form-group row">
                                                <div class="col-sm-10">
                                                    <button type="submit" name="edit_profile" class="btn btn-danger"><i class="fas fa-save"></i> Save</button>
                                                </div>
                                            </div>

                                        </div> 
                                    </div>
                                </div>
                            </form>
                            <!-- End Edit Form  -->
                        </div>
                    </div>
                    <!-- End Edit Profile Info  -->
                    
                <?php } else{ ?>
                    <!-- Start Profile Info  -->
                    <div class="profile-info">
                        <div class="page-header">
                            <h1 class="text-center">My Profile</h1>
                        </div>
                        <div class="info-box">
                            <?php 
                                if($userInfo['RegStatus'] == 0)
                                {
                                    echo "<span class='badge badge-warning approve-status'><i class='fa fa-exclamation-circle' aria-hidden='true'></i> Waiting Approval</span>";
                                }
                            ?>
                            <div class="user-pic float-left">
                                <?php
                                    if(empty($userInfo['user_Avatar']))
                                    {
                                        echo "<img class='img-fluid' src='admin/uploads/avatars/mask.jpg' alt ='User Avatar'/>";
                                    }
                                    else
                                    {
                                        echo "<img class='img-fluid' src= 'admin/uploads/avatars/".$userInfo['user_Avatar']."' alt='User Avatar'>";
                                    }
                                ?>
                            </div>
                            <div class="user-info float-left">
                                <h4><span><i class="fa fa-unlock-alt "></i> Username : </span> <?php echo $userInfo['Username'];?></h4>
                                <h5><span><i class="fa fa-envelope "></i> Email : </span> <?php echo $userInfo['Email'];?></h5>
                                <h5><span><i class="fa fa-user "></i> Full Name : </span> <?php echo $userInfo['FullName'];?></h5>
                                <h5><span><i class="fas fa-calendar "></i> Register Date : </span> <?php echo $userInfo['Date'];?></h5>
                                <h5><span><i class="fa fa-tags "></i> Faviourt Category : </span></h5>
                                <hr>
                                <a href="profile.php?profile&edit" class="edit-btn">Edit</a>
                            </div> 
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- End Profile Info  -->

                    <!-- Start Show Items  -->
                    <?php
                        echo "<div class='public-bg px-4'>"; 
                                echo "<div class='info-tittle badge badge-pill badge-danger'><i class='fa fa-tags'></i> My Items</div> <hr>";
                                $memberid = $userInfo['UserID'];

                                $user_Items = getAllFrom('*' , 'items', '', '', 'WHERE Member_ID = '.$memberid.'','', 'Item_ID');

                                if(! empty($user_Items))
                                {
                                    echo "<div class='row'>";
                                    foreach($user_Items as $item)
                                    {?>
                                            <!-- Check If Category Has Items Hidden Or Not  -->
                                        <?php if(checkStatus('Visibility','categories', 'ID',$item['Cat_ID']) == 0){?>

                                            <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                                <div class="card item-box">
                                                        <span class="price-tag"><?php echo $item['Price']; ?></span>
                                                        <?php 
                                                            if($item['Approve'] == 0)
                                                            {
                                                                echo "<span class='approve-status'>Waiting Approval</span>";
                                                            }
                                                            if(empty($item['Image']))
                                                            {
                                                                echo "<img class='img-thumbnail' src='admin/uploads/products/add.jpg' alt ='Item Image'/>";
                                                            }
                                                            else
                                                            {
                                                                echo "<img class='img-thumbnail' src= 'admin/uploads/products/".$item['Image']."' alt='Item Image'>";
                                                            }
                                                        ?>
                                                        <div class="card-body pt-2">
                                                            <div <?php if(isset($_SESSION['user'])) { echo 'id="rating-list" data-itemid="'.$item["Item_ID"].'"';}?> class='item-rating float-left'>
                                                                <?php
                                                                    $rate = getRate($item["Item_ID"]);
                                                                    for ($count =1 ; $count <=5 ; $count++)
                                                                    {
                                                                        if($count <= $rate )
                                                                        {
                                                                            echo '<span class="rating-color" data-index="'.$count.'"><i class="far fa-star"></i></span>';
                                                                        }
                                                                        else
                                                                        {
                                                                            echo '<span data-index="'.$count.'"><i class="far fa-star"></i></span>';
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                            <div class="item-date"><?php echo $item['Add_Date']; ?></div>
                                                            <h3 class="card-title my-1"><a href="items.php?itemid=<?php echo $item['Item_ID'];?>"><?php echo $item['Name']; ?></a></h3>
                                                            <p class="card-text "><?php echo $item['Description']; ?></p>
                                                            <span class='readMore-btn badge badge-danger'>Read More</span>
                                                        </div>
                                                </div>
                                            </div>

                                        <?php } 
                                            else
                                            {
                                                echo '<div class="item-box col-lg-3 col-md-4 col-sm-6 alert alert-warning alert-dismissible fade show" role="alert">';
                                                    echo '<strong> This Item Is Hidden By Admin </strong>';
                                                    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                                                        echo '<span aria-hidden="true">&times;</span>';
                                                    echo ' </button>';
                                                echo '</div>';
                                                
                                            }
                                        ?>
                                        

                                <?php }
                                    echo "</div>";
                                }
                                else
                                {
                                    echo "<div class='alert alert-danger text-center mb-0'>Ther\'s No Ads To Show,Create 
                                        <a href='newad.php' class='badge badge-danger badge-ad'>New Ad</a></div>";
                                } 
                        echo "</div>";
                    ?>
                    <!-- End Show Items  -->

                    <!-- Start Show Comments  -->
                    <?php
                        echo "<div class='public-bg all-comments'>"; 
                                echo "<div class='info-tittle badge badge-pill badge-danger'><i class='fa fa-comments'></i> My Comments</div> <hr>";

                                $user_id = $userInfo['UserID'];
                                $comments = getAllFrom('*' , 'comments', '', '', ' WHERE user_id ='.$user_id.'','', 'c_id');
                                
                                if(! empty($comments))
                                {
                                    foreach($comments as $comment)
                                    {?>
                                        <div class="profile_comments">
                                                <div>
                                                    <p class="my-comments">
                                                        <?php echo $comment['comment']; ?>
                                                    </p>
                                                    <span class="comm_date">
                                                        <?php echo $comment['comment_date'];  ?>
                                                    </span>
                                                </div>
                                                <div class='clearfix'></div>
                                        </div>
                                        <hr>
                                    <?php }
                                }
                                else
                                {
                                    echo "<div class='alert alert-info text-center mb-0'>Ther\'s No Comments To Show </div>";
                                }
                        echo "</div>";
                    ?>
                    <!-- End Show Comments  -->
                <?php } ?>
            </div>
            <!-- End Profile Page    -->

<?php   
        }
        else
        {
            header("location: login.php");
            exit();
        }
    }
    // Start Show Members Info 
    elseif(isset($_GET['memberid']) && is_numeric($_GET['memberid']))
    {
        $memberid = intval($_GET['memberid']);
        $getMember = $connection ->prepare("SELECT FullName,`Date`,user_Avatar FROM users WHERE UserID = ? ");
        $getMember ->execute(array( $memberid ));
        $memberInfo = $getMember ->fetch();
        $count = $getMember ->rowCount();
    ?>
        <div class="container">
            <?php if($count == 1 ) { ?>
                <!-- Start Show Member Info  -->
                    <div class="member-info">
                        <div class="user-pic">
                            <?php
                                if(empty($memberInfo['user_Avatar']))
                                {
                                    echo "<img class='img-fluid' src='admin/uploads/avatars/mask.jpg' alt ='User Avatar'/>";
                                }
                                else
                                {
                                    echo "<img class='img-fluid' src= 'admin/uploads/avatars/".$memberInfo['user_Avatar']."' alt='User Avatar'>";
                                }
                            ?>
                        </div>
                        <h3 class='badge badge-danger member-name'><?php echo $memberInfo['FullName']; ?></h3>
                    </div>
                <!-- End Show Member Info  -->
                <!-- Start Show Member Items  -->
                    <?php
                        echo "<div class='public-bg px-4 mt-5'>"; 
                                echo "<div class='info-tittle badge badge-pill badge-danger'><i class='fa fa-tags'></i> ". $memberInfo['FullName']. " Items</div> <hr>";
                                $member_items = getAllFrom('*' , 'items', '', '', 'WHERE Member_ID = '.$memberid.'','AND Approve = 1 ', 'Item_ID');

                                if(! empty($member_items))
                                {
                                    echo "<div class='row'>";
                                    foreach($member_items as $memberItem)
                                    {?>
                                            <!-- Check If Category Has Items Hidden Or Not  -->
                                        <?php if(checkStatus('Visibility','categories', 'ID',$memberItem['Cat_ID']) == 0){?>

                                            <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                                <div class="card item-box">
                                                        <span class="price-tag"><?php echo $memberItem['Price']; ?></span>
                                                        <?php 
                                                            if(empty($memberItem['Image']))
                                                            {
                                                                echo "<img class='img-thumbnail' src='admin/uploads/products/add.jpg' alt ='Item Image'/>";
                                                            }
                                                            else
                                                            {
                                                                echo "<img class='img-thumbnail' src= 'admin/uploads/products/".$memberItem['Image']."' alt='Item Image'>";
                                                            }
                                                        ?>
                                                        <div class="card-body pt-2">
                                                            <div <?php if(isset($_SESSION['user']) && checkStatus('RegStatus','users', 'UserID',$_SESSION['userid'] ) == 1) 
                                                                        { 
                                                                            echo 'id="rating-list" data-itemid="'.$memberItem["Item_ID"].'"';
                                                                        }
                                                                    ?> 
                                                                class='item-rating float-left'>
                                                                <?php
                                                                    $rate = getRate($memberItem["Item_ID"]);
                                                                    for ($count =1 ; $count <=5 ; $count++)
                                                                    {
                                                                        if($count <= $rate )
                                                                        {
                                                                            echo '<span class="rating-color" data-index="'.$count.'"><i class="far fa-star"></i></span>';
                                                                        }
                                                                        else
                                                                        {
                                                                            echo '<span data-index="'.$count.'"><i class="far fa-star"></i></span>';
                                                                        }
                                                                    }
                                                                ?>
                                                            </div>
                                                            <div class="item-date"><?php echo $memberItem['Add_Date']; ?></div>
                                                            <h3 class="card-title my-1"><a href="items.php?itemid=<?php echo $memberItem['Item_ID'];?>"><?php echo $memberItem['Name']; ?></a></h3>
                                                            <p class="card-text "><?php echo $memberItem['Description']; ?></p>
                                                            <span class='readMore-btn badge badge-danger'>Read More</span>
                                                        </div>
                                                </div>
                                            </div>

                                        <?php } 
                                            else
                                            {
                                                echo '<div class="item-box col-lg-3 col-md-4 col-sm-6 alert alert-warning alert-dismissible fade show" role="alert">';
                                                    echo '<strong> This Item Is Hidden By Admin </strong>';
                                                    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                                                        echo '<span aria-hidden="true">&times;</span>';
                                                    echo ' </button>';
                                                echo '</div>';
                                                
                                            }
                                        ?>
                                        

                                <?php }
                                    echo "</div>";
                                }
                                else
                                {
                                    echo "<div class='alert alert-danger text-center mb-0'>Ther\'s No Ads To Show,Create 
                                        <a href='newad.php' class='badge badge-danger badge-ad'>New Ad</a></div>";
                                } 
                        echo "</div>";
                    ?>
                <!-- End Show Member Items  -->
            <?php
                }
                else
                {
                    echo '<div class="member-info p-4">';
                        echo "<div class='alert alert-danger text-center'>";
                            echo "Sorry This Member is Not Exist !";
                        echo "</div>";
                    echo '</div>';
                }
            ?>
        </div>
        
<?php }
    // End Show Members Info 
    else
    {
        header("location: login.php");
        exit();
    }
?> 

<?php 
    include $tpl."footer.php"; 
    ob_end_flush();
?>
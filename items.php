<?php
    ob_start();
    session_start();
    $pageTitle="Show Item";
    include "init.php";
     //Check if Get Request Is Numeric &Get value Of It
     $itemid =  isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

     //Select All Data Depend On This ID 
     $stmt = $connection ->prepare("SELECT 
                                        items.* , categories.Name AS category_name, users.Username
                                    FROM 
                                        items
                                    INNER JOIN
                                        categories
                                    ON
                                        categories.ID = Cat_ID
                                    INNER JOIN
                                        users
                                    ON
                                        users.UserID = Member_ID
                                     WHERE 
                                        Item_ID = :itemid 
                                    AND 
                                        Approve = 1 ");



     $stmt ->bindparam(":itemid",$itemid);
     $stmt ->execute();
     $item = $stmt ->fetch();
     $count = $stmt ->rowCount();
     if($count > 0){
?>
           <!-- Start Show Item Page  -->
           <div class="container">
                <!-- Start Item Info  -->
                <div class="item-details">
                    <div class="page-header">
                        <h1 class="text-center">Item Details</h1>
                    </div>
                    <div class="show-item">
                        <div class='row'>
                            <!-- Start Show Item Image  -->
                            <div class='col-lg-3 col-md-4'>
                                <div class="item-pic">
                                    <?php 
                                        if(empty($item['Image']))
                                        {
                                            echo "<img class='img-thumbnail' src='admin/uploads/products/add.jpg' alt ='Item Image'/>";
                                        }
                                        else
                                        {
                                            echo "<img class='img-thumbnail' src= 'admin/uploads/products/".$item['Image']."' alt='Item Image'>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <!-- End Show Item Image  -->
                            
                            <!-- Start Item Details -->
                            <div class='col-lg-9 col-md-8'>
                                <div class="item-info">
                                    <h3 class="badge badge-danger"><?php  echo $item['Name'];?></h3>
                                    <div class="pl-2">
                                        <p class="my-1 desc-content"><?php  echo $item['Description'];?></p>
                                        <p><span class="mr-5"><i class="fa fa-money-bill-alt fa-fw"></i> Price</span> : <?php  echo $item['Price'];?></p>
                                        <p><span class="mr-4"><i class="fa fa-building fa-fw"></i> Made In</span> : <?php  echo $item['Country_Made'];?></p>
                                        <p><span><i class="fa fa-calendar fa-fw"></i>  Added Date</span> : <?php  echo $item['Add_Date'];?></p>
                                        <p><span class="mr-3"><i class="fa fa-tags fa-fw"></i> Category</span> :<a href="categories.php?catid=<?php echo $item['Cat_ID']; ?>"> <?php  echo $item['category_name'];?></a></p>
                                        <p><span  style="margin-right: 13px;"><i class="fa fa-user fa-fw"></i> Added By</span> :<a href="profile.php?memberid=<?php echo $item['Member_ID'];?> "> <?php echo $item['Username'];?></a></p>
                                        <p>
                                            <span class="tag-head"><i class="fas fa-hashtag"></i> Tags</span> :
                                            <?php 
                                                if(! empty($item['tags']))
                                                {
                                                    $allTags = explode(",",$item['tags']);
                                                    foreach($allTags as $tag)
                                                    {
                                                        $tag = str_replace(' ','',$tag);
                                                        $lowerTag = strtolower($tag);
                                                        echo "<a class='badge badge-primary show-tag' href = 'tags.php?name=".$lowerTag."'>".$tag."</a>";
                                                    }
                                                }
                                                else
                                                {
                                                    echo "This Item does not contain tags";
                                                }
                                            ?>
                                        </p>
                                        <p><span style="margin-right: 38px;">
                                            <i class="fas fa-star"></i> Rating
                                            </span> 
                                                : <strong><?php if(getRate($item["Item_ID"])> 0 ) {echo getRate($item["Item_ID"]);}else{echo "0";}  ?></strong>
                                                Out Of <strong>5</strong>
                                            </span>
                                        </p>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <!-- End Item Details -->
                        <hr>

                        <!-- Start Add Comment  -->
                        <?php if(isset($_SESSION['user'])) {?>
                            <div class="row">
                                <div class="col-md-6 offset-md-3">
                                    <div class="add-comment">
                                        <h5>Add Your Comment</h5>
                                        <?php  
                                        if(checkStatus('RegStatus','users','Username',$_SESSION['user']) == 1) 
                                        {
                                        ?>
                                            <!-- Check if This item Category Allow Comment Or Not  -->
                                            <?php 
                                            if(checkStatus('Allow_Comment','categories','ID',$item['Cat_ID']) == 0 ) 
                                            {?>
                                                <!-- Start Add Comment Validation  -->
                                                <?php
                                                    if(isset($_POST['add_comment']) && $_SERVER['REQUEST_METHOD'] == 'POST')
                                                    {
                                                        $comment = filter_var($_POST['comment'],FILTER_SANITIZE_STRING);
                                                        $itemid  = $item['Item_ID'];
                                                        $userid  = $_SESSION['userid'];

                                                        if(!empty($comment))
                                                        {
                                                            if(strlen($comment) > 50 )
                                                            {
                                                                echo "<div class='pr-4 alert alert-danger alert-dismissible fade show'>";
                                                                    echo " Comment Can't Be Greater Than <strong>50</strong> Chars  ";
                                                                    echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                                                                        echo "<span aria-hidden='true'>&times;</span>";
                                                                    echo "</button>" ;
                                                                echo "</div>";
                                                            }
                                                            else
                                                            {
                                                                $stmt = $connection ->prepare("INSERT INTO 
                                                                comments(`comment`, `comment_date`, `item_id`, `user_id`)
                                                                VALUES (:zcomment, NOW(), :zitemid ,:zuserid)");
        
                                                                $stmt ->execute(array(
                                                                    "zcomment"    => $comment,
                                                                    "zitemid"     => $itemid,
                                                                    "zuserid"     => $userid
                                                                ));
                                                                if($stmt)
                                                                {
                                                                    echo "<div class='alert alert-success'>";
                                                                        echo "Comment Added <span class='badge badge-warning'> Waiting Approve </span>";
                                                                    echo "</div>";
                                                                    // refresh page 
                                                                    header("refresh:2");
                                                                }
                                                            }
                                                        }
                                                        else
                                                        {
                                                            echo "<div class='alert alert-danger alert-dismissible fade show'>";
                                                                echo "Comment Must Be Not <strong>Empty</strong>";
                                                                echo "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>";
                                                                    echo "<span aria-hidden='true'>&times;</span>";
                                                                echo "</button>" ;
                                                            echo "</div>";
                                                        }
                                                    }
                                                ?>
                                                <!-- End Add Comment Validation  -->

                                                <form action="<?php echo $_SERVER['PHP_SELF'].'?itemid='.$item['Item_ID'] ?>" method="POST">
                                                    <textarea name="comment" class="comment-content d-block" cols="40" rows="4" required></textarea>
                                                    <input name="add_comment" class="btn btn-custom btn-sm" type="submit" value="Add Comment">
                                                </form>
                                            <?php 
                                            }
                                            else
                                            {
                                                echo '<div class="alert alert-warning" role="alert">';
                                                    echo "The Category of this product <strong> prevents comments ! </strong>";
                                                echo '</div>';
                                            }
                                            ?>
                                        <?php 
                                        }
                                        else
                                        {
                                            echo '<div class="alert alert-warning" role="alert">';
                                                echo "Your Account <strong>Waiting Approval! </strong>";
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- End Add Comment  -->
                        <?php } else{
                            echo "<p><a href='login.php'>Login</a> Or <a href='login.php'>Register</a> To Add Comment</p>"; 
                        }?>

                         <!-- Start Show Comments  -->
                         <?php
                            $Comments = getAllFrom('comments.* ,users.*' , 'comments', 'INNER JOIN users', 
                                                    'ON users.UserID = comments.user_id', ' WHERE Item_ID ='.$item['Item_ID'].' ',
                                                     'AND `status`= 1', 'c_id'
                                                   );

                            if(! empty($Comments))
                                    {
                                        echo "<hr>";
                                        echo "<div class='comm-header badge badge-pill'><i class='fa fa-comments'></i> Comments</div>";
                                        foreach($Comments as $comment)
                                        {?>
                                            <div class="comment-box">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="user-comment ">
                                                            <?php 
                                                                if(empty($comment['user_Avatar']))
                                                                {
                                                                    echo "<img class='img-fluid rounded-circle' src='admin/uploads/avatars/mask.jpg' alt ='User Avatar'/>";
                                                                }
                                                                else
                                                                {
                                                                    echo "<img src='admin/uploads/avatars/".$comment['user_Avatar']."' class='img-fluid rounded-circle' alt='User Avatar'>";
                                                                }
                                                            ?>
                                                            <span>
                                                                <a class="badge badge-secondary" href="profile.php?memberid= <?php echo $comment['user_id']?>">
                                                                    <?php echo $comment['Username']?>
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <p class="item-comment">
                                                            <?php echo $comment['comment']?>
                                                        </p>
                                                        <span class="comment-date"><?php echo $comment['comment_date']?></span>
                                                    </div>

                                                </div>
                                            </div><hr>
                                    <?php }
                                    }else
                                    {
                                        echo "<hr>";
                                        echo '<div class="alert alert-info text-center mb-0">This Item does not contain comments</div>';
                                    }?>

                         <!-- End Show Comments  -->
                    </div>
                </div>
           </div>



<?php 
    }else{
         echo  "<div class='container'><div class='page-header'><div class='alert alert-danger text-center'>
                    This Item is Not Exist Or This Item Waiting Approval
                </div></div></div>";
    }
    include $tpl."footer.php"; 
    ob_end_flush();
?>
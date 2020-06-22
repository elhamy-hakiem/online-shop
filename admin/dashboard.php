<?php 
    session_start(); 

    if(isset($_SESSION['Username']))
    {
        $pageTitle="Dashboard";
        include "init.php";

        // Number Of Latest  
        $latestNum = 4;

 ?>
<!-- start  Loading-->
<section id="loading">
    <div class="loading-content  d-flex align-items-center justify-content-center">
        <div class="lds-spinner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</section>
<!-- end  Loading-->

<!-- Start Stats  -->
<div class="home-stats text-center">
    <div class="container">
        <div class="card mt-4">
            <div class="card-body">
                <h1 >Dashboard</h1><hr>
                <div class="row">

                    <div class="col-md-3">
                        <div class="stat st-members">
                            <i class="fa fa-users"></i>
                            <div class="info">
                                Total Members
                                <span><a href="members.php"><?php echo countFunc('UserID','users'); ?></a></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat st-pending">
                            <i class="fa fa-user-plus"></i>
                            <div class="info">
                                Pending Members
                                <span><a href="members.php?action=Manage&status=Pending"><?php echo countFunc('RegStatus','users', 0 ); ?></a></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat st-items">
                            <i class="fa fa-tag"></i>
                            <div class="info">
                                Total Items
                                <span><a href="items.php"><?php echo countFunc('Item_ID','items'); ?></a></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat st-comments">
                            <i class="fa fa-comments"></i>
                            <div class="info">
                                Total Comments
                                <span><a href="comments.php"><?php echo countFunc('c_id','comments'); ?></a></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>        
    </div>
</div>
<!-- End Stats  -->

<!-- Start Latest  -->
<div class="latest my-4">
    <div class="container">
        <div class="row">
            <!-- Start Latest Users  -->
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-users"></i>
                            Latest <strong><?php echo $latestNum ;?></strong> Registerd Users
                            <span class="toggle-info float-right">
                                <i class="fa fa-minus fa-lg"></i>
                            </span>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled latest-users">
                           <?php 
                           //Get Latest Users
                           $stmt =$connection ->prepare("SELECT * FROM users WHERE GroupID != 1 ORDER BY UserID DESC LIMIT $latestNum");
                           $stmt ->execute();
                           $LatestUsers = $stmt->fetchAll();
                           if(! empty ($LatestUsers))
                           {
                                foreach($LatestUsers as $user)
                                {
                                    echo "<li>";
                                            echo "<span>".$user['Username']."</span>";
                                            echo "<a href='members.php?action=Edit&userid=".$user['UserID']."' class ='btn btn-success float-right'>";
                                                echo "<i class='fa fa-user-edit'></i> Edit";
                                            echo "</a>";
                                            echo "<a href='members.php?action=Delete&userid=".$user['UserID']."' class ='btn btn-danger float-right'>";
                                                echo "<i class='fa fa-user-times'></i> Delete";
                                            echo "</a>";
                                            if($user['RegStatus'] == 0)
                                            {
                                                echo "<a href='members.php?action=Activate&userid=".$user['UserID']."' class ='btn btn-info float-right'>";
                                                    echo "<i class='fa fa-check'></i> Activate";
                                                 echo "</a>"; 
                                            }
                                    echo "</li>";
                                }
                            }
                            else 
                            {
                                echo "<li class ='p-0'>";
                                    echo "<div class='alert alert-info text-center font-weight-bold mb-0'>Not Found Members</div>";
                                echo "</li>";
                            }
                           ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Latest Users  -->

            <!-- Start Latest Items  -->
            <?php  $LatestItems = getLatest('*' ,'items' ,'Item_ID' , $latestNum) ;   //Get Latest Items?>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-tag"></i>
                        Latest <strong><?php echo $latestNum ;?></strong> Items
                        <span class="toggle-info float-right">
                                <i class="fa fa-minus fa-lg"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled latest-users">
                            <?php 
                                if(! empty($LatestItems))
                                {
                                    foreach($LatestItems as $item)
                                    {
                                        echo "<li>";
                                                echo "<span>". $item['Name']."</span>";
                                                echo "<a href='items.php?action=Edit&itemid=".$item['Item_ID']."' class ='btn btn-success float-right'>";
                                                    echo "<i class='fas fa-edit'></i> Edit";
                                                echo "</a>";
                                                echo "<a href='items.php?action=Delete&itemid=".$item['Item_ID']."' class ='btn btn-danger float-right'>";
                                                    echo "<i class='fas fa-trash-alt'></i> Delete";
                                                echo "</a>";
                                                if($item['Approve'] == 0)
                                                {
                                                    echo "<a href='items.php?action=Approve&itemid=".$item['Item_ID']."' class ='btn btn-info float-right'>";
                                                        echo "<i class='fa fa-check'></i> Approve";
                                                        echo "</a>"; 
                                                }
                                        echo "</li>";
                                    }
                                }
                                else 
                                {
                                    echo "<li class ='p-0'>";
                                        echo "<div class='alert alert-info text-center font-weight-bold mb-0'>Not Found Items</div>";
                                    echo "</li>";
                                }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Latest Items  -->

        </div>

        <!-- Start Latest Comments  -->
        <div class="row mt-3 mb-4">
            
            <!-- Start Latest Comments  -->
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <i class="far fa-comments"></i>
                        Latest <strong><?php echo $latestNum ;?></strong> Comments
                        <span class="toggle-info float-right">
                                <i class="fa fa-minus fa-lg"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php 
                                //get latest comments
                                $stmt = $connection ->prepare("SELECT comments.* ,users.Username As Member
                                                               FROM  comments
                                                               INNER JOIN users
                                                               ON users.UserID = comments.user_id
                                                               ORDER BY c_id DESC
                                                               LIMIT $latestNum ");
                                $stmt ->execute();
                                $Comments = $stmt ->fetchAll();
                                if(! empty($Comments))
                                {
                                    foreach($Comments as $comment)
                                    {?>
                                        <div class="latestComment-body">
                                                <span class="user-comment ">
                                                    <a class="badge badge-info" href="members.php?action=Edit&userid=<?php echo $comment['user_id']?>">
                                                        <?php echo $comment['Member']?>
                                                    </a>
                                                </span>
                                                <p class="latest-comment">
                                                    <?php echo $comment['comment']?>
                                                </p>

                                            <a href='comments.php?action=Edit&comid=<?php echo $comment['c_id'] ?>' class ='btn btn-success float-right'>
                                                <i class='fas fa-edit'></i> Edit
                                           </a>
                                           <a href='comments.php?action=Delete&comid=<?php echo $comment['c_id'] ?>' class ='btn btn-danger float-right'>
                                                <i class='fas fa-trash-alt'></i> Delete
                                            </a>
                                            <?php if($comment['status'] == 0)
                                            { ?>
                                                <a href='comments.php?action=Approve&comid=<?php echo $comment['c_id'] ?>' class ='btn btn-info float-right'>
                                                    <i class='fa fa-check'></i> Approve
                                                </a> 
                                            <?php }?>
                                            <div class="clearfix"></div>
                                        </div><hr>
                                       
                                   <?php }
                                }
                                else 
                                {
                                     echo "<div class='alert alert-info text-center font-weight-bold mb-0'>Not Found Comments</div>";
                                }
                               
                           ?>
                    </div>
                </div>
            </div>
            <!-- End Latest Comments  -->

        </div>
        <!-- End Latest Comments  -->

    </div>
</div>
<!-- End Latest  -->

        <?php
        include $tpl."footer.php";
    }
    else
    {
        header("Location: index.php");
        exit();
    }
<?php

/*
====================================================================
==  Manage Cooments Page
==  You can   Edite || Delete || Approve Comments From Here
====================================================================
*/

ob_start();   // Output Buffering Start 

session_start();

$pageTitle = "Comments";

if(isset($_SESSION['Username']))
{
    include "init.php";

    $action = isset($_GET['action']) ? $_GET['action'] : 'Manage';

    
// Start Manage Page
    if($action == 'Manage')
    {
        //Fetch All Comments From Database
        $stmt = $connection ->prepare("SELECT 
                                            comments.* , items.Name AS Item_Name , users.Username 
                                        FROM 
                                            comments 
                                        INNER JOIN
                                            items
                                        ON
                                            items.Item_ID = comments.item_id
                                        INNER JOIN
                                            users
                                        ON
                                            users.UserID = comments.user_id
                                        ORDER BY
                                            c_id
                                        DESC
                                        ");    
        $stmt ->execute();
        //Assign Comments In Variable
        $comments = $stmt ->fetchAll();
        //Check If Found Comments Or No
        $count = $stmt ->rowCount();
    ?>
    <!-- Start Manage Page Design -->
    <div class="container">
        <div class="card my-4">
            <div class="card-body">
                <h1 class="edit-header text-center py-3">Manage Comments</h1><hr>
                <div class="table-responsive">
                    <table class="table text-center table-bordered">
                        
                        <?php if($count > 0) {?>
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Comments</th>
                                <th>Item Name</th>
                                <th>User Name</th>
                                <th>Added Date</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach($comments as $comment){
                                echo "<tr>";
                                    echo "<td>".$comment['c_id']."</td>";
                                    echo "<td class='desc-content'>".$comment['comment']."</td>";
                                    echo "<td>".$comment['Item_Name']."</td>";
                                    echo "<td>".$comment['Username']."</td>";
                                    echo "<td>".$comment['comment_date']."</td>";
                                    echo "<td>
                                            <a href='comments.php?action=Edit&comid=".$comment['c_id']."' class='btn btn-success edite-btn'><i class='fa fa-edit'></i> Edit</a>
                                            <a href='comments.php?action=Delete&comid=".$comment['c_id']."' class='btn btn-danger delete-btn'><i class='fas fa-times'></i> Delete</a>";
                                            if($comment['status'] == 0)
                                            {
                                                echo "<a href='comments.php?action=Approve&comid=".$comment['c_id']."' class='btn btn-info ml-2 active-btn'><i class='fa fa-check'></i> Approve </a>";
                                            }
                                    echo "</td>";
                                echo "</tr>";
                            }?>
                        </tbody>
                    <?php 
                        }else{echo "<div class='alert alert-danger text-center font-weight-bold'>Not Found Comments</div>";} ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End Manage Page Design -->
    <?php }
// End Manage Page

//Start Edite Page 
    elseif($action == 'Edit'){

        echo "<div class='container'>
                <div class='card mt-5'>
                    <div class='card-body'>
                        <h1 class='edit-header text-center py-3'>Edit Comment</h1><hr>";
        //Check if Get Request Is Numeric &Get value Of It
        $comid = isset($_GET['comid']) && is_numeric($_GET['comid']) ? intval($_GET['comid']) : 0 ;

        //Select All Data Depend On This ID 
        $stmt = $connection -> prepare("SELECT * FROM `comments` Where `c_id` = ? ");
        $stmt ->execute(array($comid));
        $row  = $stmt -> fetch();
        $count = $stmt ->rowCount();
        if($count > 0 ){?>
                        <!-- Start Edit Form  -->
                        <form class="p-3" action="?action=Update" method="POST">
                            <input type="hidden" name="comid" value="<?php echo $comid ;?>">

                            <!-- Start Comment Field  -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Comment : </label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="comment" id="" cols="30" rows="10"><?php echo $row['comment'] ?></textarea>
                                </div>
                            </div>
                            <!-- End Comment Field  -->
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-success "><i class="fas fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                        <!-- End Edit Form  -->
    <?php 
        }else{
            $theMsg = "<div class='alert alert-danger text-center font-weight-bold'>This Comment is Not Exist! </div>";
            redirectHome($theMsg,'back');
        }
        echo "</div></div></div>";
    }
//End Edite Page

//Start Update Page
    elseif($action == 'Update'){ 

    echo " <div class='container'><div class='card my-5'><div class='card-body'>
            <h1 class='edit-header text-center py-3'> Update Comment </h1><hr>";
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $comid        = filter_var($_POST['comid'],FILTER_SANITIZE_NUMBER_INT);
                $comment  = filter_var($_POST['comment'],FILTER_SANITIZE_STRING);

                if(! empty($comment))
                {
                    //Update The Database With This Info
                    $stmt = $connection->prepare("UPDATE `comments` SET  `comment`= ?  WHERE `c_id`= ? ");
                    $stmt ->execute(array($comment,$comid));

                    //Show Success Message
                    $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Comment Updated</div>";
                    redirectHome($theMsg,'back');
                }
                else
                {
                    //Show Error Message
                    $theMsg = "<div class='alert alert-danger text-center'>Comment Can't be <strong> Empty </strong></div>";
                    redirectHome($theMsg,'back');
                }

            }
            //Show Error Message
            else{
                $theMsg= "<div class='alert alert-danger text-center'>Sorry You Cant Browse This Page Directory !</div>";
                redirectHome($theMsg);
            }
        echo "</div></div></div>";
        
    }
//End Update Page

//Start Delete Page
    elseif($action == 'Delete')
    {
        echo " <div class='container'>
                    <div class='card mt-5'>
                        <div class='card-body'><h1 class='edit-header text-center py-3'>Delete Comment</h1><hr>";
                        //Check if Get Request Is Numeric &Get value Of It
                        $comid = isset($_GET['comid'])&& is_numeric($_GET['comid']) ? intval($_GET['comid']) :0;

                        //Select All Data Depend On This ID 
                        // replace CheckItem -> CountFunc 
                        $check = countFunc('c_id','comments',$comid);
                        // If Id Exist Delete It 
                        if($check > 0 ){
                            $stmt= $connection-> prepare("DELETE FROM `comments` WHERE `c_id` = :comment_id");
                            $stmt->bindParam(":comment_id",$comid);
                            $stmt->execute();  
                            $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Comment Deleted</div>";
                            redirectHome($theMsg,'back');
                        }else{
                            $theMsg = "<div class='alert alert-danger text-center'>This Comment Is Not Exist ! </div>";
                            redirectHome($theMsg);
                        }
        echo "</div></div></div>";
    }
// End Delete Page

//Start Approve Page
    elseif($action == 'Approve')
    {
        echo " <div class='container'>
        <div class='card mt-5'>
            <div class='card-body'><h1 class='edit-header text-center py-3'>Approve Comment</h1><hr>";
            //Check if Get Request Is Numeric &Get value Of It
            $comid = isset($_GET['comid'])&& is_numeric($_GET['comid']) ? intval($_GET['comid']) :0;

            //Select All Data Depend On This ID 
            // replace CheckItem -> CountFunc 
            $check = countFunc('c_id','comments',$comid);
            // If Id Exist Delete It 
            if($check > 0 ){
                $stmt= $connection-> prepare("UPDATE `comments` SET `status`= 1 WHERE `c_id`= ?");
                $stmt->execute(array($comid));  
                $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Comment Approved</div>";
                redirectHome($theMsg,'back');
            }else{
                $theMsg = "<div class='alert alert-danger text-center'>This Comment Is Not Exist ! </div>";
                redirectHome($theMsg);
            }
    echo "</div></div></div>";  
    }
// End Approve Page 
else
{
    $theMsg = "<div class='alert alert-danger text-center font-weight-bold mt-3'>This Page Is Not Found !</div>";
    redirectHome($theMsg);
}




// Start Footer
include $tpl."footer.php";
}
else
{
//If No Session Redirect to login form
header("Location: index.php");
exit();
}
ob_end_flush();
?>
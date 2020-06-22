<?php

/*
====================================================================
==  Manage Members Page
==  You can Add || Edite || Delete || Activate Members From Here
====================================================================
*/
ob_start();

session_start(); 
$pageTitle = 'Members';

if(isset($_SESSION['Username']))
{
    include "init.php";

    $action = isset($_GET['action']) ? $_GET['action'] : 'Manage';

// Start Manage Page
    if($action == 'Manage')
    {
        // Start Method Pagination 
        $limit = isset($_POST['limit-records']) ? $_POST['limit-records'] : 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
        $start = ($page - 1 )* $limit;
        $getcount = $connection ->prepare ('SELECT COUNT(UserID) AS userCount FROM users  WHERE GroupID !=1');
        $getcount ->execute();
        $usersCount = $getcount ->fetch();
        $total = intval($usersCount['userCount']);
        $pages = ceil($total / $limit);
        $prevPage = $page - 1;
        $nextPage = $page + 1;
        // End Method Pagination 

        // Check Pending Users 
        $pending =''; 
        if(isset($_GET['status']) && $_GET['status'] == 'Pending')
        {
            $pending ='AND `RegStatus` = 0';
        }
        //Fetch All Members From Database
        $stmt = $connection ->prepare("SELECT * FROM users WHERE GroupID !=1 $pending ORDER BY UserID DESC LIMIT $start , $limit");    
        $stmt ->execute();
        //Assign Members In Variable
        $members = $stmt ->fetchAll();
        //Check If Found Members Or No
        $count = $stmt ->rowCount();
    ?>
    <!-- Start Manage Page Design -->
    <div class="container">
        <div class="card my-5">
            <div class="card-body">
                <h1 class="edit-header text-center py-3">Manage Members</h1><hr>
                <!-- Choose Limit To Show  -->
                <div class='select-limit'>
                    <form method="POST" >
                        <span>show</span>
                        <select name="limit-records" id="limit-records">
                            <option disabled ='disabled' selected>limit</option>
                            <?php foreach([10,30,60,80] as $limit) { ?> 
                                <option <?php if( isset($_POST["limit-records"]) && $_POST["limit-records"] == $limit) {echo "selected";}?> value="<?php echo $limit; ?>">
                                    <?php echo $limit; ?>
                                </option>
                            <?php }?>
                        </select>
                        <span>Members</span>
                    </form>
                </div>
                <!-- Choose Limit To Show  -->
                <div class="table-responsive">
                    <table class="manage-members table text-center table-bordered">
                        
                        <?php if($count > 0) {?>
                        <thead class="thead-dark">
                            <tr>
                                <th>#ID</th>
                                <th>Avatar</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Registerd Date</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  foreach($members as $member){
                                echo "<tr>";
                                    echo "<td>".$member['UserID']."</td>";
                                    echo "<td class='avatar-body'>";
                                        if(empty($member['user_Avatar']))
                                        {
                                            echo "<img class='avatar' src='uploads/avatars/avatar.jpg' alt ='User Avatar'/>";
                                        }
                                        else
                                        {
                                            echo "<img class='avatar' src='uploads/avatars/".$member['user_Avatar']."' alt ='User Avatar'/>";
                                        }
                                    echo "</td>";
                                    echo "<td>".$member['Username']."</td>";
                                    echo "<td>".$member['Email']."</td>";
                                    echo "<td>".$member['FullName']."</td>";
                                    echo "<td>".$member['Date']."</td>";
                                    echo "<td>
                                            <a href='members.php?action=Edit&userid=".$member['UserID']."' class='btn btn-success edite-btn'><i class='fa fa-user-edit'></i> Edit</a>
                                            <a href='members.php?action=Delete&userid=".$member['UserID']."' class='btn btn-danger delete-btn'><i class='fas fa-user-times'></i> Delete</a>";
                                            if($member['RegStatus'] == 0)
                                            {
                                                echo "<a href='members.php?action=Activate&userid=".$member['UserID']."' class='btn btn-info ml-2 active-btn'><i class='fa fa-check'></i> Activate </a>";
                                            }
                                    echo "</td>";
                                echo "</tr>";
                            }?>
                        </tbody>
                    <?php 
                        }else{echo "<div class='alert alert-danger text-center font-weight-bold'>Not Found Members</div>";} ?>
                    </table>
                </div>
                <a href='members.php?action=Add' class="btn btn-primary btn-sm float-left"><i class="fa fa-plus"></i> New Member</a>
                <!-- Start pagination  -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination custom-pagination">
                            <li class="page-item  <?php if($prevPage < 1 ){echo "disabled";} ?>">
                                <a class="page-link" href="<?php echo "members.php?action=Manage&page=".$prevPage;?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>

                            <?php 
                                // Custom Pagination Number 
                                $customPageNum = 6;
                                if($pages <= 6)
                                {
                                    $customPageNum = $pages;
                                }
                                for ( $i =1 ; $i <= $customPageNum ; $i++ ){
                                    echo '<li class="page-item">';
                                        echo '<a class="page-link" href="members.php?action=Manage&page='.$i.'">';
                                            echo $i;
                                        echo'</a>';
                                    echo '</li>';
                                }
                            ?>
                            <li class="page-item <?php if($nextPage > $pages){echo "disabled";} ?>">
                                <a class="page-link" href="<?php echo "members.php?action=Manage&page=".$nextPage;?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span aria-hidden="true" class="sr-only">Next</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class='clearfix'></div>
                <!-- End pagination  -->
            </div>

        </div>
    </div>
   <!-- End Manage Page Design -->
  <?php }
// End Manage Page

//Start Add Page 
    elseif($action == 'Add')
    {?>
        
        <!-- Start Add Page Design     -->
        <div class="container">
            <div class="card mt-5">
                <div class="card-body">
                    <h1 class="edit-header text-center py-3">Add New Member</h1><hr>

                    <!-- Start Add Form  -->
                    <form class="p-3" action="?action=Insert" method="POST" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" autocomplete="off" required="required" placeholder="Email Must Be Valid">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Full Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="fullname" autocomplete="off" required="required" placeholder="Full Name Appear In Your Profile Page" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="username" autocomplete="off" required="required"  placeholder="Username To Login Into Shop ">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">User Avatar</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="userAvatar"  required="required">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="password form-control" name="password" autocomplete="new-password" required="required" placeholder="Password Must Be Hard & Complex ">
                                <i class="fa fa-eye show-pass"></i>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-success btn-sm"><i class='fa fa-plus'></i> Add Member</button>
                            </div>
                        </div>
                    </form>
                    <!-- End Add Form  -->

                </div>
            </div>
        </div>
    <!-- End Add Page Design     -->

    <?php }
//End Add Page

//Start Insert Page
    elseif($action == 'Insert')
    {
        echo " <div class='container'><div class='card my-5'><div class='card-body'>
        <h1 class='edit-header text-center py-3'>Insert Member</h1><hr>";
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            // Upload Variables 
            $userAvatar = $_FILES['userAvatar'];
            $avatarName = $_FILES['userAvatar']['name'];
            $avatarType = $_FILES['userAvatar']['type'];
            $avatarTmp = $_FILES['userAvatar']['tmp_name'];
            $avatarSize = $_FILES['userAvatar']['size'];
            // List Of Allowed File Type To Upload 
            $avatarAllowedExtension = array("jpeg","jpg","png","gif");

            //Get Avatar Extension
            $arrayName = explode('.',$avatarName);
            $avatarExtension = strtolower(end($arrayName));

            // Get Variables From The Form 
            $email     = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $fullname  = filter_var($_POST['fullname'],FILTER_SANITIZE_STRING);
            $username  = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $password   = filter_var($_POST['password'],FILTER_SANITIZE_STRING);

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
            if(empty($password))
            {
                $formErrors[] = "Password Cant Be <strong>Empty</strong>";
            }
            if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password))
            {
                $formErrors[] = "Password Must Be have at least one number and at least one letter and there have to be 8-12 characters ";
            }
            if(empty($avatarName))
            {
                $formErrors[] = "User Avatar Is  <strong>required</strong>";
            }
            if(! empty($avatarName) && ! in_array($avatarExtension,$avatarAllowedExtension))
            {
                $formErrors[] = "This Extension is Not <strong>Allowed</strong>";
            }
            if($avatarSize > 4194304)
            {
                $formErrors[] = "Avatar Can't Be Larger Than <strong>4MB</strong>";
            }
            // Loop Into Errors And Show Error 
            foreach($formErrors as $error)
            {
                echo "<div class='alert alert-danger text-center'>".$error."</div>";
            }
            

            if(empty($formErrors))
            {
                // check If User Name Already Exist In Database Or No
                // replace CheckItem -> CountFunc 
                $count =countFunc('Username','users',$username);
                if($count > 0 ){
                    $theMsg = "<div class='alert alert-danger text-center'>Sorry Username  Already Exist ! </div>"; 
                    redirectHome($theMsg,'back');
                }
                else
                {
                    // Edit User Avatar Name Before Insert In Database 
                    $avatar = rand(0,100000000).'_'.$avatarName;
                    move_uploaded_file($avatarTmp,"uploads\avatars\\".$avatar);

                    //Add New User In Database 
                    $stmt = $connection ->prepare("INSERT INTO `users`(`Username`, `password`, `Email`, `FullName`,`RegStatus`,`Date`,`user_Avatar`) 
                                                VALUES (:username, :pass, :email, :fullname , 1 , now(), :zavatar )");
                    $stmt ->execute(array(
                        'username'   => $username,
                        'pass'       => password_hash($password,PASSWORD_DEFAULT),
                        'email'      => $email,
                        'fullname'   => $fullname,
                        'zavatar'    => $avatar
                    ));

                    // // Show Success Message
                    $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Member Added</div>";
                    redirectHome($theMsg,'back');
                }
            }

        }
        //Show Error Message
        else{

            $theMsg= "<div class='alert alert-danger text-center'>Sorry You Cant Browse This Page Directory !</div>";
            redirectHome($theMsg);
        }
        echo "</div></div></div>";
    }
//End Insert Page

//Start Edite Page 
    elseif($action == 'Edit'){

        echo "<div class='container'>
                <div class='card mt-5'>
                    <div class='card-body'>
                        <h1 class='edit-header text-center py-3'>Edit Member</h1><hr>";
                        //Check if Get Request Is Numeric &Get value Of It
                        $userid = isset($_GET['userid'])&& is_numeric($_GET['userid']) ? intval($_GET['userid']) :0;

                        //Select All Data Depend On This ID 
                        $stmt = $connection -> prepare("SELECT * FROM `users` Where `UserID` = ? LIMIT 1");
                        $stmt ->execute(array($userid));
                        $row  = $stmt -> fetch();
                        $count = $stmt ->rowCount();
                        if($count > 0 ){?>
                        <!-- Start Edit Form  -->
                        <form class="p-3" action="?action=Update" method="POST">
                            <input type="hidden" name="userid" value="<?php echo $userid ;?>">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="<?php echo $row['Email'];?>" name="email"  autocomplete="off" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Full Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo $row['FullName'];?>" name="fullname"  autocomplete="off" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="<?php echo $row['Username'];?>" name="username"  autocomplete="off" required="required">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="hidden" value="<?php echo $row['password'];?>"  name="oldpassword">
                                    <input type="password" class="form-control" name="mypassword" placeholder="Leave Blank If You Dont Want To Change " autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                        <!-- End Edit Form  -->
    <?php 
        }else{
            $theMsg = "<div class='alert alert-danger text-center font-weight-bold'>This Member is Not Exist! </div>";
            redirectHome($theMsg,'back');
        }
        echo "</div></div>";

          // Start  Manage Items of This Category
                //Fetch All Items From Database
                $stmt = $connection ->prepare("SELECT 
                                                    items.*, 
                                                    users.Username
                                                FROM   
                                                    items
                                                INNER JOIN
                                                    users
                                                ON
                                                    users.UserID  = items.Member_ID
                                                WHERE Member_ID = ?
                                                ORDER BY
                                                    Item_ID
                                                DESC
                                                ");    
                $stmt ->execute(array($userid));
                //Assign Items In Variable
                $items = $stmt ->fetchAll();
                //Check If Found Items Or No
                $count = $stmt ->rowCount();
                ?>
                <!-- Start Manage Items of This Category Page Design -->
                <div class="card my-5">
                    <div class="card-body manage-items">
                        <h1 class="second-header text-center py-3">Manage [ <?php echo $row['Username'] ?> ] Items</h1><hr>
                        <div class="table-responsive">
                        <table class="table text-center table-bordered">

                            <?php if($count > 0) {?>
                            <thead class="thead-dark">
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Adding Date</th>
                                    <th>Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  foreach($items as $item){
                                echo "<tr>";
                                    echo "<td>";
                                    if(empty($item['Image']))
                                    {
                                        echo "<img class='item-img' src='uploads/products/add.jpg' alt ='Item Image'/>";
                                    }
                                    else
                                    {
                                        echo "<img class='item-img' src= 'uploads/products/".$item['Image']."' alt='Item Image'>";
                                    }
                                    echo "</td>";
                                    echo "<td>".$item['Name']."</td>";
                                    echo "<td class='desc-content'>".$item['Description']."</td>";
                                    echo "<td>".$item['Price']."</td>";
                                    echo "<td>".$item['Add_Date']."</td>";
                                    echo "<td>
                                            <a href='items.php?action=Edit&itemid=".$item['Item_ID']."' class='btn btn-success edite-btn'><i class='fa fa-user-edit'></i> Edit</a>
                                            <a href='items.php?action=Delete&itemid=".$item['Item_ID']."' class='btn btn-danger delete-btn'><i class='fa fa-trash'></i> Delete</a>";
                                            if($item['Approve'] == 0)
                                            {
                                                echo "<a href='items.php?action=Approve&itemid=".$item['Item_ID']."' class='btn btn-info ml-2 active-btn'><i class='fa fa-check'></i> Approve</a>";
                                            }
                                    echo "</td>";
                                echo "</tr>";
                                }?>
                            </tbody>
                            <?php 
                            }else{echo "<div class='alert alert-danger text-center font-weight-bold'>Not Found <span class='badge badge-pill badge-danger'> Items </span></div>";} ?>
                        </table>
                        </div>
                    </div>
                </div>
                <!-- End Manage Items of This Category Page Design --> 
        <?php  echo "</div>";
    }
//End Edite Page
    
//Start Update Page
    elseif($action == 'Update'){ 
    
    echo " <div class='container'><div class='card my-5'><div class='card-body'>
            <h1 class='edit-header text-center py-3'> Update Member </h1><hr>";
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $id        = filter_var($_POST['userid'],FILTER_SANITIZE_NUMBER_INT);
                $email     = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
                $fullname  = filter_var($_POST['fullname'],FILTER_SANITIZE_STRING);
                $username  = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
                $oldPass   = $_POST['oldpassword'];  //This Is my password In Database
                $newPassword = $_POST['mypassword'];

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
                    $formErrors[] = "Full Name Must Be Greater Than <strong>8 Characters</strong>";
                }

                if(empty($username))
                {
                    $formErrors[] = "Username Cant Be <strong>Empty</strong>";
                }
                if(strlen($username) < 5)
                {
                    $formErrors[] = "Username Must Be Greater Than  <strong>5 Characters</strong>";
                }
                if(! empty($newPassword))
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
                // Loop Into Errors And Show Error 
                foreach($formErrors as $error)
                {
                    echo "<div class='alert alert-danger text-center'>".$error."</div>";
                }
                

                if(empty($formErrors))
                {
                    //Check if username is exist or no
                    $stmt2 = $connection ->prepare("SELECT * FROM users WHERE Username = ? AND UserID != ?  "); 
                    $stmt2 ->execute(array($username,$id));
                    $count = $stmt2 ->rowCount();
                    if($count > 0)
                    {
                        //Show Error Message
                        $theMsg = "<div class='alert alert-danger text-center'>Username Is Already Exist</div>";
                        redirectHome($theMsg,'back');
                    }
                    else
                    {
                        //Update The Database With This Info
                        $stmt = $connection->prepare("UPDATE `users` SET  `Email`= ? ,`FullName`= ? ,`Username`= ?,`password`= ?  WHERE `UserID`= ? ");
                        $stmt ->execute(array($email, $fullname, $username, $newPassword, $id));

                        //Show Success Message
                        $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Member Updated</div>";
                        redirectHome($theMsg,'back');
                    }
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
                        <div class='card-body'><h1 class='edit-header text-center py-3'>Delete Member</h1><hr>";
                        //Check if Get Request Is Numeric &Get value Of It
                        $userid = isset($_GET['userid'])&& is_numeric($_GET['userid']) ? intval($_GET['userid']) :0;

                        //Select All Data Depend On This ID 
                        // replace CheckItem -> CountFunc 
                        $check = countFunc('UserID','users',$userid);
                        // If Id Exist Delete It 
                        if($check > 0 ){
                            $stmt= $connection-> prepare("DELETE FROM `users` WHERE `UserID` = :user_id");
                            $stmt->bindParam(":user_id",$userid);
                            $stmt->execute();  
                            $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Member Deleted</div>";
                            redirectHome($theMsg,'back');
                        }else{
                            $theMsg = "<div class='alert alert-danger text-center'>This Member Is Not Exist ! </div>";
                            redirectHome($theMsg);
                        }
        echo "</div></div></div>";
    }
// End Delete Page

//Start Activate Page
    elseif($action == 'Activate')
    {
        echo " <div class='container'>
        <div class='card mt-5'>
            <div class='card-body'><h1 class='edit-header text-center py-3'>Activate Member</h1><hr>";
            //Check if Get Request Is Numeric &Get value Of It
            $userid = isset($_GET['userid'])&& is_numeric($_GET['userid']) ? intval($_GET['userid']) :0;

            //Select All Data Depend On This ID 
            // replace CheckItem -> CountFunc 
            $check = countFunc('UserID','users',$userid);
            // If Id Exist Delete It 
            if($check > 0 ){
                $stmt= $connection-> prepare("UPDATE `users` SET `RegStatus`= 1 WHERE `UserID`= ?");
                $stmt->execute(array($userid));  
                $theMsg = "<div class='alert alert-success text-center'>". $stmt->rowCount() . " Member Activate</div>";
                redirectHome($theMsg,'back');
            }else{
                $theMsg = "<div class='alert alert-danger text-center'>This Member Is Not Exist ! </div>";
                redirectHome($theMsg);
            }
    echo "</div></div></div>";  
    }
// End Activate Page 
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
<?php 
    session_start();
    $noNavbar ='';
    $pageTitle="Login";       // Variable to Change Page Title
    $login_body = '';        //variable to add class name to login body
    if(isset($_SESSION['Username']))
    {
        header('Location: dashboard.php');
        exit();
    }
    include "init.php";

    // Start Login Validation 

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $username = filter_var($_POST['user'],FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'],FILTER_SANITIZE_STRING);

        //Check if the user exist in database
        $stmt = $connection -> prepare("SELECT `UserID`,`Username`,`password`
                                        FROM `users`
                                        WHERE `Username`= ? 
                                        AND `GroupID`= 1
                                        LIMIT 1 ");  
        $stmt ->execute(array($username));
        $row   = $stmt ->fetch();
        $count = $stmt ->rowCount();
       
        if($count > 0)
       {
           if(password_verify($password,$row['password']))
           {
                $_SESSION['Username'] = $username;     //Register Session UserName
                $_SESSION['ID'] = $row['UserID'];     //Register  Session ID
                header('Location: dashboard.php');   //redirect to admin dashboard page
                exit(); 
           }
           else
           {
                echo "<script>alert('Password is not correct');</script>";
           }
       }
       else
       {
           echo "<script>alert('user not exist');</script>";
       }


    }
    // End Login Validation
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

<!-- Start Login Form  -->
    <div class="container">
        <div class="login-form">
            <h2 class="login-header text-center font-weight-bold"> Admin<span class="text-danger"> / Login</span></h2>
            <form class=" p-4" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-group">
                    <label class="login-label">Username</label>
                    <input class="form-control" type="text" name="user" placeholder="Username" autocomplete="off"  >
                </div>

                <div class="form-group">
                    <label class="login-label">Password</label>
                    <input class="form-control" type="password" name="password" placeholder="password" autocomplete="new-password" >
                </div>  

                <div class="hint">Forgot Password? <a href="" class="forgetpass">Click Here</a></div><br>   
            
                <div class="form-group">
                    <input type="submit" class="btn btn-danger btn-block font-weight-bold" value="Login">
                </div>  

                <div class="text-center hint">Don't have an account? <a href="" class="newaccount">Create one</a></div>

            </form>
        </div>
    </div>
<!-- End Login Form  -->

<?php  include $tpl."footer.php"; ?>
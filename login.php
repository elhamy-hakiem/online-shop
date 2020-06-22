<?php  $pageTitle="login";
    ob_start();
    session_start();
    if(isset($_SESSION['user']))
    {
        header("location:index.php");
        exit();
    }
    include "init.php";
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        //Start User Login
        if( isset($_POST['login']) )
        {
            $loginErrors = array();
            $user = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
            $password = $_POST['password'];
 
            $stmt = $connection ->prepare("SELECT 
                                                `UserID`, `Username`, `Password`
                                            FROM 
                                                `users`
                                            WHERE
                                                `Username` = ?
                                            AND
                                                `GroupID` != 1");
            $stmt ->execute(array($user));
            $get = $stmt ->fetch();
            $count = $stmt->rowCount();

            if($count > 0)
            {
                if(password_verify($password,$get['Password']))
                {
                    $_SESSION['user'] = $user;
                    $_SESSION['userid'] = $get['UserID'];
                    header("location: index.php");
                    exit();
                }
                else
                {
                    $loginErrors[]= "<div class='form-error alert alert-danger text-center'> Password Is Not  <strong>Correct</strong> </div>";
                }
            }
            else
            {
                $loginErrors[]= "<div class='form-error alert alert-danger text-center'> Sorry Username Is Not  <strong>Exist</strong> </div>";
            }
        }
        //End User Login
        
        // start user signup  
        else
        {
            $formErrors =array();
            $username           = $_POST['username'];
            $password           = $_POST['password'];
            $confirm_password   = $_POST['confirm_password'];
            $email              = $_POST['email'];
            // start user validation 
            if(isset($username))
            {
                $filterUser = filter_var( $username ,FILTER_SANITIZE_STRING);
                if(strlen($filterUser) < 4)
                {
                    $formErrors[]='Sorry Username Must Be Greater Than <strong> 3 </strong> Characters';
                }
            }
            // End user validation 

            // start password validation 
            if(isset($password) && isset( $confirm_password))
            {
                if(empty($password))
                {
                    $formErrors[]="Sorry Password Can't Be <strong>Empty</strong>";
                }
                if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password))
                {
                    $formErrors[] = "Password Must Be have at least one number and at least one letter and there have to be 8-12 characters  ";
                }
                if(! password_verify($confirm_password,password_hash($password,PASSWORD_DEFAULT)))
                {
                    $formErrors[]="Sorry Password Is Not  <strong>Match</strong>";
                }
            }
            // End password validation 

            // start Email validation 
            if(isset( $email ))
            {
                $filteredEmail = filter_var( $email ,FILTER_SANITIZE_EMAIL);
                if(filter_var($filteredEmail,FILTER_VALIDATE_EMAIL) != true )
                {
                    $formErrors[]='This Email Is Not <strong>Valid</strong> ';
                }
            }
            // End user validation 

            // Check If No Errors Insert User In Database
            if(empty($formErrors))
            {
                //check if username already exist
                $check = countFunc('Username','users',$username);
                if($check > 0)
                {
                    $formErrors[]="Username Already Exist ";
                }
                else
                {
                    //Insert User Info In Database
                    $stmt = $connection ->prepare("INSERT INTO 
                                                    users(`Username`,`Password`,`Email`,`RegStatus`,`Date`)
                                                   VALUES(:zuser, :zpass, :zemail, 0, now() )");
                    $stmt ->execute(array(
                        'zuser'    => $username,
                        'zpass'    => password_hash($password,PASSWORD_DEFAULT),
                        'zemail'   => $email
                    ));

                    //show success message
                    $success_msg = "Successfuly Registered";
                }
            }



        }
        // End  user signup
    }

?>

<div class="container">
    <div class="header-content">
        <h1>
            <span class="login path-active">Login</span> / <span class="signup">Signup</span>
        </h1>
    </div>
    <!-- Start Show Errors  -->
    <?php

        if(! empty($loginErrors))
        {
            foreach($loginErrors as $loginError)
            {
                echo $loginError;
            }
            //refresh page 
            header('refresh:2');
        }
        if(! empty($formErrors))
        {
            echo '<div class="form-error alert alert-danger text-center">';
                foreach($formErrors as $error)
                {?>
                    <div class="error">
                        <?php echo $error; ?>
                    </div>
                <?php }
            echo ' </div>';
            //refresh page 
            header('refresh:2');
        }
        else
        {
            if(isset($success_msg))
            {
                echo '<div class="form-error alert alert-success text-center">';  
                    echo '<div>'.$success_msg.'</div>'; 
                echo ' </div>';
                //refresh page 
                header('refresh:2');
            }
        }    
    ?>
    <!-- End Show Errors  -->

    <div class="card login-card">
        <img src="layout/images/logo4.png" class="login-logo" alt="...">
        <div class="card-body">
            <!-- Start Login Form  -->
            <form class="login-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">

                <div class="form-group">
                    <input type="text" class="form-control" name="username" autocomplete="off" placeholder="Username" required>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control password" name="password" autocomplete="new-password" placeholder="Password" required>
                    <i class="fa fa-eye show-pass"></i>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Remember me</label>
                    <a href="resetPassword.php" class="float-right">Forget Password</a>
                    <div class="clearfix"></div>
                </div>

                <button type="submit" name="login" class="btn btn-danger btn-login">Login</button>
            </form>
            <!-- End Login Form  -->

            <!-- Start signup Form  -->
            <form class="signup-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" autocomplete="off" placeholder="Username" 
                    pattern =".{4,12}" title="Username Must Be Between 4 & 15 chars" required>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password" autocomplete="new-password" placeholder="Password" minlength="8" required>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="confirm_password" autocomplete="new-password" placeholder="Confirm Password" minlength="8" required>
                </div>

                <div class="form-group">
                    <input type="email" class="form-control" name="email"  placeholder="Type Valid Email">
                </div>

                <button type="submit" name="signup" class="btn btn-danger btn-login">SignUp</button>
            </form>
            <!-- End signup Form  -->
        </div>
    </div>
</div>


<?php include $tpl."footer.php"; ?>
<?php ob_end_flush(); ?>
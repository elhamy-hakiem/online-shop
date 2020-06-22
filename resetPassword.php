<?php 

$pageTitle="Reset Password";
ob_start();
session_start();
if(isset($_SESSION['user']))
{
    header("location:index.php");
    exit();
}

else
{
    require 'includes/libraries/PHPMailer/PHPMailerAutoload.php';
    include "init.php";

    if(isset($_GET['code']))
    {
        $code =$_GET['code'];
        $getEmail = $connection->prepare("SELECT email FROM resetpasswords WHERE code = ? LIMIT 1");
        $getEmail ->execute(array($code));
        $usermail = $getEmail ->fetch();
        if(empty($usermail))
        {
            echo "<div class='container'><div class='page-header pt-4'>
                    <div class='alert alert-danger text-center'>
                        <span class='badge badge-pill badge-danger'>
                            <i style='font-size:17px;' class='fa fa-exclamation-circle' aria-hidden='true'></i>
                        </span>
                        This Page Is Not Found 
                    </div>
                  </div></div>";
                  header("refresh:3;url=login.php");
        }
        else
        {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changePass']))
            {
                $password = $_POST['password'];
                $confirm_password   = $_POST['confirm_password'];

                $errors = array();
                // start password validation 
                if(isset($password) && isset( $confirm_password))
                {
                    if(empty($password))
                    {
                        $errors[]="Sorry Password Can't Be <strong>Empty</strong>";
                    }
                    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password))
                    {
                        $errors[] = "Password Must Be have at least one number and at least one letter and there have to be 8-12 characters  ";
                    }
                    if(! password_verify($confirm_password,password_hash($password,PASSWORD_DEFAULT)))
                    {
                        $errors[]="Sorry Password Is Not  <strong>Match</strong>";
                    }
                }
                // End password validation 
                if(empty($errors))
                {
                    $hashedPass = password_hash($password,PASSWORD_DEFAULT);
                    $changePass = $connection ->prepare("UPDATE users SET `password` = ?  WHERE `Email` = ? ");
                    $changePass ->execute(array($hashedPass,$usermail['email']));

                    if($changePass)
                    {
                        $deleteCode = $connection ->prepare("DELETE FROM  resetpasswords WHERE code = ? ");
                        $deleteCode ->execute(array($code));
                        $success_msg = "Your Password Changed Now ";
                        header("refresh:3;url=login.php");
                    }
                    else
                    {
                        echo "<div class='container'><div class='page-header pt-4'>
                                <div class='alert alert-danger text-center'>
                                    <span class='badge badge-pill badge-danger'>
                                        <i style='font-size:17px;' class='fa fa-exclamation-circle' aria-hidden='true'></i>
                                    </span>
                                    some thing went <strong> Wrong </strong>
                                </div>
                        </div></div>";
                        header("refresh:3");
                    }
                }

            }
?>
            <div class="container">
                    <div class="header-content mb-2">
                        <h1 class='text-center mt-2'>
                            Change Password
                        </h1>
                    </div>
                    <?php
                        if(! empty($errors))
                        {
                            echo '<div class="mb-0 alert alert-danger text-center">';
                                foreach($errors as $error)
                                {?>
                                    <div class="error">
                                        <?php echo $error; ?>
                                    </div>
                                <?php }
                            echo ' </div>';
                            //refresh page 
                            header('refresh:2');
                        }
                        if(isset($success_msg) && ! empty($success_msg))
                        {
                            echo '<div class="mb-0 alert alert-success text-center">';
                                    echo $success_msg;
                            echo '</div>';
                            header("refresh:3;url=login.php");
                        }

                    ?>
                    <div class="card login-card mt-3">
                        <img src="layout/images/logo4.png" class="login-logo" alt="...">
                        <div class="card-body">
                            <!-- Start Login Form  -->
                            <form class="login-form" action="" method="POST">

                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" autocomplete="off" placeholder="New Password" minlength="8" required>
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control" name="confirm_password" autocomplete="new-password" placeholder="Confirm Password" minlength="8" required>
                                </div>
                    
                                <button type="submit" name="changePass" class="btn btn-danger btn-login">Save</button>
                            </form>
                            <!-- End Login Form  -->
                        </div>
                    </div>
                </div>
<?php 
        }
    }
    else
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resetPass']))
        {
            $email = filter_var($_POST['email'] , FILTER_SANITIZE_EMAIL);

            $resetError = '';
            if(empty($email))
            {
                $resetError = "Sorry Email Can't Be <strong>Empty</strong>";
            }
            else
            {
                if(filter_var($email,FILTER_VALIDATE_EMAIL) != true )
                {
                    $resetError='This Email Is Not <strong>Valid</strong> ';
                }
                else
                {
                    //Check if email is Exist Or not
                    $check_Email = countFunc('Email','users' , $email);

                    if($check_Email == 1)
                    {
                        // Code Use For Reset Password 
                        $code =bin2hex(random_bytes(22));
                        $stmt = $connection ->prepare("INSERT INTO `resetpasswords`(`code`, `email`) VALUES (:zcode , :zmail)");
                        $stmt ->execute(array(
                            'zcode'   => $code,
                            'zmail'   =>$email
                        ));
                        // Get User Name  From Database
                        $getName = $connection ->prepare("SELECT FullName FROM users WHERE Email = ? LIMIT 1");
                        $getName ->execute(array($email));
                        $user = $getName ->fetch();

                        // Start Send Mail To Reset Password 
                            $mail = new PHPMailer;
                                            
                            //Server settings
                            $mail->isSMTP();                                            // Send using SMTP
                            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication

                            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
                            $mail->Username   = 'onlineshopservices6@gmail.com';       // SMTP username
                            $mail->Password   = 'onlineshop16498';                    // SMTP password
                            $mail->SMTPSecure ='TLS';                                 // Enable Tls encryption;
                            $mail->Port       = 587;                                  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                            //Recipients
                            $mail->setFrom('onlineshopservices6@gmail.com', 'ONLINE SHOP');
                            $mail->addAddress($email, $user['FullName']);                // Add a recipient
                            $mail->addReplyTo('onlineshopservices6@gmail.com', 'ONLINE SHOP');


                            // Content
                            $url = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/resetPassword.php?code=$code";
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = 'Reset Password - ONLINE SHOP';
                            $mail->Body    = '
                                <div style="text-align:center; background-color:#BF1415; padding:20px;">
                                    <img style="width:250px; height:250px; border-radius:20px;" src="https://image.freepik.com/free-vector/online-shop-logo_18099-275.jpg" " alt="logo">
                                    <p style="color:#fff; font-size:15px;">
                                        Hello '.$user['FullName'].' Now You Can Reset Password From Here 
                                    </p>
                                    <a style="background-color: #fbfafa;color:#BF1415;padding:10px; font-weight:bold; border-radius:10px" href="'.$url.'">Reset Password</a>
                                </div>
                            ';

                            if($mail->send())
                            {
                                $successMsg = " Message has been sent  Check Your Email Now ";
                            }
                            else
                            {
                                $resetError=" Some thing went <strong> Wrong </strong> ";
                            }
                        // End Send Mail To Reset Password 
                    }
                    else
                    {
                        $resetError = " This Email is Not <strong> Exist </strong> ";
                    }
                }
            }
        }
            
?>
        <div class="container">
            <div class="header-content mb-3">
                <h1 class='text-center mt-2'>
                    Reset Password
                </h1>
            </div>
            <!-- Start Show Errors Or Succescc Messages  -->
            <?php

                if(isset($resetError) && ! empty($resetError))
                {
                    echo '<div class="mb-0 alert alert-danger text-center">';
                            echo "<span class='badge badge-pill badge-danger'>";
                                echo "<i style='font-size:17px;' class='fa fa-exclamation-circle' aria-hidden='true'></i>";
                            echo "</span>";
                            echo $resetError;
                    echo ' </div>';
                    //refresh page 
                    header('refresh:2');
                }
                if(isset($successMsg) && ! empty($successMsg))
                {
                    echo '<div class="mb-0 alert alert-success text-center">';
                            echo $successMsg;
                    echo '</div>';
                    header("refresh:3;url=login.php");
                }

            ?>
            <!-- End Show Errors Or Succescc Messages  -->
            <div class="card login-card mt-3">
                <img src="layout/images/logo4.png" class="login-logo" alt="...">
                <div class="card-body">
                    <!-- Start Login Form  -->
                    <form class="login-form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" autocomplete="off" placeholder="Type your Email Address">
                        </div>
            
                        <button type="submit" name="resetPass" class="btn btn-danger btn-login">Send</button>
                    </form>
                    <!-- End Login Form  -->
                </div>
            </div>
        </div>
<?php 
    }
} 
?>



<?php include $tpl."footer.php"; ?>
<?php ob_end_flush(); ?>
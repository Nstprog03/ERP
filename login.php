<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Include config file
require_once "db.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

$suspend_err="";


 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty($_POST["username"])){
        $username_err = "Please enter username.";
    } else{
        $username = $_POST["username"];
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = $_POST["password"];
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password,name,email,user_type,user_status FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password,$name,$email,$user_type,$user_status);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password))
                        {

                            //check is user is suspend or not
                            if($user_status=='1') // if not suspend
                            {
                                    // Password is correct, so start a new session
                                    session_start();
                                    
                                    // Store data in session variables
                                    $_SESSION["loggedin"] = true;
                                    $_SESSION["id"] = $id;

                                    
                                   //this variables have user id to store in all table when create & update record
                                    $_SESSION["username"] = $id;

                                    //this variblae have username
                                    $_SESSION["user_name"] = $username;


                                     //this users details
                                    $_SESSION["name"] = $name;
                                    $_SESSION["email"] = $email;
                                    $_SESSION["user_type"] = $user_type;
                                    
                                    
                                    // Redirect user to welcome page
                                    header("location: index.php");
                            }
                            else // if suspend
                            {

                                // Display an error message if account is suspend
                                $suspend_err = "This Account is Suspended...";


                            }



                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel - Jivandhara Database</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

        <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="/style4.css">
    <link rel="stylesheet" href="/css/custom.css">

    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <style type="text/css" media="screen">
        input.login100-form-btn {
    background: transparent;
}
body
{

        font-family: 'Poppins', sans-serif;
    background: #fafafa;
}
.form-group .fa-user, .form-group .fa-key {
    color: #6e96ed;
    margin-right: 3px;
}
span.help-block {
    color: black;
    font-size: 13px;
}

.form-group {
    margin-bottom: 0;
}
        
    </style>
<!--===============================================================================================-->
</head>
<body>
    
    <div class="limiter">
        <div class="container-login100" style="background-image: url('image/login-bg.jpeg');">
            <div class="wrap-login100 p-l-55 p-r-55 p-t-35 p-b-54">
                
                <div class="login-icon"><img src="image/jivandhara-logo-login.png" height="350" width="350" alt="Jivandhara Navjivan"></div>
                <form class="login100-form validate-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <!--<i class="fa fa-user" aria-hidden="true"></i>
                <label class="label-input100">Username</label>-->

                <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo $username; ?>">
            
                <span class="help-block"><?php echo $username_err; ?></span>

            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <!--<i class="fa fa-key" aria-hidden="true"></i>
                <label class="label-input100">Password</label>-->
                <input type="password" name="password" placeholder="Password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
                 <span class="help-block"><?php echo $suspend_err; ?></span>
            </div>


                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                               <input type="submit" class="login100-form-btn" value="Enter">
                            </button>
                        </div>
                    </div>
        </form>



                 <!--   <form class="login100-form validate-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <span class="login100-form-title p-b-49">
                        Login
                    </span>

                        <div class="wrap-input100 validate-input m-b-23 <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                        <span class="label-input100">Username</span>
                        <input class="input100" type="text" name="username" value="<?php echo $username; ?>">
                        <span class="help-block"><?php echo $username_err; ?></span>
                        <span class="focus-input100" data-symbol="&#xf206;"></span>                   
                
                    </div>

                    <div class="wrap-input100 validate-input <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>" data-validate="Password is required">
                        <span class="label-input100">Password</span>
                        <input class="input100" type="password" name="pass" placeholder="Type your password">
                        <span class="help-block"><?php echo $password_err; ?></span>
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
                    </div>
                    
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button class="login100-form-btn">
                                Login
                            </button>
                        </div>
                    </div>

                </form>-->



            </div>
           <div class="copyright-wrapper">
            <div class="maintain">Design &amp; Developed By <a href="https://www.madniinfoway.com"><img src="image/madni-logo.png" width="50" height="50" alt="Madni Infoway">Madni Infoway</a>
            </div>
            <div class="copyjivan">Copyright &copy;  2022 <a href="https://www.jivandhara-navjivan.com" target="_blank">Jivandhara &amp; Navjivan Cotton</a> 
            </div>
        </div>
        </div>

    </div>
<!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
</body>
</html>
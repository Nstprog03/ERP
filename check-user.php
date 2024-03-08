<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Jivandhara Database	</title>
 
    

</head>



<body>
	<?php 
	if (isset($_SESSION['username'])) {
    $id = $_SESSION['username'];
    $sql = "select * from users where username=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
    

                           if(isset($_SESSION['username'])){
    $user=$_SESSION['username'];
}?>
                                <a class="nav-link" href="javascript:;"><i class="fa fa-user text-success" aria-hidden="true"></i><?php echo $user; ?></a>

<?php                                 }
  
    
      $errorMsg = 'Could not Find Any Record';
    echo $errorMsg;
    ?>
  





                           
</body>

</html>
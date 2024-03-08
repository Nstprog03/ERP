<?php
  require_once('../db.php');
 

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from pur_report where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
    $firm = $_POST['firm'];
    $mnt = $_POST['mnt'];
    $start_yr = $_POST['start_yr'];
    $end_yr = $_POST['end_yr'];    
    
 
  
  
    if(!isset($errorMsg)){
      $sql = "update pur_report
                  set firm = '".$firm."',
                    start_yr = '".$start_yr."',
                    end_yr = '".$end_yr."',                    
                    mnt = '".$mnt."'                  


          where id=".$id;
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
        header('Location:index.php');
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
      }
    }

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Purchase Report Edit</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

     <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 
    });
    </script> 
  </head>
  <body>

    
    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
      <div class="container">
        <a class="navbar-brand" href="index.php">Purchase Report Edit</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                Purchare Report Edit
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                      <label for="firm">Select Firm</label>
                      <?php
                            $sql = "select * from party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="firm" class="form-control" >
                            <option value="<?php echo $row['firm']; ?>" selected="selected"><?php echo $row['firm']; ?></option>
                            
                            <?php                   
                                foreach ($conn->query($sql) as $result) {

                                    echo "<option  value=" .$result['party_name']. ">" .$result['party_name']. "</option>";  

                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                      <label for="mnt">Select Month</label>
                      <?php
                            $sql = "select * from emp_salary";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="mnt" class="form-control" >
                            <option value="<?php echo $row['mnt']; ?>" selected="selected"><?php echo $row['mnt']; ?></option>

                      <option>January</option>
                      <option>February</option>
                      <option>March</option>
                      <option>April</option>
                      <option>May</option>
                      <option>June</option>
                      <option>July</option>
                      <option>August</option>
                      <option>September</option>
                      <option>October</option>
                      <option>November</option>
                      <option>December</option>
                    
                            
                            
                        </select>
                    </div>

                   
                    
                    <div class="form-group">
                      <label for="party">Start Year</label>
                     <select name="start_yr" class="form-control" id="selectElementId">
                            <option value="<?php echo $row['start_yr']; ?>" selected="selected"><em><?php echo $row['start_yr']; ?></em></option>                    
                           
                     </select>
                   </div>

                   <div class="form-group">
                      <label for="party">End Year</label>
                      <select name="end_yr" class="form-control" id="endYear">
                            <option value="<?php echo $row['end_yr']; ?>" selected="selected"><em><?php echo $row['end_yr']; ?></em></option>                    
                           
                     </select>
                    </div>                                                         
                    

                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
</div>
</div>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
              <script>
var min = 1950,
    max = min + 100,
    select = document.getElementById('selectElementId');

for (var i = min; i<=max; i++){
    var opt = document.createElement('option');
    opt.value = i;
    opt.innerHTML = i;
    select.appendChild(opt);
}

var min = 1950,
    max = min + 100,
    select = document.getElementById('endYear');

for (var i = min; i<=max; i++){
    var opt = document.createElement('option');
    opt.value = i;
    opt.innerHTML = i;
    select.appendChild(opt);
}
        </script>
  </body>
</html>

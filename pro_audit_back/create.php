<?php
  require_once('../db.php');
  include('add.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Programme Audit</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

        <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
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
        <a class="navbar-brand" href="index.php">Programme Audit Report</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i></a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">Programme Audit Report Create</div>
              <div class="card-body">
                 


                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="party_name">Select Party</label>
                      <?php
                            $sql = "select * from party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="party_name" class="form-control">
        <?php                   
            foreach ($conn->query($sql) as $result) {

                echo "<option  value=" .$result['party_name']. ">" .$result['party_name']. "</option>";  

            }
        ?>
    </select>

                      
                  
                    </div>

                    <div class="form-group">
                      <label for="orgname">Select Orgnization</label>
                      <?php
                            $sql = "select * from organization";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="orgname" class="form-control">
        <?php                   
            foreach ($conn->query($sql) as $result) {

                echo "<option  value=" .$result['orgname']. ">" .$result['orgname']. "</option>";  

            }
        ?>
    </select>

                      
                  
                    </div>

 
                    <div class="form-group">
                      <label for="start_yr">Select Start Year</label>
                    <select name="start_yr" class="form-control" id="selectElementId">
                    </select>
                  </div>

                    <div class="form-group">
                      <label for="end_yr">Select End Year</label>
                    <select name="end_yr" class="form-control" id="endYear">
                    </select>
                  </div>
                  
                   
                    <div class="form-group">
                      <label for="trans">Transaction Image</label>
                      <input type="file" class="form-control" name="trans" Transaction="">
                    </div>


                    <div class="form-group">
                      <label for="audit">Audit Report Image</label>
                      <input type="file" class="form-control" name="audit" Audit Report="">
                    </div>

                    <div class="form-group">
                      <label for="doc">Document Image</label>
                      <input type="file" class="form-control" name="doc" Document="">
                    </div>

                    <div class="form-group">
                      <label for="sales">Sales Record Image</label>
                      <input type="file" class="form-control" name="sales" value="">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
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

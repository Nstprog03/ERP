<?php
  include('../db.php');

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales Confirmation Database</title>
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
          <a class="navbar-brand" href="index.php">Sales Confirmation Database</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>
                &nbsp;
                <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
              </ul>
          </div>
        </div>
      </nav>

      <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Sales Confirmation List</div>
                      <div class="card-body">


<form action="" method="post" class="mb-3">
      <div class="select-block">
        <div class="row">
                       <?php
                            $sql = "select * from seller_conf";
                            $result = mysqli_query($conn, $sql);
                            $sql2 = "SELECT DISTINCT external_party FROM seller_conf";
                            $result2 = mysqli_query($conn, $sql2);
                          ?>   
               
                        <div class="form-group col-sm-6">
                          <label for="seller_conf">Sales Party</label>
                          <select name="seller_conf_party" class="form-control" onchange="$('select[name=seller_conf] option').hide();$('select[name=seller_conf] option[data-party='+this.value+']').show();">
                            <option value="" disabled selected>Choose Parties</option>
                            <?php                   
                              foreach ($conn->query($sql2) as $result2) 
                              {
                                    echo "<option  value=" .$result2['external_party']. ">" .$result2['external_party']. "</option>";
                              }

                            ?>                              
                          </select>     
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="seller_conf">Sale Confirmation No</label>              
                           <select name="seller_conf" class="form-control">
                            <option value="" disabled selected>Choose option</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option data-party=" .$result['external_party']. " value=" .$result['sales_conf']. ">" .$result['sales_conf']. "</option>";
                              }

                            ?>                              
                            </select>
                        </div>
        </div>
        <div class="row">
          <div class="form-group col-sm-12">                        
      <input type="submit" name="submit" class="btn btn-warning" vlaue="Choose options">
          <?php
      if(isset($_POST['submit'])){
        if(!empty($_POST['seller_conf'])) {
          $selected = $_POST['seller_conf'];
          echo 'You have chosen: ' . $selected;
        } else {
          echo 'Please select the value.';
        }
      }
    ?>
                    </div>    
                   
    </div>
    <div class="row">
     <div class="form-group col-sm-12">   

                  
                              <a href="create.php?id=<?php echo  $selected ?>" class="btn btn-success"><i class="fa fa-user-plus"></i>&nbsp;Create Report</a>
                            </div>
                          </div>
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

  </body>
</html>

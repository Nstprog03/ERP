<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from sales_report where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }


?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Recivied Register</title>
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


     <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">

      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">



    
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
        <a class="navbar-brand" href="index.php">Payment Recivied Register</a>
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
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">Sales Register Database</div>
              <div class="card-body">
             
                <form class=""  method="POST"
                   enctype="multipart/form-data">
                   <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="row">
                       <?php
                            $sql4 = "select * from party where id='".$row['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }
                            
                          ?>
               
                        <div class="form-group col-sm-4">
                          <label for="party_data"> Firm Name</label>
                          <input type="text" class="form-control" value="<?php  echo $pname; ?>">    
                        </div>

                        <div class="form-group col-sm-4">
                          <label for="party_data"> Billing Party</label>
                          <input type="text" class="form-control" value="<?php  

                              $Ex_party = "select * from external_party where id='".$row['party_name']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                                
                             echo  $Ex_partyrow['partyname'];

                           ?>">    
                        </div>

                        <div class="form-group col-sm-4">
                          <label for="party_data"> Delivery City</label>
                          <input type="text" class="form-control" value="<?php echo $row['delivery_city'] ?>">    
                        </div>

                        <div class="form-group col-sm-4">
                            <label for="party">Credit days</label>
                            <input type="text" class="form-control" value="<?php  echo $row['credit_days'];?>" readonly>
                            
                               
                        </div>


                     

                    
                      <div class="form-group col-sm-4">
                        <label for="delivery_city">Bill Date</label>
                        <input type="text" class="form-control" value="<?php echo date("d/m/Y", strtotime($row['invoice_date']));  ?>">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="truck">Bill No.</label>
                        <input type="text" class="form-control" value="<?php echo $row['invice_no'] ?>">
                      </div>
                      </div>
                      <div class="row">
                      <div class="form-group col-sm-4">
                        <label for="invice_no">Final Amount</label>
                        <input type="text" class="form-control" name="invice_no"  placeholder="Enter Invoice No" value="<?php echo $row['total_value'] ?>">
                      </div>
                    
                      </div>
      
                  
                  </div></div>
                    
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
    
    <script type="text/javascript">
        $(document).ready(function () {
          $('input[type="text"]').prop('readonly', true);
    });
    </script>
  </body>
</html>

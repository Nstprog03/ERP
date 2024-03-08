<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from urd_purchase_payment where id=".$id;
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
    <title>URD Kapas Purchase & Payment Report Details</title>
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

<style type="text/css">
.modal-dialog {width:800px;}
.thumbnail {margin-bottom:6px;}
.modal.fade .modal-dialog {
      -webkit-transform: translate(0, 0);
      -ms-transform: translate(0, 0); // IE9 only
          transform: translate(0, 0);

 }
</style>
    
    
    
  </head>
  <body>


    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

            
      <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> URD Kapas Purchase & Payment Report Details</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
 


          <?php
          $page=1;
          if(isset($_GET['page']))
          {
            $page=$_GET['page'];
          }
          ?>

              <ul class="navbar-nav ml-auto"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
                            </ul>


          </div>
        </div>
      </nav>

        <!-- last change on table START-->
       <div class="last-updates">
                  <div class="firm-selectio">
             <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["pur_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["pur_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
           $sqlLastChange="select username,updated_at from urd_purchase_payment where id='".$row['id']."'";

           $resultlLastChange = mysqli_query($conn, $sqlLastChange);

           if(mysqli_num_rows($resultlLastChange)>0)
           {
              $lastChangeRow=mysqli_fetch_assoc($resultlLastChange);

              //.get username from user master
              $user_name='';
              $sqlgetUser="select * from users where id='".$lastChangeRow['username']."'";
              $sqlResultGetUser = mysqli_query($conn, $sqlgetUser);
              if(mysqli_num_rows($sqlResultGetUser)>0)
              {
                $getUserRow=mysqli_fetch_assoc($sqlResultGetUser);
                $user_name=$getUserRow['name'];
              }

              echo "
                
                      <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>
                 
              ";
           }
          ?>

        </div>
          </div>

          <!-- last change on table END-->       

      <div class="container-fluif">
        <div class="row justify-content-center">
          <div class="card">
            <div class="card-header">
              URD Kapas Purchase & Payment Report Details 
            </div>
            <div class="card-body">
              <form class="" action="add.php" method="post"
                   enctype="multipart/form-data">
                   
                       
                        <div class="row">

                          <?php
                          $sql2 = "select * from farmer where id='".$row['farmer']."'";
                          $result2 = mysqli_query($conn, $sql2);
                          $row2=mysqli_fetch_array($result2);
                          ?> 


                            <div class="form-group col-md-4">
                              <label for="farmer">Select Farmer</label>
                                  <input type="text" class="form-control" name="" value="<?php echo $row2['farmer_name'] ?>"  readonly="">
                            </div>

                            <div class="form-group col-md-4">
                              <label for="village">Select Village </label>
                              <input type="text" class="form-control" name="" value="<?php echo $row['village'] ?>"  readonly="">
                            </div>

                            <div class="form-group col-md-4">
                              <label for="district">Select District </label>
                              <input type="text" class="form-control" name="" value="<?php echo $row['district'] ?>"  readonly="">
                            </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-4">
                            <label for="weight">Man Weight</label>
                            <input type="text"  class="form-control weight" name="weight" placeholder="Enter Weight" value="<?php echo $row['weight']; ?>" readonly>
                          </div>

                          <div class="form-group col-md-4">
                              <label for="rate">Man Rate</label>
                            <input type="text"  class="form-control rate" name="rate" placeholder="Enter Rate" value="<?php echo $row['rate']; ?>" readonly >
                          </div>

                          <div class="form-group col-md-4">
                              <label for="amount">Amount</label>
                            <input type="text"  class="form-control bold" id="amount" name="amount" readonly  value="<?php echo $row['amount']; ?>">
                          </div>
                        </div>                  


                        <div class="row">



                            <?php
                     
                        $date='';
                        if($row['date']!='' && $row['date']!='0000-00-00')
                          {
                            $date = str_replace('-', '/', $row['date']);
                            $date = date('d/m/Y', strtotime($date));
                          }

                      ?>

                  <div class="form-group col-md-4">
                      <label for="date">Date</label>
                      <input type="text" class="form-control datepicker" name="date"  value="<?php echo $date ?>" readonly>
                  </div>



                          <div class="form-group col-md-4">
                            <label for="firm"> Firm</label>
                            <?php
                                $sql4 = "select * from party where id='".$row['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }?>
                              <input type="text" class="form-control" name="" value="<?php echo $pname; ?>"  readonly="">
                          </div>

                           <?php
                          $sql2 = "select * from broker where id='".$row['broker']."'";
                          $result2 = mysqli_query($conn, $sql2);
                          $row2=mysqli_fetch_array($result2);
                          ?>

                          <div class="form-group col-md-4">
                            <label for="broker">Broker</label>
                            <input type="text" class="form-control" name="" value="<?php echo $row2['name'] ?>"  readonly="">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="payment">Payment Status</label>
                             <input type="text" class="form-control" name="" value="<?php echo $row['payment_status'] ?>"  readonly="">
                          </div>
                        </div>
                  

                    
                </form>
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
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
});
    </script>
    </body>
  </html>

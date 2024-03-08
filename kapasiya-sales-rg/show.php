<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['kap_firm_id']) && !isset($_SESSION['kap_seasonal_year_id']))
{
  header('Location: index.php');
}

  $assreport_dir = 'files/assessment/';

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from kapasiya where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  $truck = explode(",", $row['truck']);
  foreach ($truck as $key => $value) {
    $data[] = explode(":",$value);
  }
  
  $date = explode("/", $row['seasonal_year']);

  // $s_date = DateTime::createFromFormat('Y-m-d',$date[0]);
  // $s_date=$s_date->format('d/m/Y');

  // $e_date = DateTime::createFromFormat('Y-m-d',$date[1]);
  // $e_date=$e_date->format('d/m/Y');
  // echo date("d-m-Y", strtotime($s_date));
  // exit();

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>View Kapasiya Sales Report Details</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">
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
             <a class="navbar-brand" href="index1.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Kapasiya Sales Report</span></a>
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

          


              <ul class="navbar-nav ml-auto"><a class="btn btn-outline-danger" href="index1.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
                            </ul>


          </div>
        </div>
      </nav>

      <!-- last change on table START-->
      <div class="last-updates">
        <div class="firm-selectio">
         <div class="firm-selection-pre">
          <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["kap_firm"]; ?></span>
        </div>
        <div class="year-selection-pre">
          <span class="pre-year-text">Seasonal Year :</span> 
          <span class="pre-year">
            <?php 

            $finYearArr=explode('/',$_SESSION["kap_seasonal_year"]);

            $start_date=date('Y', strtotime($finYearArr[0]));
            $end_date=date('Y', strtotime($finYearArr[1]));

            echo $start_date.' - '.$end_date; 

            ?>
          </span>
        </div>
      </div>
      <div class="last-edits-fl">
        <?php
        $sqlLastChange="select username,updated_at from kapasiya where id='".$row['id']."'";

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

      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="card">
            <div class="card-header">
              Kapasiya Sales Report Details 
            </div>
            <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                  <div class="row">

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Serial No.</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $row['serialno'] ?></span>
                    </div>
                    </div>

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Firm Name</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span>

                        <?php 

                          $party = "select * from party where id='".$row['firm']."'";
                          $partyresult = mysqli_query($conn, $party);

                          $partyrow = mysqli_fetch_assoc($partyresult);

                          $ex_party='';
                          if(isset($partyrow))
                          {
                            $ex_party=$partyrow['party_name'];
                          }
                          echo $ex_party;

                        ?>

                    </span>
                    </div>
                    </div>                    

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Seasonal Year</h6>
                    </div>
                    <div class="form-control view-form"> 

                      <span>
                        <?php 

                          $sql4 = "select * from seasonal_year where id='".$row['seasonal_year']."'";
                          $result4 = mysqli_query($conn, $sql4);
                          $row10 = mysqli_fetch_assoc($result4);
                          $seasonal_yearGet='';
                          if(isset($row10))
                          {
                               $seasonal_yearGet=date("Y", strtotime($row10['startdate'])). ' - ' .date("Y", strtotime($row10['enddate']));
                          }

                          echo $seasonal_yearGet;
                        ?>
                      </span>
                    </div>
                    </div>                     

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Party Name</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span>

                        <?php 

                          $external_party = "select * from external_party where id='".$row['party']."'";
                          $external_partyresult = mysqli_query($conn, $external_party);

                          $external_partyrow = mysqli_fetch_assoc($external_partyresult);

                          $ex_party='';
                          if(isset($external_partyrow))
                          {
                            $ex_party=$external_partyrow['partyname'];
                          }
                          echo $ex_party;

                        ?>

                    </span>
                    </div>
                    </div> 

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Rate</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $row['rate'] ?></span>
                    </div>
                    </div>

                     <?php
                $conf_date='';
                if($row['conf_date']!='' && $row['conf_date']!='0000-00-00')
                {
                  $conf_date= date("d/m/Y", strtotime($row['conf_date']));
                }
                ?>

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Confirmation Date</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $conf_date ?></span>
                    </div>
                    </div>

                    <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Credit</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $row['credit'] ?></span>
                    </div>
                    </div>

                    <?php if ($row['credit'] == 'other') {?>
                      <div class="field-show mb-3 col-sm-3">
                    <div class="label">
                    <h6 class="title">Other Days</h6>
                    </div>
                    <div class="form-control view-form">  
                      <span><?php echo $row['other_day'] ?></span>
                    </div>
                    </div>
                    <?php } ?>  

                    
                    </div>     



             


        

                      
                </div>

              </div>
              <?php 
              $truckDataArr=json_decode($row['truck'],true);
              foreach ($truckDataArr as $key => $value) 
                { 

                $sales_date='';
                if($value['sales_date']!='' && $value['sales_date']!='0000-00-00')
                {
                  $sales_date = str_replace('-', '/', $value['sales_date']);
                  $sales_date = date('d/m/Y', strtotime($sales_date));
                }

                  ?>
              <div class="table-responsive">
                <table class="table" border="1">
                  
                  <tr>
                    <td colspan="2"><b>No. Of Trucks : <?= $key+1 ?></b></td>
                    
                  </tr>

                  <tr>
                    <td>Trucks</td>
                    <td><?php echo $value['truck_id'] ?></td>
                  </tr>

                  <tr>
                    <td>Sales Date</td>
                    <td><?php echo $sales_date; ?></td>
                  </tr>

                  <tr>
                    <td>Weight</td>
                    <td><?php echo $value['weight']; ?></td>
                  </tr>

                  <tr>
                    <td>Basic Amount</td>
                    <td><?php echo $value['basic_amt']; ?></td>
                  </tr>

                  <tr>
                    <td>GST</td>
                    <td><?php echo $value['gst_per']; ?></td>
                  </tr>

                  <tr>
                    <td>GST Amount</td>
                    <td><?php echo $value['gst_amount']; ?></td>
                  </tr>


                  <tr>
                    <td>TCS Percentage</td>
                    <td><?php echo $value['tcs_per']; ?></td>
                  </tr>

                  <tr>
                    <td>TCS Amount</td>
                    <td><?php echo $value['tcs_amt']; ?></td>
                  </tr>

                  <tr>
                    <td>Final Amount</td>
                    <td><?php echo $value['final_amt']; ?></td>
                  </tr>

                  <tr>
                    <td>Invoice No</td>
                    <td><?php echo $value['invoice_no']; ?></td>
                  </tr>

                  <tr>
                    <td>Truck</td>
                    <td><?php 

                        $sql5 = "select * from truck_master where id='".$value['truck_no']."'";
                            $result5 = mysqli_query($conn, $sql5);

                            $row5 = mysqli_fetch_assoc($result5);
                            // print_r($row5);
                            $truck='';
                            if(isset($row5))
                            {
                              $truck=$row5['truck_no'];
                            }
                      echo $truck; ?></td>
                  </tr>

                  <tr>
                    <td>Payment Status</td>
                    <td><?php echo $value['payment_status'] ?></td>
                  </tr>
                  <tr>
                    <td>Sold Out</td>
                    <td>
                      <?php 

                      $check = $value['truck_complete'];
                      if($check=='1')
                      {
                        echo 'Yes';
                      }
                      else
                      {
                        echo 'No';
                      }
                      ?>

                    </td>
                  </tr>
                </table>
              </div>
            <?php } ?>
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
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
        $(document).ready(function () {
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
});
    </script>
    </body>
  </html>

<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(isset($_POST['submit'])){
  $firmDetails=explode("/", $_POST['firm']);
  $_SESSION["b2bp_firm"] = $firmDetails[0];
  $_SESSION["b2bp_firm_id"] = $firmDetails[1];
  $_SESSION["b2bp_financial_year"] = $_POST['financial_year'];
  //get fincial year record id from DB
  $getFYearDates=explode("/", $_POST['financial_year']);
  $sql3="select * from financial_year where startdate='".$getFYearDates[0]."' AND enddate='".$getFYearDates[1]."'";

  $result3 = mysqli_query($conn, $sql3);
  $row3 = mysqli_fetch_array($result3);
  $_SESSION["b2bp_financial_year_id"] = $row3['id'];
  header("Location:index1.php");
} 
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bill 2 Bill Payment Dashboard</title>
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">-->
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
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Bill 2 Bill Payment Dashboard</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
   
        </div>
      </nav>

<div class="container">
        <div class="row justify-content-center">
          <div class="col-md-7">
            <div class="card">
              <div class="card-header">Bill 2 Bill Payment Dashboard</div>
              

    <div class="card-body">


        <!-- Page Content  -->

       

          <form action="" method="post" enctype="multipart/form-data">

            <div class="row">
              <div class="col-md-12">
                      <div class="form-group">
                <label for="firm">Select Firm</label>
                <?php
                        $sql = "select * from party";
                        $result = mysqli_query($conn, $sql);
                    ?>
                    <select name="firm" class="form-control">
                <?php                   
                    foreach ($conn->query($sql) as $result) 
                    {
                        echo "<option  value='".$result['party_name'].'/'.$result['id']."'>".$result['party_name']. "</option>";
                    }
                ?>
                </select>
            </div>
              </div>
            

                <div class="col-md-12">
                      <div class="form-group">
                <label for="financial_year">Select Financial Year</label>
                <?php

                     $financialYear = getFinancialYear($conn);

                      $storeDates=array();
                      $Dates=array();
                      foreach($financialYear as $rwdata){


                        $Dates=[
                          'startdate'=>date('Y-m-d',strtotime($rwdata['startdate'])),
                          'enddate'=>date('Y-m-d',strtotime($rwdata['enddate'])),
                        ];

                        //array_push($storeDates, date('Y-m-d',strtotime($rwdata['startdate']))."-".date('Y-m-d',strtotime($rwdata['enddate'])));
                        array_push($storeDates, $Dates);
                      }
                    ?>
                    <select name="financial_year" class="form-control">
                    <?php                   
                    //   rsort($storeDates);
                      $arrlength = count($storeDates);
                      
                      for($x = 0; $x < $arrlength; $x++) {
                      
                        //current financial year selected
                             $curDate=date('Y-m-d');
                             $startdate=date('Y-m-d', strtotime($storeDates[$x]['startdate']));
                             $enddate=date('Y-m-d', strtotime($storeDates[$x]['enddate']));
                            
                             if($curDate>=$startdate && $curDate<=$enddate)
                             {
                              echo "<option value=" .$storeDates[$x]['startdate']."/".$storeDates[$x]['enddate']." selected=''>".date('Y',strtotime($storeDates[$x]['startdate']))."-".date('Y',strtotime($storeDates[$x]['enddate']))."</option>";
                             }else{
                              echo "<option value=" .$storeDates[$x]['startdate']."/".$storeDates[$x]['enddate'].">".date('Y',strtotime($storeDates[$x]['startdate']))."-".date('Y',strtotime($storeDates[$x]['enddate']))."</option>";
                             }
                      }
                    // foreach ($conn->query($sql2) as $result2) 

                    // {

                    //     //get Start Year And End Year
                    //     $syear = date("Y", strtotime($result2['startdate']));

                    //     $eyear = date("Y", strtotime($result2['enddate']));


                    //     //current financial year selected
                    //     $curDate=date('Y-m-d');
                    //     $startdate=date('Y-m-d', strtotime($result2['startdate']));
                    //     $enddate=date('Y-m-d', strtotime($result2['enddate']));

                    //     if($curDate>=$startdate && $curDate<=$enddate)
                    //      {
                    //         echo "<option  value=" .$result2['startdate']."/".$result2['enddate']." selected=''>" .$syear."-".$eyear."</option>";
                    //      }
                    //      else
                    //      {
                    //         echo "<option  value=" .$result2['startdate']."/".$result2['enddate'].">" .$syear."-".$eyear."</option>";
                    //      }

                    //  }

                ?>
                </select>

            </div>
                </div>

            
            <div class="col-md-12">
            <div class="form-group">

                <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>

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

    <!-- jQuery CDN - Slim version (=without AJAX) -->
    
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

  
  </body>
</html>
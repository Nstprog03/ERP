<?php

// Initialize the session

  include('db.php');

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
    <title>Purchase Dashboard</title>
    
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

<?php




$step = '0';

if(isset($_POST['submit'])){

  $step = '1';

   $_SESSION["active_module"] = 'purchase';


  $firmDetails=explode("/", $_POST['firm']);

  $_SESSION["pur_firm"] = $firmDetails[0];
  $_SESSION["pur_firm_id"] = $firmDetails[1];




  $_SESSION["pur_financial_year"] = $_POST['financial_year'];


  //get fincial year record id from DB
  $getFYearDates=explode("/", $_POST['financial_year']);
  $sql3="select * from financial_year where startdate='".$getFYearDates[0]."' AND enddate='".$getFYearDates[1]."'";

  $result3 = mysqli_query($conn, $sql3);

  $row3 = mysqli_fetch_array($result3);

  
  $_SESSION["pur_financial_year_id"] = $row3['id'];

} 

//unsert all  filter  session varibale

//pur conf
unset($_SESSION["purconf_filter_data"]);
unset($_SESSION["purconf_filter_selected"]);

//pur report
unset ($_SESSION["purreport_filter_data"]);
unset ($_SESSION["purreport_filter_selected"]);

//pur debit report
unset ($_SESSION["purdebit_filter_data"]);
unset ($_SESSION["purdebit_filter_selected"]);

//bales payout
unset ($_SESSION["purpay_filter_data"]);
unset ($_SESSION["purpay_filter_selected"]);

//transport payout
unset ($_SESSION["tppay_filter_data"]);
unset ($_SESSION["tppay_filter_selected"]);

//RD kapas purchase report
unset ($_SESSION["rdkpr_filter_data"]);
unset ($_SESSION["rdkpr_filter_selected"]);

//RD kapas purchase Payment
unset ($_SESSION["rdkpp_filter_data"]);
unset ($_SESSION["rdkpp_filter_selected"]);


//URD kapas purchase & payment
unset ($_SESSION["urdkpp_filter_data"]);
unset ($_SESSION["urdkpp_filter_selected"]);
if(isset($_SESSION['pur_ext_party'])){ unset($_SESSION['pur_ext_party']); }
if(isset($_SESSION['kapasiya_sales_party'])){ unset($_SESSION['kapasiya_sales_party']); } 

?>


        <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

                  <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Purchase Dasboard</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <?php if($step=='1') {  ?>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="btn btn-outline-danger" href="purchase_index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
              </ul>
          </div>

          <?php } ?>
        </div>
      </nav>




        <!-- Page Content  -->



          <?php if($step == '0'){?>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-7">
            <div class="card">
              <div class="card-header">Purchase Dashboard</div>
              

              <div class="card-body">

          <form action="" method="post" enctype="multipart/form-data">

            <div class="row">
              <div class="col-md-12">
                      <div class="form-group">
                <label for="firm">Select Party (Firm)</label>
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

          <?php } else {?>


            <div class="last-updates">
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


    <div class="container-fluid">
            <div class="row">
              <div class="form-group col-md-4">
                    <label for="select_page_group">Select Group:</label>
                  <select class="form-control" id="select_page_group" name="select_page_group" onchange="select_page_group(this.value)">
                      <option value="CottonBales">Cotton Bales</option>

                      <option value="Kapas">Kapas</option>

                  </select>

              </div>
            </div>

            <div class="form-group select-pages purchase_page">

        

                <ul id="CottonBales_page_list">

                    <li>
                        <a href="/pur_conf">
                         <span class="inital"><span class="icon-pending"></span></span>
                        Confirmation Box</a>
                    </li>     

                    <li>
                        <a href="/purchase_report">
                          <span class="inital"><span class="icon-ledger"></span></span>
                        Purchase Report</a>
                    </li>

                    <li>
                        <a href="/pur_debit_report">
                    <span class="inital"><span class="icon-purchase_list2"></span></span>
                        Debit Report</a>
                    </li> 

                    <li>
                       <a href="/bales_pur_payout">
                   <span class="inital"><span class="icon-pruchase_2"></span></span>
                       Bales Payout</a>
                    </li> 
                     <li>
                       <a href="/transport_payout">
                   <span class="inital"><span class="icon-pruchase_2"></span></span>
                       Transport Payout</a>
                    </li>
           <!--          <li> -->
           <!--         <a href="/tds_tcs_declaration/index.php?purchase_cotton=1">-->
           <!--<span class="inital"><span class="icon-annual_data_document_fee_legal_icon"></span></span>-->
           <!--         TDS/TCS Declaration</a>-->
           <!--         </li>-->

                </ul>

                <ul id="Kapas_page_list">
                    <li>

                        <a href="/rd_kapas_purchase_report">
                          <span class="inital"><span class="icon-purchase_list2"></span></span>
                        RD Kapas Purchase Report</a>

                    </li>  

                    <li>                        
                        <a href="/rd_kapas_purchase_payment">
                          <span class="inital"><span class="icon-pruchase_2"></span></span>
                        RD Kapas Purchase Payment</a>
                    </li>    

                        

                    

                    <li>

                        <a href="/urd_kapas_purchase_payment">
                          <span class="inital"><span class="icon-ledger"></span></span>
                        URD Kapas Purchase & Payment</a>

                    </li> 

                    <!--<li>-->
                    <!--   <a href="/tds_tcs_declaration/index.php?purchase_kapas=1">-->
                    <!--    <span class="inital"><span class="icon-annual_data_document_fee_legal_icon"></span></span>-->
                    <!--   TDS/TCS Declaration</a>-->
                    <!--</li> -->

                </ul>

            </div>

            <script>


              select_page_group();

            function select_page_group() {

                var val = $('#select_page_group :selected').val();

                if(val=="CottonBales")
                {
                      $('#CottonBales_page_list').show();
                      $('#Kapas_page_list').hide();
                }
                else
                {
                      $('#Kapas_page_list').show();
                      $('#CottonBales_page_list').hide();
                }

            }

            </script>

             </div>

          <?php }?>

   

    

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

    <script type="text/javascript">
        $(document).ready(function () {
            
        });
     
    </script>


  </body>
</html>
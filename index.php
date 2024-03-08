<?php
session_start();
// Initialize the session
include('db.php');

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
  }

function getFirmName($id)
{
    include('db.php');
    $name='';
    $party = "select * from party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $name=$partyrow['party_name'];
    }
    return $name;
}
function getExtPartyName($id)
{
    include('db.php');
    $name='';
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $name=$partyrow['partyname'];
    }
    return $name;
}
function getFinancialYear($id)
{
    include('db.php');
    $year='';
    $sql = "select * from financial_year where id='".$id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result)>0)
    {
      $row = mysqli_fetch_array($result);
      $syear = date("Y", strtotime($row['startdate']));
      $eyear = date("Y", strtotime($row['enddate']));
      $year=$syear.'-'.$eyear;
    }
    return $year;
}
function getSeasonalYear($id)
{
    include('db.php');
    $year='';
    $sql = "select * from seasonal_year where id='".$id."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result)>0)
    {
      $row = mysqli_fetch_array($result);
      $syear = date("Y", strtotime($row['startdate']));
      $eyear = date("Y", strtotime($row['enddate']));
      $year=$syear.'-'.$eyear;
    }
    return $year;
}
?>
<!DOCTYPE html>
<html>

<head>
        
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Jivandhara Database  </title>

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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>

 <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
     <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

    <style type="text/css">
      body {
          scroll-behavior: smooth !important;
        }
    </style>





     <script> 
    $(function(){
    $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

       

   });
    
    </script>  

    <style type="text/css">




      .hiddenRow {
            padding: 0 !important;
        }
        @-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
    @-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
    @keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }

    @-moz-keyframes nospin { 100% { -moz-transform: rotate(-360deg); } }
    @-webkit-keyframes nospin { 100% { -webkit-transform: rotate(-360deg); } }
    @keyframes nospin { 100% { -webkit-transform: rotate(-360deg); transform:rotate(-360deg); } }

    /*body
    {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(51deg, #594879, #ff5722);
    }*/
    .menu-wrapper {
    /* margin-right: calc(100% - 800px); */
    max-width: 100%;
    width: 80%;
    margin-top: 30px;
    /* text-align: center; */
}

    .menu {
    position: relative;
    width: 400px;
    height: 400px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
}
#kapasiya_sales_section {
    max-height: 450px;
    overflow: auto;
}
.menu-section-dash {
    display: flex;
    justify-content: space-between;
    vertical-align: middle;
    align-items: center;
}
.left-menu-wrapper ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: inline-block;
}
.left-menu-wrapper li a {
    background: #594879;
    color: #FFF;
    width: 100%;
    /* display: block; */
    padding: 8px 16px 8px 20px;
    position: relative;
    z-index: 27;
}
.left-menu-wrapper li a:before {
    background: #fbb040;
    left: 0;
    top: 0;
    position: absolute;
    width: 10px;
    height: 100%;
    content: '';
    top: 0;
}
   /* background: #fbb040;
    left: 0;
    top: 0;
    position: absolute;
    width: 10px;
    height: 35px;
    content: '';
    top: -8px;*/
.left-menu-wrapper li a:hover:before
{
 width: 10px;
}    
/*.left-menu-wrapper li a:after, .left-menu-wrapper li a:after {
    width: 0;
    width: 0;
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    z-index: 0;
}*/
.left-menu-wrapper li a:hover:after, .left-menu-wrapper li a:focus:after {
    background: #fbb040;
    width: 100%;
    /* content: ''; */
    /* position: absolute; */
    /* left: 0; */
    /* top: 0; */
    /* height: 100%; */
    /* z-index: -1; */
    transition: 0.3s;
}
.left-menu-wrapper li {
    padding: 0;
    position: relative;
    margin: 25px 0;
    display: block;
    line-height: 1.2;
    height: 100%;
}

.jumbotron.dash-jumbo {
    padding: 0px 35px 25px 35px;
    background: transparent;
    position: relative;
    position: relative;
    z-index: 1;
    /* backdrop-filter: blur(100px); */
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
    background-color: rgb(89 72 121);
    background: rgba(255, 255, 255, 0.25);
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(13.1px);
    -webkit-backdrop-filter: blur(13.1px);
    border: 10px solid #FFF;
}
.menu li:nth-child(odd) a
{
background: #715b9b;  
}
.menu li:nth-child(even) a
{
  background: #594879;
}
.menu li a {    
    color: #FFF;
    border: 6px solid #fbb040;
}
    .menu li
    {
      position: absolute;
      left: 0;
      list-style: none;
      transform-origin: 200px;
      transition: 1.25s;
      transition-delay: calc(0.1s * var(--i));
        transform: rotate(0deg) translateX(160px);
    }
    .menu.active li 
    {
      transform: rotate(calc(360deg / 8 * var(--i))); 
      transition: 1.25s;
    }
    .menu li a 
    {
      width: 80px;
      height: 80px;
      background: #FFF;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      transform: rotate(calc(360deg / -8 * var(--i)));  
      box-shadow: 0 3px 4px rgba(0,0,0,0.15);
      transition: 1.25s;
    }
    .menu li a:hover
    {
      color: #fbb040;
    }
    .top-texter {
    display: flex;
    justify-content: space-between;
    background: #ebebeb;
    margin: 0 -35px;
    padding: 15px 50px 15px;
    border-radius: 7px 3px 0 0;
}
    .toggle
    {
      position: absolute;
      width: 80px;
      height: 80px;
      background: #FFF;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      border-radius: 50%;
      cursor: pointer;
      box-shadow: 0 0px 4px rgba(0,0,0,0.15);
      font-size: 2em;
      transition: 1.25s;
    }
    .top-texter {
    display: flex;
    justify-content: space-between;
}
.welcomgre, .top-line {
    display: inline-block;
}
.top-line {
    font-weight: 400;
    font-size: 22px;
    font-weight: 600;
    line-height: 1.7;
}

.top-hording {
    padding: 15px 65px;
    background: transparent;
    position: relative;
    position: relative;
    z-index: 1;
    /* backdrop-filter: blur(100px); */
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
    background-color: rgb(89 72 121);
    background: rgba(255, 255, 255, 0.25);
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(13.1px);
    -webkit-backdrop-filter: blur(13.1px);
    border: 10px solid #FFF;
    margin-bottom: 25px;
    z-index: 3;
}
.top-hording-wrap:before, .top-hording-wrap:after {
    content: '';
    position: absolute;
    background: #594879;
    height: 120px;
    width: 1px;
    /* z-index: 999; */
}
.top-hording:before {
    right: 15px;
    /* top: 50%; */
}
.top-hording-wrap:after {
    right: -28px;
    top: 15px;
}
.menu li a [class^="icon-"]
{
    font-size:27px;
}
.top-hording:after {
    left: 15px;
    /* top: 50%; */
}
.top-hording-wrap:before {
    left: -28px;
    top: 14px;
}
.top-hording-wrap {
    justify-content: space-between;
    position: relative;
    z-index: 999;
}
.top-hording:after, .top-hording:before {
    content: '';
    position: absolute;
    background: #594879;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    top: 35%;
    background: #594879;
    border: 3px solid #fbb040;
}
.jumbotron.dash-jumbo:after, .jumbotron.dash-jumbo:before {
    content: '';
    position: absolute;
    width: 15px;
    height: 15px;
    background: #594879;
    border: 3px solid #fbb040;
    top: 26px;
    border-radius: 50%;
}
.jumbotron.dash-jumbo:after {
    left: 15px;
}
.jumbotron.dash-jumbo:before {
    right: 15px;
}
.top-line {
    font-weight: 400;
}
span.red {
    font-weight: 500;
}
.left-wel {
    font-size: 22px;
    font-weight: 600;
    line-height: 1.7;
}
    .menu.active .toggle
    {
      transform: rotate(360deg);
    }
@media (max-width: 1380px) 
{
.menu-wrapper {
  /* margin-right: calc(100% - 800px); */
  max-width: 100%;
  width: 70%;
  margin-top: 30px;
  /* text-align: center; */
}
}    
    </style> 
    

</head>



<body>
       <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

          


           <div class="container">
            <div class="top-hording">
              <div class="row top-hording-wrap">
                <div class="searchbox">
                  <input type="text" class="form-control" id="search" name="search" autocomplete="off" placeholder="Search...">
                </div>
                <div class="manageusers">
                   <?php if($_SESSION['user_type']=='master'){
                    ?>
                    <div class="manage-user">
                     <a href="/users_master" class="btn btn-primary">Manage Users</a>
                     </div>
                    <?php
                    }  
                    ?>
                </div>

              </div>
            </div>


              <div class="jumbotron dash-jumbo">
                <div class="top-texter">
<div class="left-wel">
<div class="welcomgre">
 <script type="text/javascript">
  var now = new Date();
var hrs = now.getHours();
var msg = "";

if (hrs >  0) msg = "Mornin' Sunshine!"; // REALLY early
if (hrs >  6) msg = "Good morning";      // After 6am
if (hrs > 12) msg = "Good afternoon";    // After 12pm
if (hrs > 17) msg = "Good evening";      // After 5pm
if (hrs > 22) msg = "Good Night!";        // After 10pm

document.write(msg);
</script></div>
               
        </div>  

        <div class="top-line">
          Welcome to ERP <span class="red">Mr.<?php echo $_SESSION["name"]; ?></span>
        </div>           
              
          </div>        
<div class="menu-section-dash">
<div class="left-menu-wrapper">
  <ul>
    <li>
      <a href="/purchase_index.php">Purchase</a>
    </li>
    <li>
      <a href="/debit_note_register">Debit Note</a>
    </li>
    <li>
      <a href="/sales_conf_index.php">Sales Confirmation</a>
    </li>
    <li>
      <a href="/pending_bales_report">Pending Bales</a>
    </li>
    <li>
      <a href="/comparison_report">Comparision Report</a>
    </li>
    <li>
      <a href="/other_payout">Other Payout</a>
    </li>
    <li>
      <a href="/kapasiya-sales-rg">Kapasiya Sales</a>
    </li>
    <li>
      <a href="/sales_conf_index.php">Sales</a>
    </li>
  </ul>
</div>

              <div class="menu-wrapper">
               <div class="menu">
                <div class="toggle"><img src="image/navjivan-logo-circle.png" width="200" height="200"></div>

                <li style="--i:0">
                  <a title="Sales Pending Confirmation"  href="#sales_pending_section"><span class="icon-pending"></span></a>
                </li>
                <li  style="--i:1">
                  <a title="Kapasiya Sales Dashboard" href="#kapasiya_sales_dashboard"><span class="icon-seeds-svgrepo-com1"></span></a>
                </li>
                

                <li   style="--i:2">
                  <a title="QIS Report" href="#qis_report_section"><span class="icon-purchase_list"></span></a>
                </li>
                
                <li  style="--i:3">
                  <a title="Bank Stock Statement Reminders" href="#bank_stock_section1"><span class="icon-banking-svgrepo-com"></span></a>
                </li>
                <li  style="--i:4">
                  <a title="Total Courier Details" href="#total_curier_section"><span class="icon-delivery-cart-svgrepo-com"></span></a>
                </li>
                <li  style="--i:5">
                  <a title="Kapasiya Sales Reminders" href="#kapasiya_sales_reminders"><span class="icon-seed-svgrepo-com1"></span></a>
                </li>
                <li  style="--i:6">
                  <a title="Insurance Expiration Reminders" href="#insurance_exp_section"><span class="icon-insurance-svgrepo-com"></span></a>
                </li>

                <li  style="--i:7">
                  <a title="Purchase Pending Confirmation" href="#purchase_pending_section"><span class="icon-add_shopping_cart"></span></a>
                </li>



              </div>
            </div>
             </div>
             </div>
           </div>



           <div class="row">

 

                <!-- QIS Report Section -->

                <?php 
                $qisDataArr=array();
                
                $curDate=date('Y-m-d');
                $curMonth = date('m');
                $curYear = date("Y");

                // Quarter 1 Form 1 Check after 01 Jan
                if($curDate>=$curYear.'-01-01')
                {

                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter1' AND form='form1' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){

                        $qisDataArr['q1f1'][]=$firmrow['party_name'];                       
                      }
                    }else{

                        $qisDataArr['q1f1'][]=$firmrow['party_name'];                       
                    }
                  }
                }


                // Quarter 1 Form 2 Check After 01 April 
                if($curDate>=$curYear.'-04-01')
                {
                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter1' AND form='form2' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q1f2'][]=$firmrow['party_name'];
                      }
                    }else{
                      $qisDataArr['q1f2'][]=$firmrow['party_name'];
                    }
                  }        
                  
                }

                // Quarter 2 Form 1 Check After 01 April
                if($curDate>=$curYear.'-04-01')
                {
                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter2' AND form='form1' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q2f1'][]=$firmrow['party_name'];
                      }
                    }else{
                        $qisDataArr['q2f1'][]=$firmrow['party_name'];
                    }
                  } 
                  
                }

               

                // Quarter 2 Form 2 Check After 01 july
                if($curDate>=$curYear.'-07-01')
                {
                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter2' AND form='form2' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q2f2'][]=$firmrow['party_name'];
                      }

                    }else{
                       $qisDataArr['q2f2'][]=$firmrow['party_name'];
                    }
                  } 
                  
                }

                // Quarter 3 Form 1 Check After 01 july
                if($curDate>=$curYear.'-07-01')
                {
                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter3' AND form='form1' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q3f1'][]=$firmrow['party_name'];
                      }

                    }else{
                       $qisDataArr['q3f1'][]=$firmrow['party_name'];
                    }
                  } 
                  
                }



                 // Quarter 3 Form 2 Check After 01 Oct
                if($curDate>=$curYear.'-10-01')
                {
                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter3' AND form='form2' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q3f2'][]=$firmrow['party_name'];
                      }

                    }else{
                       $qisDataArr['q3f2'][]=$firmrow['party_name'];
                    }
                  } 
                  
                }


                // Quarter 4 Form 1 Check after 01 Oct
                if($curDate>=$curYear.'-10-01')
                {
                  $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$curYear."' AND quarter='quarter4' AND form='form1' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q4f1'][]=$firmrow['party_name'];
                      }

                    }else{
                       $qisDataArr['q4f1'][]=$firmrow['party_name'];
                    }
                  }          
                  
                }

             

                // Quarter 4 Form 2 Check after Next Year 01 Jan
                $backYear=$curYear-1;
                if($curDate>=$curYear.'-01-01')
                {
                 $query = "SELECT * FROM party where show_in_qis='1'";
                  $result_query = mysqli_query($conn,$query);
                  while($firmrow = mysqli_fetch_array($result_query)){
                     $sql = "select * from qis where year='".$backYear."' AND quarter='quarter4' AND form='form2' AND firm = ".$firmrow['id'];
                    $result = mysqli_query($conn, $sql);
                    $rowcount=mysqli_num_rows($result);
                    if($rowcount > 0){
                      $firm1row = mysqli_fetch_array($result);
                      if($firm1row['status'] == "pending"){
                        $qisDataArr['q4f2'][]=$firmrow['party_name'];
                      }

                    }else{
                        $qisDataArr['q4f2'][]=$firmrow['party_name'];
                    }
                  } 
                  
                }


                if(count($qisDataArr)>0)
                {
                ?>

                
                <div id="qis_report_section" class="col-md-6">

                <div class="card mt-4">
                  <div class="card-header primary_bg">

                    <div class="d-flex align-items-center">
                         <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>  QIS Report
                    </div>

                  </div>
                <div class="card-body dashboard_card_body">                 
                  
                      <?php
                        $titleArr=$arrayName = array(
                          'q1f1' => "Quarter 1 - Form 1",
                          'q1f2' => "Quarter 1 - Form 2",
                          'q2f1' => "Quarter 2 - Form 1",
                          'q2f2' => "Quarter 2 - Form 2",
                          'q3f1' => "Quarter 3 - Form 1",
                          'q3f2' => "Quarter 3 - Form 2",
                          'q4f1' => "Quarter 4 - Form 1",
                          'q4f2' => "Quarter 4 - Form 2"
                        );

                        $t=0;
                        foreach ($qisDataArr as $key => $item) 
                        {

                        ?>
                      <div class="row">
                        <div class="col-md-12">
                        <div class="card mt-2">
                            <div class="card-header">
                               <div class="d-flex justify-content-between" data-toggle="collapse" data-target="#qis<?php echo $t ?>">
                                    <?php echo $titleArr[$key] ?>

                                    <span class="badge badge-pill badge-warning ml-auto mr-2"><?php echo count($item) ?></span>

                               <i class="fas fa-chevron-down"></i>
                              </div>
                            </div>
                             <div id="qis<?php echo $t ?>"class="card-body collapse">
                              <div class="row">                   
                                <div class="col-md-12">
                                  <table width="100%" class="table table-striped">
                                    
                                    <?php
                                      foreach ($item as $key1 => $sub_item) 
                                       {

                                        ?>
                                            
                                              <tr>
                                                <td width="80%"><?php echo $sub_item ?></td>
                                                <td ><a class="btn" href="/banking-reports/qis/">
                                                    <i class="fas fa-edit"></i></a>
                                                </td>
                                              </tr>
                                            
                                        <?php

                                       }
                                    ?> 
                                    </table>                                   
                                </div>
                              </div>
                            </div>
                        </div>
                      </div>
                         </div>
                         
                        <?php 
                          $t++;                    
                        }                      
                      ?>                
                    
               
              </div>
            </div>
          </div>
        

          <?php } ?>


               <!-- QIS Report Section END-->



            <!-- Sanction Report Section -->

            <?php 
            $sanctionDataArr=array();

            $currentDate1 = new DateTime();
            $date1 = $currentDate1->add(new DateInterval('P30D'));
            $before30day = $date1->format('Y-m-d');
            $cur_date=date("Y-m-d");
            $sql = "select * from sanction where end_date <= '$before30day' AND end_date>'$cur_date'";
            $result = mysqli_query($conn, $sql);        
            foreach($conn->query($sql) as $result) 
            {
              $sanctionDataArr[]=$result; 
            }

            if(count($sanctionDataArr)>0)
            {

            ?>

             <div id="sanction_report" class="col-md-6">
        <div class="card mt-4">
            <div class="card-header primary_bg">
               <div class="d-flex align-items-center">
                 <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                    Sanction Report
              </div>
             
            </div>
             <div  class="card-body dashboard_card_body">
              <div class="row">

                <?php        
                  foreach($sanctionDataArr as $result) 
                  { 
                    ?>
               

                <div class="col-md-6">
                   <div class="card text-center">
                  <div class="card-header">

                    <b><?php echo getFirmName($result['firm']); ?></b></div>
                    <div class="card-body">
                      <h6 class="card-title">Bank Name :
                        <?php echo $result['bank_name']; ?>
                          
                        </h6>

                        <h6 class="card-title">Sanction Amount :
                        <?php echo $result['san_amount']; ?>
                          
                        </h6>
                       
                      <p class="card-text">
                        
                      </p>
                      <a href="banking-reports/sanction-letter/show.php?id=<?php echo $result['id']; ?>" class="btn btn-primary">Go</a>
                    </div>
                    <div class="card-footer text-muted">
                      Exp. Date : <?php echo date("d/m/Y", strtotime($result['end_date']));  ?>
                    </div>
                  </div>
                  

              </div>

            <?php } ?>
            </div>
          </div>
        </div>
      </div>
      
         <?php } ?>
               <!-- Sanction Report Section END-->




                
            <!-- Bank Stock Section -->


            <?php

              //check if entery pending
              $bankStockArr1=array();
              $curMonth=date('F'); 
              $curYear=date('Y'); 
              $sql = "select * from bank_stock where mnt='".$curMonth."' AND year='".$curYear."'";

              $result = mysqli_query($conn, $sql);
              $rowcount=mysqli_num_rows($result); 

              if($rowcount==0)
              {
                $bankStockArr1[]=$curMonth.'-'.$curYear;
              }


              //check if record exist but CA certified option not check
              $bankStockArr2=array();
              $sql2 = "select * from bank_stock where certified_book!=1";
              $result2 = mysqli_query($conn, $sql2);
              $rowcount2=mysqli_num_rows($result2); 
              if($rowcount2>0)
              {
                foreach($conn->query($sql2) as $result2) 
                {
                  $bankStockArr2[]=$result2;
                }
              }


              
              if(count($bankStockArr1)>0 || count($bankStockArr2)>0)
              {

               ?>
        <div id="bank_stock_section1" class="col-md-6">
            <div class="card mt-4">           
              <div class="card-header primary_bg">
               <div class="d-flex align-items-center">
                     <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                     Bank Stock Report
              </div>
            </div>

             <div class="card-body dashboard_card_body">
              <div class="row">

                <?php
                foreach ($bankStockArr1 as $key => $item)                
                {
                 ?>
               
                <div class="col-md-6">
                   <div class="card text-center">
                  <div class="card-header">
                    <b>Reminder</b></div>
                    <div class="card-body">
                      <h6 class="card-title">
                      This Month (<?php echo $item ?>)  <br>Entry Pending...                          
                        </h6>                      
                  
                    </div>
                    <div class="card-footer text-muted">
                      <a href="banking-reports/bank-stock-stmt/">Go To Bank Stock Report</a>
                    </div>
                  </div>                  
              </div>

            <?php } ?>

            <!-- check if CA not certified -->

             <?php
                  foreach($bankStockArr2 as $result2) 
                  {
                 ?>
               
                <div class="col-md-6">
                   <div class="card text-center">
                  <div class="card-header">
                    <b>Reminder</b></div>
                    <div class="card-body">
                      <h6 class="card-title">

                          Firm : <?php echo getFirmName($result2['firm']) ?><br>
                          Month : <?php echo $result2['mnt'] ?><br>
                          Year : <?php echo $result2['year'] ?><br><br>
                          <b>CA Certified - Not Marked</b>                     
                        </h6>
                       
                      <p class="card-text">
                        
                      </p>
                      
                    </div>
                    <div class="card-footer text-muted">
                      <a href="banking-reports/bank-stock-stmt/show.php?id=<?php echo $result2['id']; ?>">Go To Entry</a>
                    </div>
                  </div>
                  

              </div>
                <?php 
                    } 
                ?>
            </div>
          </div>
        </div>
      </div>
     

      <?php  } ?>


      <!-- Bank Stock Report Section END-->


       <!-- Total Courier Details START-->

          <?php 

          $totalCourierArr=array();

          $currentDate = new DateTime();
          $date = $currentDate->sub(new DateInterval('P1D'));
          $before2day = $date->format('Y-m-d');
          $cou_index=0;

          $sql = "select * from courier where send_date < '$before2day' AND rcvd_date = '0000-00-00'";
          foreach($conn->query($sql) as $result) 
          { 
            $totalCourierArr[]=$result;
          }


          if(count($totalCourierArr)>0)
          {

          ?>
          <div id="total_curier_section" class="col-md-6">
          <div class="card mt-4">
            <div class="card-header primary_bg">             
               <div class="d-flex align-items-center">
                    <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                    Total Courier Details 
              </div>
            </div>
             <div class="card-body dashboard_card_body">
              <div class="row">
               
                <div class="col-md">
                  <div class="row">

                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Sr. No.</th>
                          <th scope="col">Send Date</th>
                          <th scope="col">Docket Type</th>
                        </tr>
                      </thead>
                      <tbody>
                       <?php 
                        foreach($totalCourierArr as $result) 
                        { 
                          ?>
                        <tr>
                          <th scope="row"><a href="courier/show.php?id=<?php echo $result['id'] ?>"><?php echo $cou_index=$cou_index+1; ?></a></th>
                          <td><a href="courier/show.php?id=<?php echo $result['id'] ?>">  <?php echo date("d/m/Y", strtotime($result['send_date']));  ?></a></td>

                          <td><a href="courier/show.php?id=<?php echo $result['id'] ?>"><?php echo $result['docket_type']; ?></a></td>

                        </tr>
                        <?php 
                        }
                         ?>
                      </tbody>
                    </table>
                      
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
       
      <?php } ?>

      <!-- Total Courier Details END-->



        <!-- Kapasiya Sale Report Above 50 Lakh amount records Section START-->


            <?php

            //check if entery pending
            $kapasiyaSalesArr=array();
            $ksi=0;

            //get firm
            $sqlKp1="SELECT DISTINCT firm FROM `kapasiya`";
            $resultKp1=mysqli_query($conn,$sqlKp1);
            if(mysqli_num_rows($resultKp1)>0)
            {
              while ($rowKp1=mysqli_fetch_assoc($resultKp1)) 
              {

                //get seasonal year
                $sqlKp2="SELECT DISTINCT seasonal_year FROM `kapasiya`";
                $resultKp2=mysqli_query($conn,$sqlKp2);
                if(mysqli_num_rows($resultKp2)>0)
                {
                  while ($rowKp2=mysqli_fetch_assoc($resultKp2)) 
                  {

                    //get party
                    $sqlKp3="SELECT DISTINCT party FROM `kapasiya`";
                    $resultKp3=mysqli_query($conn,$sqlKp3);
                    if(mysqli_num_rows($resultKp3)>0)
                    {
                      while($rowKp3=mysqli_fetch_assoc($resultKp3)) 
                      {

                        //get total
                        $total=0;

                        $sqlTotal="select * from kapasiya where party='".$rowKp3['party']."' AND seasonal_year='".$rowKp2['seasonal_year']."' AND firm='".$rowKp1['firm']."'";
                        $resultTotal=mysqli_query($conn,$sqlTotal);
                        if(mysqli_num_rows($resultTotal)>0)
                        {
                          while($rowTotal=mysqli_fetch_assoc($resultTotal)) 
                          {
                            $jsonArr=json_decode($rowTotal['truck'],true);
                            foreach ($jsonArr as $kitem) 
                            {
                              $total+=(float)$kitem['final_amt'];
                            }


                          }
                        }

                        //if total goes above 50 Lakh then add record in array
                        if($total>=5000000)
                        {
                          $kapasiyaSalesArr[$ksi]['firm']=getFirmName($rowKp1['firm']);
                          $kapasiyaSalesArr[$ksi]['ext_party']=getExtPartyName($rowKp3['party']);
                          $kapasiyaSalesArr[$ksi]['seasonal_year']=getSeasonalYear($rowKp2['seasonal_year']);
                          $kapasiyaSalesArr[$ksi]['total']=$total;

                          $ksi++;

                        }

                      }
                    }
                  }
                } 
              }
            }
              


              
              if(count($kapasiyaSalesArr)>0)
              {

               ?>
        <div id="kapasiya_sales_reminders" class="col-md-6">
            <div class="card mt-4">           
              <div class="card-header primary_bg">
               <div class="d-flex align-items-center">
                     <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                     Kapasiya Sales Report
              </div>
            </div>

             <div class="card-body dashboard_card_body">
              <div class="row">

                <?php
                foreach ($kapasiyaSalesArr as $key => $item)                
                {
                 ?>
               
                <div class="col-md-6">
                   <div class="card mt-2 text-center">
                  <div class="card-header">
                    <b> <?php echo $item['ext_party'] ?></b>
                     <div style="font-size: 11px;"><?php echo $item['firm']; ?></div>
                    <div style="font-size: 11px;">Year : <?php echo $item['seasonal_year']; ?></div>
                  </div>
                    <div class="card-body">
                      

                       Total Amount : <?php echo $item['total'] ?>                     
                  
                    </div>
                    <div class="card-footer text-muted">
                   
                  
                    </div>
                  </div>                  
              </div>

            <?php } ?>

            <!-- check if CA not certified -->

            </div>
          </div>
        </div>
      </div>
     

      <?php  } ?>


      <!-- Kapasiya Sale Report Above 50 Lakh amount records Section END-->




      <!-- Insurance Upcoming Expiration Details START-->

           <?php 
            $insuranceArr=array();

            $currentDate1 = new DateTime();
            $date1 = $currentDate1->add(new DateInterval('P15D'));
            $before15day = $date1->format('Y-m-d');
            $cur_date=date("Y-m-d");
            $ins_index=0;

            $sql = "select i.*,p.party_name from insurance i,party p where i.firm_id=p.id AND i.end_date <= '$before15day' AND i.end_date>'$cur_date' AND status=1";
            $result = mysqli_query($conn, $sql);        
            foreach($conn->query($sql) as $result) 
            {
               $insuranceArr[]=$result; 
            }
            if(count($insuranceArr)>0)
            {

          ?>

          <div id="insurance_exp_section" class="card mt-4">
            <div class="card-header primary_bg">
              
               <div class="d-flex align-items-center">
                     <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                     Insurance Upcoming Expiration Details
              </div>
            </div>
             <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                  <div class="row">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Sr. No.</th>
                          <th scope="col">Policy No.</th>
                          <th scope="col">Vehicle No.</th>
                          <th scope="col">Firm</th>
                          <th scope="col">Policy Type</th>
                          <th scope="col">Sum Assured Amount</th>
                          <th scope="col">Policy End Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        foreach($insuranceArr as $result) 
                        { 
                        ?>
                        <tr>
                          <td scope="row"><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo $ins_index=$ins_index+1; ?></a></td>

                          <td><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo $result['policyno']; ?></a></td>

                          <td><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo $result['vehicle_no']; ?></a></td>

                          <td><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo $result['party_name']; ?></a></td>

                          <td><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo $result['ins_type']; ?></a></td>

                          <td><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo $result['sum_ass']; ?></a></td>

                          <td><a href="insurance/show.php?id=<?php echo $result['id'] ?>"><?php echo date("d/m/Y", strtotime($result['end_date']));  ?></a></td>

                        </tr>
                        <?php 
                        }

                         ?>
                      </tbody>
                    </table>                
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php } ?>

      <!-- Insurance Upcoming Expiration Details END-->




       <!-- Purchase Pending Confirmation START-->

           <?php 
            $purchaseArr=array();

            $sql = "select * from pur_conf where conf_type!='2'";
            $result = mysqli_query($conn, $sql);        
            foreach($conn->query($sql) as $key => $result) 
            {
              $total_bales=$result['bales'];

              //get used bales from purchase report
              $sqlPurReport="SELECT IFNULL(SUM(bales), 0) AS used_bales FROM `pur_report` where pur_conf_ids='".$result['id']."'";
              $resultPurReport=mysqli_query($conn,$sqlPurReport);
              $rowPurReport=mysqli_fetch_assoc($resultPurReport);
              $used_bales=$rowPurReport['used_bales'];

              if($total_bales!=$used_bales)
              {
                $purchaseArr[$key]=$result; 
                $purchaseArr[$key]['avl_bales']=$total_bales-$rowPurReport['used_bales']; 
              }
            }
            if(count($purchaseArr)>0)
            {

          ?>

          <div id="purchase_pending_section" class="card mt-4">
            <div class="card-header primary_bg">
              
               <div class="d-flex align-items-center">
                     <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                     Purchase Pending Confirmation Report
              </div>
            </div>
             <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                  <div class="row">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Sr. No.</th>
                          <th scope="col">Firm</th>
                          <th scope="col">Financial Year</th>
                          <th scope="col">External Party</th>
                          <th scope="col">Confirmation No.</th>
                          <th scope="col">Available Bales</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $ins_index=0;
                        foreach($purchaseArr as $result) 
                        { 
                        ?>
                        <tr>
                          <td scope="row"><?php echo $ins_index=$ins_index+1; ?></td>

                          <td><?php echo getFirmName($result['firm']); ?></td>

                          <td><?php echo getFinancialYear($result['financial_year']); ?></td>

                          <td><?php echo getExtPartyName($result['party']); ?></td>

                          <td><?php echo $result['pur_conf']; ?></td>

                           <td><?php echo $result['avl_bales']; ?></td>


                        </tr>
                        <?php 
                        }

                         ?>
                      </tbody>
                    </table>                
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php } ?>

      <!-- Purchase Pending Confirmation END-->



        <!-- Sales Pending Confirmation START-->

           <?php 
            $salesArr=array();

            $s=0;
            $sql = "select * from seller_conf where conf_type!='2'";
            $result = mysqli_query($conn, $sql);        
            foreach($conn->query($sql) as $key=> $result) 
            {

               $total_bales=$result['cont_quantity'];

              //get used bales from sales report
              $sqlSalesReport="SELECT IFNULL(SUM(noOFBales), 0) AS used_bales FROM `sales_report` where conf_no='".$result['sales_conf']."' AND sales_ids='".$result['id']."'";
              $resultSalesReport=mysqli_query($conn,$sqlSalesReport);
              $rowSalesReport=mysqli_fetch_assoc($resultSalesReport);
              $used_bales=$rowSalesReport['used_bales'];

              if($total_bales!=$used_bales)
              {
                $salesArr[$s]['table']="Sales Confirmation";
                $salesArr[$s]['partyname']=getExtPartyName($result['external_party']);
                $salesArr[$s]['firmname']=getFirmName($result['firm']);
                $salesArr[$s]['fyear']=getFinancialYear($result['financial_year_id']);
                $salesArr[$s]['conf_no']=$result['sales_conf'];
                $salesArr[$s]['avl_bales']=$total_bales-$used_bales; 
                $s++;
              }
            }

       


            $sql = "select * from sales_conf_split where conf_type!='2'";
            $result = mysqli_query($conn, $sql);        
            foreach($conn->query($sql) as $key => $result) 
            {

               $total_bales=$result['no_of_bales'];

              //get used bales from sales report
              $sqlSalesReport="SELECT IFNULL(SUM(noOFBales), 0) AS used_bales FROM `sales_report` where conf_no='".$result['conf_split_no']."' AND sales_ids='".$result['id']."'";
              $resultSalesReport=mysqli_query($conn,$sqlSalesReport);
              $rowSalesReport=mysqli_fetch_assoc($resultSalesReport);
              $used_bales=$rowSalesReport['used_bales'];

              if($total_bales!=$used_bales)
              {
                 //$salesArr[]=$result; 
                 $salesArr[$s]['table']="Sales Confirmation Split";
                 $salesArr[$s]['partyname']=getExtPartyName($result['split_party_name']);
                 $salesArr[$s]['firmname']=getFirmName($result['firm']);
                 $salesArr[$s]['fyear']=getFinancialYear($result['financial_year_id']);
                 $salesArr[$s]['conf_no']=$result['conf_split_no'];
                 $salesArr[$s]['avl_bales']=$total_bales-$used_bales; 
                 $s++;
              }
            }


            if(count($salesArr)>0)
            {

          ?>

          <div id="sales_pending_section" class="card mt-4">
            <div class="card-header primary_bg">
              
               <div class="d-flex align-items-center">
                     <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                     Sales Pending Confirmation Report
              </div>
            </div>
             <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                  <div class="row">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Sr. No.</th>
                          <th scope="col">Firm</th>
                          <th scope="col">Financial Year</th>
                          <th scope="col">External Party</th>
                          <th scope="col">Confirmation No.</th>
                          <th scope="col">Available Bales</th>
                          <th scope="col">Table</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $ins_index=0;
                        foreach($salesArr as $result) 
                        { 
                        ?>
                        <tr>
                          <td scope="row"><?php echo $ins_index=$ins_index+1; ?></td>

                          <td><?php echo $result['firmname']; ?></td>

                          <td><?php echo $result['fyear']; ?></td>

                          <td><?php echo $result['partyname']; ?></td>

                          <td><?php echo $result['conf_no']; ?></td>

                          <td><?php echo $result['avl_bales']; ?></td>

                          <td><?php echo $result['table']; ?></td>


                        </tr>
                        <?php 
                        }

                         ?>
                      </tbody>
                    </table>                
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php } ?>

      <!-- Sales Pending Confirmation END-->




     


      <!-- BANK STOCK STATEMENT START-->

          <?php

              $bankStockArr3=array();
              $bindex=0;
              $month = date('F', strtotime("-1Months"));
              $currentDate = new DateTime();
              $first_date = date('d',strtotime('first day of this month'));
              $ten = date('20');
              $one = date('1');
              $c = date('d');

              if ($c>=$one && $c<=$ten) 
              {
                 $sql = "select * from bank_stock where mnt = '$month' ";
                  $result = mysqli_query($conn, $sql);        
                  foreach($conn->query($sql) as $result) 
                  { 
                     $bankStockArr3[]=$result;
                  }
              }

              if(count($bankStockArr3)>0)
              {

          ?>

          <div class="card mt-4">
    <div class="card-header primary_bg">
              
               <div class="d-flex align-items-center">
                 <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                   Bank Stock Statement
              </div>
    </div>
       <div id="bank_stock_Section2"class="card-body">
        <div class="row">
               
                <div class="col-md">
                  <div class="row">

                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th scope="col">Sr. No.</th>
                          <th scope="col">Month</th>
                          <th scope="col">Firm Name</th>
                          <th scope="col">CA certified</th>
                        </tr>
                      </thead>
                      <tbody>
                       <?php        
                            foreach($bankStockArr3 as $result) 
                            { 
                        ?>
                            <tr>
                            <td scope="row">
                              <?php echo $bindex+=1; ?>
                            </td>
                            <td>
                              <?php echo $result['mnt'];?>
                            </td>

                            <td>
                              <?php echo getFirmName($result['firm']); ?>
                            </td>
                            <td>
                              <?php 

                                if ($result['certified_book'] == 1) {
                                  echo "Check";
                                }else{
                                  echo "Not Check";
                                }

                              ; ?>
                            </td>

                        </tr>
                        <?php }
                      
                         ?>
                      </tbody>
                    </table>            
                  </div>

                </div>
        </div>
      </div>
  </div>

<?php } ?>

 <!-- BANK STOCK STATEMENT end-->



 <!-- Kapasiya Sales Dashboard  START-->


         <?php
        $kapasiyaDataArr=array();
        $sql = "select * from kapasiya order by id DESC";
        $result = mysqli_query($conn, $sql);
        foreach($conn->query($sql) as $key1 => $result) 
        { 
          $kapasiyaDataArr[]=$result;
        }

        if(count($kapasiyaDataArr)>0)
        {

         ?>
  <div id="kapasiya_sales_dashboard" class="card mt-4">
    <div class="card-header primary_bg">
             
               <div class="d-flex align-items-center">
                 <span class="fa fa-chart-line mr-2 dashboard_card_icon secondary_color"></span>
                    Kapasiya Sales Dashboard 
              </div>
    </div>
       <div id="kapasiya_sales_section" class="card-body">
        <div class="row">
            <style type="text/css">
               .foot1 {
                          display: table-header-group;
                      }
            </style>
               
                <div class="col-md">
                  <div class="row table-responsive ">
                    <table id="kapasiyaSalesReport" class="table table-striped">

                      <thead>
                        <tr>
                        
                          <th>ID</th>
                          <th>Firm</th>
                          <th>Confirmation Date</th>
                          <th>External Party</th>
                          <th>Rate</th>
                          <th>Total Trucks</th>
                          <th>Sales Trucks</th>
                          <th>Credit</th>
                          <th>Pending Truck</th>
                          
                        
                        </tr>
                      </thead>
                     
                      <tbody>
                       <?php 

                            $index=0;
                            $id=0;
                            foreach($kapasiyaDataArr as $key1 => $result) 
                            { 


                              $firm_name='';
                              $sql2 = "select * from party where id='".$result['firm']."'";
                              $result2 = mysqli_query($conn, $sql2);
                              if(mysqli_num_rows($result2)>0)
                              {
                                 $row_firm4=mysqli_fetch_array($result2);
                                $firm_name= $row_firm4['party_name'];
                              }


                              $ext_party='';
                              $sql2 = "select * from external_party where id='".$result['party']."'";
                              $result2 = mysqli_query($conn, $sql2);
                              $row_party4=mysqli_fetch_array($result2);
                              $ext_party = $row_party4['partyname'];


                              $truckArr = json_decode($result['truck'],true);


                              $sales_trcuk=0;
                              $pending_truck=0;
                              $total_truck=$result['no_of_truck'];

                              foreach ($truckArr as $key => $itm1) {
                                  
                                  if($itm1['truck_complete']=='1')
                                  {
                                    $sales_trcuk+=1;
                                  }

                              }

                              $pending_truck=$total_truck-$sales_trcuk;

                              ?>
                                  <tr>
                                   
                                <td><?php echo $index=$index+1; ?></td>
                                <td>
                                  <?php echo $firm_name ?>
                                </td>
                                 <td>
                                  <?php

                                  $conf_date='';
                                    if($result['conf_date']!='' && $result['conf_date']!='0000-00-00')
                                    {
                                      $conf_date = str_replace('-', '/', $result['conf_date']);
                                      $conf_date = date('d/m/Y', strtotime($conf_date));
                                    }

                                  echo $conf_date; 

                                  ?>
                                </td>
                                <td>
                                  <?php echo $ext_party ?>
                                </td>
                                <td>
                                  <?php echo $result['rate'] ?>
                                </td>
                                <td><span class="badge badge-pill badge-info"><?php echo $total_truck; ?></span>
                                  
                                </td>

                                                             
                                <td><span class="badge badge-pill badge-success"><?php echo $sales_trcuk; ?></span>
                                </td>

                                <td>
                                  <?php echo $result['credit'] ?>
                                </td>
                                      
                                <td>
                                  <span class="badge badge-pill badge-warning"><?php echo $pending_truck; ?></span>
                                </td>

                                

                               </tr>


                            <?php 
                                
                                $id++;
                              
                            

                              ?>
                            
                        <?php }
                        


                         ?>
                      </tbody>
                    </table>


                      
                  </div>

                </div>
        </div>
      </div>
  </div>
<?php } ?>

  <!-- Kapasiya Sales Dashboard end-->






    

        </div>
    </div>
    

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

  <!-- Datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">

     <script src="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.js"></script>




     
    <script type="text/javascript">
        $(document).ready(function () {
            

           
             $('#kapasiyaSalesReport').DataTable( {
                "paging":   false,
                "ordering": false,
                "info":     false,
                searching: false
            });

        });
    </script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script type="text/javascript">
    let toggle = document.querySelector('.toggle');
    let menu = document.querySelector('.menu');
    toggle.onclick = function()
    {
      menu.classList.toggle('active')
    }
  </script>

  <script type="text/javascript">
        $(document).ready(function () {
             var availableTags = [
                
                {value:"Sales", link:'/sales_conf_index.php'},
                {value:"Sales Confirmation", link:'/sales_conf_index.php'},
                {value:"Sales Confirmation Split", link:'/sales_conf_index.php'},
                {value:"Sales Report", link:'/sales_conf_index.php'},
                {value:"Sales Recievable", link:'/sales_conf_index.php'},
                // {value:"TDS/TCS Declaration (Sales)", link:'/sales_conf_index.php'},
                {value:"Sales Register", link:'/sales_register'},
                {value:"Payment Recivied Register", link:'/payment_recivied_register'},
                {value:"Pending Bales Report", link:'/pending_bales_report'},
                

                {value:"Kapasiya Sales Report", link:'/kapasiya-sales-rg'},
                // {value:"TDS/TCS Declaration (Kapasiya)", link:'/kapasiya-sales-rg'},
                {value:"Kapasiya Annual Sales", link:'/kapasiya_annual_sales'},
                {value:"Kapasiya Sales Register", link:'/kapasiya_sales_register'},
                {value:"TDS/TCS Declaration Sales", link:'/tds_tcs_declaration_sales/index.php?module=sales'},


               {value:"Purchase Confirmation", link:'/purchase_index.php'},
               {value:"Purchase Report", link:'/purchase_index.php'},
               {value:"Purchase Debit Report", link:'/purchase_index.php'},
               {value:"Purchase Bales Payout", link:'/purchase_index.php'},
               {value:"Transport Payout", link:'/purchase_index.php'},
               {value:"RD Kapas Purchase Report", link:'/purchase_index.php'},
               {value:"RD Kapas Purchase Payment", link:'/purchase_index.php'},
               {value:"URD Kapas Purchase & Payment", link:'/purchase_index.php'},
            //   {value:"TDS/TCS Declaration (Purchase)", link:'/purchase_index.php'},

                {value:"Purchase Register", link:'/purchase_register'},
                {value:"Debit Note Register", link:'/debit_note_register'},
                {value:"Comparison Report", link:'/comparison_report'},
                {value:"Comparison Pending Register", link:'/comparison_pending_register'},
                {value:"Comparison Report Excel Export", link:'/comparison_report_excel_export'},
                {value:"Bill 2 Bill Payment", link:'/bill2bill_payment'},
                {value:"Bank Transaction", link:'/bank_transation.php'},
                {value:"RD Kapas Report", link:'/rd_kapas_report_export'},
                {value:"TDS/TCS Declaration Purchase", link:'/tds_tcs_declaration_sales/index.php?module=purchase'},

                {value:"Other Payout", link:'/other_payout'},
                {value:"Daily Payment Report", link:'/daily_payment_report'},
                {value:"Ledger Purchase", link:'/ledger-purchase'},
                {value:"Ledger Sales", link:'/ledger-sale'},


                {value:"Financial Year Master", link:'/financial-year'},
                {value:"Seasonal Year Master", link:'/seasonal-year'},
                {value:"Firm Master", link:'/firm'}, 
                {value:"Product Master", link:'/products'},            
                {value:"Broker Master", link:'/broker'},
                {value:"Truck Master", link:'/truck-master'},
                {value:"Transport Master", link:'/transport'},
                {value:"External Party Master", link:'/external-party'},
                {value:"Organization Master", link:'/organization-master'},
                {value:"Laboratory Master", link:'/laboratory-master'},
                {value:"External Party Banks", link:'/external-banks'},
                {value:"Farmer Master", link:'/farmer'},

                {value:"Bank Stock Statement", link:'/banking-reports/bank-stock-stmt'},
                {value:"CMA Report", link:'/banking-reports/cma'},
                {value:"External Credit Rating", link:'/banking-reports/external-credit-rating'},
                {value:"QIS Report", link:'/banking-reports/qis'},
                {value:"Sanction Report", link:'/banking-reports/sanction-letter'},
                {value:"Stock Audit Report", link:'/banking-reports/stock-auditor-detail'},

                {value:"Assessment Report", link:'/assessment'},
                {value:"Audit & ITR Report", link:'/audit-report'},
                // {value:"TDS/TCS Declaration Received", link:'/tds_tcs_declaration_received'},

                {value:"Insurance", link:'/insurance'},
                {value:"Courier", link:'/courier'},
                {value:"Personal KYC", link:'/personal-kyc'},
                {value:"Employee Salary", link:'/emp-salary'},
                {value:"Hundi Transaction", link:'/external_party_transaction'},
                
                
                
              ];
              $( "#search" ).autocomplete({
                source: availableTags,
                select: function( event, ui ) 
                {
                  console.log(ui.item.link);
                  window.location.replace(ui.item.link)
                }
              });
              
        });
    </script>


</body>

</html>
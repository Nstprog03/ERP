<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}



function getExternalPartyDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

    $arr[0]=$partyrow['partyname'];

    $arr[1]=$partyrow['address'].', '.$partyrow['city'].', '.$partyrow['district'].', '.$partyrow['state'].' - '.$partyrow['pincode'];

    return $arr;

}

function getBrokerDetails($id)
{
    $arr=array();
    include('../db.php');
    $brokerSQL = "select * from broker where id='".$id."'";
    $brokerResult = mysqli_query($conn, $brokerSQL);

    $brokerRow = mysqli_fetch_array($brokerResult);

    $arr[]=$brokerRow['name'];

    return $arr;
}

function getFirmDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

   $firm=$partyrow['party_name'];

    return $firm;

}


//dd/mm/yyy
function convertDate($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }


    return $final_date;

}


if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  

  if(isset($_POST['submit']))
  {


      $main_query="select * from debit_report where";

      $start_date='';
      $end_date='';
      $where_cond = array();
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));
        $where_cond[] = " debit_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

       
        $where_cond[] = " debit_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " debit_date>='".$start_date."' AND debit_date<='".$end_date."'";

      }

      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);
        $where_cond[] = " firm in (".$firm.")";
        
      }

      if(isset($_POST['ext_party']))
      {
        $ext_party="'".implode("','",$_POST['ext_party'])."'";
        $where_cond[] = " party in (".$ext_party.")";
      }

      if(isset($_POST['broker']))
      {
        $broker="'".implode("','",$_POST['broker'])."'";
        $where_cond[] = " broker in (".$broker.")";
      }

 
      if(!empty($where_cond))
      {
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where.' order by debit_date DESC';
      }else
      {

        $main_query="select * from debit_report order by debit_date DESC";
      }



      $mainArr  = array();
      $i=0;
  
      $debitResult = mysqli_query($conn, $main_query);

         while($debit_row = mysqli_fetch_assoc($debitResult))
         {

            $mainArr[$i]['firm'] = getFirmDetails($debit_row['firm']);
            $mainArr[$i]['party_name'] = getExternalPartyDetails($debit_row['party'])[0];
            $mainArr[$i]['debit_date'] = convertDate($debit_row['debit_date']);
            $mainArr[$i]['invoice_no'] = $debit_row['invoice_no'];

            $mainArr[$i]['broker'] = getBrokerDetails($debit_row['broker'])[0];


            //get total amount from purchase report
              $purSQL = "select * from pur_report where id='".$debit_row['pur_report_id']."'";
              $purResult = mysqli_query($conn, $purSQL);
              $pur_row = mysqli_fetch_array($purResult);

              $mainArr[$i]['total_amount']=$pur_row['netpayableamt'];
              $mainArr[$i]['weight']=$debit_row['weight'];
              $mainArr[$i]['no_of_bales']=$pur_row['bales'];

              $mainArr[$i]['bill_date'] = convertDate($pur_row['report_date']);

              $mainArr[$i]['lot_no']=$debit_row['lot_no'];

              $mainArr[$i]['start_pr']=$debit_row['pr_start'];

              $mainArr[$i]['end_pr']=$debit_row['pr_end'];

              //RD
              $mainArr[$i]['rd_con']=$debit_row['rd_con'];
              $mainArr[$i]['rd_lab']=$debit_row['rd_lab'];
              $mainArr[$i]['rd_diff']=$debit_row['rd_diff'];
              $mainArr[$i]['rd_cndy']=$debit_row['rd_cndy'];
              $mainArr[$i]['rd_amt']=$debit_row['rd_amt'];

              //Length
              $mainArr[$i]['len_con']=$debit_row['len_con'];
              $mainArr[$i]['len_lab']=$debit_row['len_lab'];
              $mainArr[$i]['len_diff']=$debit_row['len_diff'];
              $mainArr[$i]['len_cndy']=$debit_row['len_cndy'];
              $mainArr[$i]['len_amt']=$debit_row['len_amt'];

               //mic
              $mainArr[$i]['mic_con']=$debit_row['mic_con'];
              $mainArr[$i]['mic_lab']=$debit_row['mic_lab'];
              $mainArr[$i]['mic_diff']=$debit_row['mic_diff'];
              $mainArr[$i]['mic_cndy']=$debit_row['mic_cndy'];
              $mainArr[$i]['mic_amt']=$debit_row['mic_amt'];


               //trash
              $mainArr[$i]['trs_con']=$debit_row['trs_con'];
              $mainArr[$i]['trs_lab']=$debit_row['trs_lab'];
              $mainArr[$i]['trs_diff']=$debit_row['trs_diff'];
              $mainArr[$i]['trs_amt']=$debit_row['trs_amt'];

                //moisture
              $mainArr[$i]['mois_con']=$debit_row['mois_con'];
              $mainArr[$i]['mois_lab']=$debit_row['mois_lab'];
              $mainArr[$i]['mois_diff']=$debit_row['mois_diff'];
              $mainArr[$i]['mois_amt']=$debit_row['mois_amt'];

                 //sample
              $mainArr[$i]['sample_kg']=$debit_row['sample_kg'];
              $mainArr[$i]['sample_amt']=$debit_row['sample_amt'];

                //tare
              $mainArr[$i]['tare_kg']=$debit_row['tare_kg'];
              $mainArr[$i]['tare_amt']=$debit_row['tare_amt'];

                 //brokrage
              $mainArr[$i]['brok_per_bales']=$debit_row['brok_per_bales'];
              $mainArr[$i]['brok_amt']=$debit_row['brok_amt'];


              //interest
              if($debit_row['int_option']=='dynamic')
              {
                 $mainArr[$i]['int_days']=15;
                 $mainArr[$i]['interest']=$debit_row['interest'];
              }
              else
              {
                $mainArr[$i]['int_days']=$debit_row['int_days'];
                $mainArr[$i]['interest']=$debit_row['interest'];
              }


                  //rate diff
              $mainArr[$i]['rate_diff_candy']=$debit_row['rate_diff_candy'];
              $mainArr[$i]['rate_diff_amount']=$debit_row['rate_diff_amount'];


              //weight shortage
              $mainArr[$i]['shortage']=$debit_row['shortage'];
              $mainArr[$i]['shortage_amt']=$debit_row['shortage_amt'];





                //repressing
              $mainArr[$i]['repress_per_bales']=$debit_row['repress_per_bales'];
              $mainArr[$i]['repress_total']=$debit_row['repress_total'];


                //other
              $mainArr[$i]['other_reason']=$debit_row['other_reason'];
              $mainArr[$i]['other_amount']=$debit_row['other_amount'];

                 //debit_amount
              $mainArr[$i]['total_debit_amount']=$debit_row['debit_amount'];
         
          




          
           $i++;
         
      }
     
 

    $_SESSION['debit_note_register_export_data']=$mainArr;

  


  }




?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Debit Note Register</title>
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

      <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">

      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

       
       <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 








     <script> 

    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

       $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });

       $(".datepicker").keydown(false);

       $('.searchDropdown').selectpicker();
      

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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Debit Note Register</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
               <ul class="navbar-nav">

             

               <!--  <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li> -->

              </ul>
          </div>
        </div>
      </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">

         
                <div class="card">
                  <div class="card-header">Filter</div>
                      <div class="card-body">
                        <form class="" action="" method="post" enctype="multipart/form-data">
                    <div class="row justify-content-center">

                      <div class="form-group col-md-4">
                            <label for="firm">Select Firm</label>
                                <?php
                                    $sql = "select * from party";
                                    $result = mysqli_query($conn, $sql);
                                ?>                      
                        <select name="firm[]" class="form-control searchDropdown" data-live-search="true" title="Select Option" multiple> 
                              
                            
                            <?php                   
                                foreach ($conn->query($sql) as $result) 
                                {
                                   if(isset($_POST['firm']) && in_array($result['id'], $_POST['firm']))
                                   {
                                        echo "<option  value='".$result['id']."' selected>".$result['party_name']. "</option>";
                                   }
                                   else
                                   {
                                        echo "<option  value='".$result['id']."'>".$result['party_name']. "</option>";
                                   }
                                }
                            ?>                              
                            </select>
                        </div>


                         

                             <div class="form-group col-md-4">
                              <label for="party">Select External Party</label>
                              <?php
                                $sql = "select * from external_party";
                                $result = mysqli_query($conn, $sql);
                              ?>                      
                              <select name="ext_party[]" class="form-control searchDropdown" data-live-search="true" title="Select Option" multiple>
                                
                                <?php                   
                                  foreach ($conn->query($sql) as $result) 
                                  {
                                    if(isset($_POST['ext_party']) && in_array($result['id'], $_POST['ext_party']))
                                     {
                                          echo "<option  value='".$result['id']."' selected>" .$result['partyname']. "</option>";
                                     }
                                     else
                                     {
                                          echo "<option  value='".$result['id']."'>" .$result['partyname']. "</option>";
                                     }
                                  }
                                ?>                              
                              </select>
                            </div>

                      <div class="form-group col-md-4">
                          <label for="broker">Select Broker</label>
                          <?php
                            $Broker_sql = "select * from broker";
                            $Broker_result = mysqli_query($conn, $Broker_sql);                            
                          ?>                      
                          <select name="broker[]" class="form-control searchDropdown" data-live-search="true" title="Select Option" multiple>
                            <?php                   
                              foreach ($conn->query($Broker_sql) as $Broker_result) 
                              {
                                if(isset($_POST['broker']) && in_array($Broker_result['id'], $_POST['broker']))
                                 {
                                     echo "<option  value='".$Broker_result['id']."' selected>" .$Broker_result['name']. "</option>";
                                 }
                                 else
                                 {
                                     echo "<option  value='".$Broker_result['id']."'>" .$Broker_result['name']. "</option>";
                                 }
                              }
                            ?>                              
                          </select>
                        </div>




                             <div class="form-group col-md-6">
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} ?>" autocomplete="off">
                            </div>

                            <div class="form-group col-md-6">
                              <label for="end_date">End Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} ?>" autocomplete="off">
                            </div>

                          
                      </div>
                        <div class="row">
                           <div class="form-group col-md-1">
                            <button type="submit" name="submit" class="btn btn-primary waves">Filter</button>
                          </div>
                          <div class="form-group col-md-1">
                            <button type="submit" name="clearFilter" class="btn btn-danger waves">Clear Filter</button>
                          </div>
                           
                         </div>
                        </form>
                        
                     </div>
                </div>
           

                <div class="card mt-4">
                   
                      <div class="card-body export-data expoert-register">

                        <?php
                          if(isset($_POST['submit']))
                          {
                            ?>
                            <div class="export-cta">
                             
                                
                              <a href="export.php" name="export" class="btn btn-info">Export To Excel</a>
                              
                         
                            </div>
                            <br>
                            <?php
                          }
                        ?>
                      <table id="example" class="registertable table table-striped table-bordered" style="width:100%">
                        <thead>

                          <tr>

                            <th rowspan="2">ID</th>
                            <th rowspan="2">Firm</th>
                            <th rowspan="2">Party</th>
                            <th rowspan="2">Broker</th>
                            <th rowspan="2">Debit Report Date</th>
                            <th rowspan="2">Bill Date</th>
                            <th rowspan="2">Invoice No</th>
                            <th rowspan="2">Total Amount</th>
                            <th rowspan="2">Weight</th>
                            <th rowspan="2">No. Of Bales</th>
                            <th rowspan="2">Lot No.</th>
                            <th rowspan="2">Start PR</th>
                            <th rowspan="2">End PR</th>
                            <th colspan="5">RD</th>
                            <th colspan="5">Length</th>
                            <th colspan="5">MIC</th>
                            <th colspan="4">Trash</th>
                            <th colspan="4">Moisture</th>
                            <th colspan="2">Sample</th>
                            <th colspan="2">Tare</th>
                            <th colspan="2">Brokerage</th>
                            <th colspan="2">Weight Shortage</th>
                            <th colspan="2">Interest</th>
                            <th colspan="2">Rate Difference</th>
                            <th colspan="2">RePressing</th>
                            <th colspan="2">Other</th>
                            <th rowspan="2">Total Debit Amount</th>
                                
                           </tr>

                            <tr>
                                
                                <!-- RD -->
                               
                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Deduction</th>
                                <th>Amount</th>


                                 <!-- Length -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Deduction</th>
                                <th>Amount</th>




                                 <!-- MIC -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Deduction</th>
                                <th>Amount</th>

                                 <!-- Trash -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>                              
                                <th>Amount</th>


                                <!-- Moisture -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Amount</th>


                                <!-- Sample -->

                                <th>KG</th>
                                <th>Amount</th>


                                <!-- Tare -->

                                <th>KG</th>
                                <th>Amount</th>

                                  <!-- Brokerage -->

                                <th>Per Bales</th>
                                <th>Amount</th>


                                 <!-- Weight Shortage -->
                                <th>Shortage</th>
                                <th>Shortage Amount</th>


                                  <!-- Interest -->

                                <th>Days</th>
                                <th>Amount</th>

                                   <!-- rate Difference -->

                                <th>Deduct</th>
                                <th>Amount</th>

                                 


                                  <!-- Repressing -->

                                <th>Deduct</th>
                                <th>Amount</th>


                                  <!-- Other -->

                                <th>Reason</th>
                                <th>Amount</th>



                              </tr>
                        </thead>
                        <tfoot>
                          
                           
                          <tr>

                            <th rowspan="2">ID</th>
                            <th rowspan="2">Firm</th>
                            <th rowspan="2">Party</th>
                            <th rowspan="2">Broker</th>
                            <th rowspan="2">Debit Report Date</th>
                            <th rowspan="2">Bill Date</th>
                            <th rowspan="2">Invoice No</th>
                            <th rowspan="2">Total Amount</th>
                            <th rowspan="2">Weight</th>
                            <th rowspan="2">No. Of Bales</th>
                            <th rowspan="2">Lot No.</th>
                            <th rowspan="2">Start PR</th>
                            <th rowspan="2">End PR</th>
                            <th colspan="5">RD</th>
                            <th colspan="5">Length</th>
                            <th colspan="5">MIC</th>
                            <th colspan="4">Trash</th>
                            <th colspan="4">Moisture</th>
                            <th colspan="2">Sample</th>
                            <th colspan="2">Tare</th>
                            <th colspan="2">Brokerage</th>
                            <th colspan="2">Weight Shortage</th>
                            <th colspan="2">Interest</th>
                            <th colspan="2">Rate Difference</th>
                            <th colspan="2">RePressing</th>
                            <th colspan="2">Other</th>
                            <th rowspan="2">Total Debit Amount</th>
                                
                           </tr>

                            <tr>
                                
                                <!-- RD -->
                               
                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Deduction</th>
                                <th>Amount</th>


                                 <!-- Length -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Deduction</th>
                                <th>Amount</th>




                                 <!-- MIC -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Deduction</th>
                                <th>Amount</th>

                                 <!-- Trash -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>                              
                                <th>Amount</th>


                                <!-- Moisture -->

                                <th>Condition</th>
                                <th>Lab</th>
                                <th>Difference</th>
                                <th>Amount</th>


                                <!-- Sample -->

                                <th>KG</th>
                                <th>Amount</th>


                                <!-- Tare -->

                                <th>KG</th>
                                <th>Amount</th>

                                  <!-- Brokerage -->

                                <th>Per Bales</th>
                                <th>Amount</th>


                                 <!-- Weight Shortage -->
                                <th>Shortage</th>
                                <th>Shortage Amount</th>


                                  <!-- Interest -->

                                <th>Days</th>
                                <th>Amount</th>

                                   <!-- rate Difference -->

                                <th>Deduct</th>
                                <th>Amount</th>

                                 


                                  <!-- Repressing -->

                                <th>Deduct</th>
                                <th>Amount</th>


                                  <!-- Other -->

                                <th>Reason</th>
                                <th>Amount</th>



                              </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($mainArr)>0) {

                            
                            
                            $i=0;
                            foreach ($mainArr as $key => $row) {
                               
                              
                            ?>
                          

                          <tr>
                            <td><?php echo $i = $i+1 ?></td>
                            <td><?php echo $row['firm'] ?></td>
                            <td><?php echo $row['party_name'] ?></td>
                            <td><?php echo $row['broker'] ?></td>
                            <td><?php echo $row['debit_date'] ?></td>
                            <td><?php echo $row['bill_date'] ?></td>
                            <td><?php echo $row['invoice_no'] ?></td>
                            <td><?php echo $row['total_amount'] ?></td>
                            <td><?php echo $row['weight'] ?></td>
                            <td><?php echo $row['no_of_bales'] ?></td>
                            <td><?php echo $row['lot_no'] ?></td>
                            <td><?php echo $row['start_pr'] ?></td>
                            <td><?php echo $row['end_pr'] ?></td>


                            <!-- RD -->
                            <td><?php echo $row['rd_con'] ?></td>
                            <td><?php echo $row['rd_lab'] ?></td>
                            <td><?php echo $row['rd_diff'] ?></td>
                            <td><?php echo $row['rd_cndy'] ?></td>
                            <td><?php echo $row['rd_amt'] ?></td>

                            <!-- Length -->
                            <td><?php echo $row['len_con'] ?></td>
                            <td><?php echo $row['len_lab'] ?></td>
                            <td><?php echo $row['len_diff'] ?></td>
                            <td><?php echo $row['len_cndy'] ?></td>
                            <td><?php echo $row['len_amt'] ?></td>

                            <!-- MIC -->
                            <td><?php echo $row['mic_con'] ?></td>
                            <td><?php echo $row['mic_lab'] ?></td>
                            <td><?php echo $row['mic_diff'] ?></td>
                            <td><?php echo $row['mic_cndy'] ?></td>
                            <td><?php echo $row['mic_amt'] ?></td>

                            <!-- Trash -->
                            <td><?php echo $row['trs_con'] ?></td>
                            <td><?php echo $row['trs_lab'] ?></td>
                            <td><?php echo $row['trs_diff'] ?></td>                          
                            <td><?php echo $row['trs_amt'] ?></td>

                            <!-- Moisture -->
                            <td><?php echo $row['mois_con'] ?></td>
                            <td><?php echo $row['mois_lab'] ?></td>
                            <td><?php echo $row['mois_diff'] ?></td>                          
                            <td><?php echo $row['mois_amt'] ?></td>


                             <!-- Same -->
                            <td><?php echo $row['sample_kg'] ?></td>
                            <td><?php echo $row['sample_amt'] ?></td>
                           
                             <!-- Tare -->
                            <td><?php echo $row['tare_kg'] ?></td>
                            <td><?php echo $row['tare_amt'] ?></td>


                             <!-- Brokerage -->
                            <td><?php echo $row['brok_per_bales'] ?></td>
                            <td><?php echo $row['brok_amt'] ?></td>



                              <!-- rate diffrence -->
                            <td><?php echo $row['shortage'] ?></td>
                            <td><?php echo $row['shortage_amt'] ?></td>


                             <!-- intrest -->
                            <td><?php echo $row['int_days'] ?></td>
                            <td><?php echo $row['interest'] ?></td>

                              <!-- rate diffrence -->
                            <td><?php echo $row['rate_diff_candy'] ?></td>
                            <td><?php echo $row['rate_diff_amount'] ?></td>



                             <!-- respressing -->
                            <td><?php echo $row['repress_per_bales'] ?></td>
                            <td><?php echo $row['repress_total'] ?></td>

                             <!-- other -->
                            <td><?php echo $row['other_reason'] ?></td>
                            <td><?php echo $row['other_amount'] ?></td>


                              <!-- total debit amount -->
                            <td><?php echo $row['total_debit_amount'] ?></td>
                      
                           
                          </tr>

                          <?php }
                           }

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
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>


<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <script type="text/javascript">

  
    $(document).ready(function() {

      
  



      } );
    </script>
  </body>
</html>

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

function getBrokerName($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from broker where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

   $name=$partyrow['name'];
    return $name;
}

function getTransportName($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from transport where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

   $trans_name=$partyrow['trans_name'];

    return $trans_name;

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

  
    $main_query="select * from pur_report where";

    $where_cond = array();


      $start_date='';
      $end_date='';
 
     
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));
        $where_cond[] = " report_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));
        $where_cond[] = " report_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " report_date>='".$start_date."' AND report_date<='".$end_date."'";

      }

      //filter in sql query
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
        $where = implode(' AND ',$where_cond);
        $main_query = $main_query.$where;
      }
      else
      {
        $main_query="select * from pur_report order by id desc";
      }


      $mainArr  = array();
     

         $purReportResult = mysqli_query($conn, $main_query);

      $i=0;
      if(mysqli_num_rows($purReportResult)>0)
      {
         while($report_row = mysqli_fetch_assoc($purReportResult))
         {
           $mainArr[$i]['firm'] = getFirmDetails($report_row['firm']);
           $mainArr[$i]['party_name'] = getExternalPartyDetails($report_row['party'])[0];
           $mainArr[$i]['report_date'] = convertDate($report_row['report_date']);
           $mainArr[$i]['invoice_no'] = $report_row['invoice_no'];
           $mainArr[$i]['total_amount'] = $report_row['netpayableamt'];
           $mainArr[$i]['weight'] = $report_row['weight'];
           $mainArr[$i]['no_of_bales'] = $report_row['bales'];
           $mainArr[$i]['lot_no'] = $report_row['lot_no'];
           $mainArr[$i]['start_pr'] = $report_row['pr_no_start'];
           $mainArr[$i]['end_pr'] = $report_row['pr_no_end'];
           $mainArr[$i]['broker'] = getBrokerName($report_row['broker']);
         
            $mainArr[$i]['transport'] = getTransportName($report_row['trans_id']);
            $mainArr[$i]['trans_veh_no'] = $report_row['trans_veh_no'];
          


            //get adhoc from debit report
            $adhoc_amt='';
            $sqlDebit="SELECT * FROM `debit_report` where pur_report_id='".$report_row['id']."'";
             $resultDebit = mysqli_query($conn, $sqlDebit);
             if(mysqli_num_rows($resultDebit)>0)
             {
                $rowDebit=mysqli_fetch_assoc($resultDebit);
                $adhoc_amt=$rowDebit['ad_hoc'];
             }
            $mainArr[$i]['ad_hoc']=$adhoc_amt;


           $mainArr[$i]['candy_rate'] = $report_row['cndy_rate'];


           $outstanding_amt=$report_row['netpayableamt']; //by default total amount


           $pay_amt='';
           //if adhoc not null (if debit report created)
           if($adhoc_amt!='')
           {
              $outstanding_amt-=$adhoc_amt;
              $pay_amt=$adhoc_amt;
           }



           //get bales payout record
           $sqlBalesPay="SELECT * FROM `pur_pay` where pur_report_id='".$report_row['id']."'";
           $resultBalesPay = mysqli_query($conn, $sqlBalesPay);

           if(mysqli_num_rows($resultBalesPay)>0)
           {
              $PayRow=mysqli_fetch_assoc($resultBalesPay);


              //pay_amt = adhoc + all dynamic added fields
              $pay_amt=0;
              $pay_amt+=$PayRow['ad_hoc'];
              if($PayRow['dynamic_field']!='')
               {
                   $dynamicData=json_decode($PayRow['dynamic_field']);
                   if(count($dynamicData)>0)
                   {
                      foreach ($dynamicData as $key => $item) 
                      {
                        $pay_amt+=(float)$item->amt; 
                      }

                   }
               }
            
            $pay_amt=number_format($pay_amt, 2, '.', '');

            


              $outstanding_amt=number_format($PayRow['pay_amt'], 2, '.', '');
           }
           


           $mainArr[$i]['out_standing_amt'] = $outstanding_amt;
           $mainArr[$i]['total_paid_amt'] = $pay_amt;

          
          
        
           $i++;
         
         }



      
      }
     
 

    $_SESSION['purchase_register_export_data']=$mainArr;
   
  

  }




?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Purchase Register</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Purchase Register</span></a>
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
                          <select name="broker[]" class="form-control searchDropdown" multiple data-live-search="true" title="Select Option">
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
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>External Party</th>
                                <th>Report Date</th>
                                <th>Invoice No.</th>
                                <th>Total Amount</th>
                                <th>Weight</th>
                                <th>No. of Bales</th>
                                <th>Lot No.</th>
                                <th>Start PR</th>
                                <th>End PR</th>
                                <th>Broker Name</th>
                                <th>Transport</th>
                                <th>Transport Vehicle No.</th>
                                <th>Candy Rate</th>
                                 <th>Ad-Hoc Amount</th>
                                <th>Out standing Amount</th>
                                <th>Total Paid Payment</th>
                                </tr>
                        </thead>
                        <tfoot>
                          
                            
                          
                          <tr>
                              
                               <th>ID</th>
                                <th>Firm Name</th>
                                <th>External Party</th>
                                <th>Report Date</th>
                                <th>Invoice No.</th>
                                <th>Total Amount</th>
                                <th>Weight</th>
                                <th>No. of Bales</th>
                                <th>Lot No.</th>
                                <th>Start PR</th>
                                <th>End PR</th>
                                <th>Broker Name</th>
                                <th>Transport</th>
                                <th>Transport Vehicle No.</th>
                                <th>Candy Rate</th>
                                <th>Ad-Hoc Amount</th>
                                <th>Out standing Amount</th>
                                <th>Total Paid Payment</th>
                          </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($mainArr)>0) {

                            
                            
                            $i=0;
                            foreach ($mainArr as $key => $value) {
                               
                              
                            ?>
                          

                          <tr>
                            <td><?php echo $i = $i+1 ?></td>
                            
                            <td><?php echo $value['firm']; ?></td>
                            <td><?php echo $value['party_name']; ?></td>
                            <td><?php echo $value['report_date']; ?></td>
                            <td><?php echo $value['invoice_no']; ?></td>
                            <td><?php echo $value['total_amount']; ?></td>
                            <td><?php echo $value['weight']; ?></td>
                            <td><?php echo $value['no_of_bales']; ?></td>
                            <td><?php echo $value['lot_no']; ?></td>
                            <td><?php echo $value['start_pr']; ?></td>
                            <td><?php echo $value['end_pr']; ?></td>
                            <td><?php echo $value['broker']; ?></td>
                            <td><?php echo $value['transport']; ?></td>
                            <td><?php echo $value['trans_veh_no']; ?></td>
                            <td><?php echo $value['candy_rate']; ?></td>
                            <td><?php echo $value['ad_hoc']; ?></td>
                            <td><?php echo $value['out_standing_amt']; ?></td>
                            <td><?php echo $value['total_paid_amt']; ?></td>


                           
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

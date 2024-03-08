<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}
  


if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  if(isset($_POST['submit']))
  {


    // get records from sales receivable
      $main_query="select * from sales_rcvble where";

      $start_date='';
      $end_date='';
      $where_cond = array();
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        // $main_query=$main_query." received_date>='".$start_date."'";
        $where_cond[] = " received_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

       
        $where_cond[] = " received_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " received_date>='".$start_date."' AND received_date<='".$end_date."'";

      }

      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);

        // $main_query=$main_query." firm in (".$firm.")";
        $where_cond[] = " firm in (".$firm.")";
        


      }

      if(isset($_POST['ext_party']))
      {
        $ext_party="'".implode("','",$_POST['ext_party'])."'";
        // $main_query=$main_query." party_name in (".$ext_party.")";
        $where_cond[] = " pur_party in (".$ext_party.")";

      }

      //where variable condition without outstanding query 
      if (isset($_POST['out_standing'])) 
      {
          
          if ($_POST['out_standing'] == '1') 
          {

          $where_cond[] = " OSAmount != 0.00";
          }
      }

     
      
      if(!empty($where_cond))
      {
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where.' order by received_date DESC';
      }else
      {

        $main_query="select * from sales_rcvble order by received_date DESC";
      }

       $row_arr  = array();
       $reportIdArr  = array();
       $result2 = mysqli_query($conn, $main_query);

       while($value = mysqli_fetch_assoc($result2)){

         $row_arr[] = $value;
         $reportIdArr[]=$value['sale_report_id'];
       
       }




//--------------------------------------------------------------------------

      // if oustanding is selected then get all sales recevable id from table

      if (isset($_POST['out_standing'])) 
      {
               
              $main_query="select * from sales_rcvble where";

              $start_date='';
              $end_date='';
              $where_cond = array();
              if($_POST['start_date']!='' && $_POST['end_date']=='')
              {
                $start_date = str_replace('/', '-', $_POST['start_date']);
                $start_date = date('Y-m-d', strtotime($start_date));

                // $main_query=$main_query." received_date>='".$start_date."'";
                $where_cond[] = " received_date>='".$start_date."'";
              }

              if($_POST['start_date']=='' && $_POST['end_date']!='')
              {
                $end_date = str_replace('/', '-', $_POST['end_date']);
                $end_date = date('Y-m-d', strtotime($end_date));

               
                $where_cond[] = " received_date<='".$end_date."'";
              }

             
              if($_POST['start_date']!='' && $_POST['end_date']!='')
              {

                $start_date = str_replace('/', '-', $_POST['start_date']);
                $start_date = date('Y-m-d', strtotime($start_date));

                $end_date = str_replace('/', '-', $_POST['end_date']);
                $end_date = date('Y-m-d', strtotime($end_date));

                $where_cond[] = " received_date>='".$start_date."' AND received_date<='".$end_date."'";

              }

              if(isset($_POST['firm']))
              {
                $firm=implode(",",$_POST['firm']);
                $where_cond[] = " firm in (".$firm.")";
                
              }

              if(isset($_POST['ext_party']))
              {
                $ext_party="'".implode("','",$_POST['ext_party'])."'";
                // $main_query=$main_query." party_name in (".$ext_party.")";
                $where_cond[] = " pur_party in (".$ext_party.")";

              }

              
              if(!empty($where_cond))
              {
                $where = implode('AND',$where_cond);
                $main_query = $main_query.$where.' order by received_date DESC';
              }else
              {

                $main_query="select * from sales_rcvble order by received_date DESC";
              }

               $reportIdArr  = array();
               $result2 = mysqli_query($conn, $main_query);

               while($value = mysqli_fetch_assoc($result2)){

                 $reportIdArr[]=$value['sale_report_id'];
               
               }
        
      }




      //get records from sales report (not generated sales receivale)

      $main_query2="select * from sales_report where";

      $start_date='';
      $end_date='';
      $where_cond2 = array();
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $where_cond2[] = " invoice_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

       
        $where_cond2[] = " invoice_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond2[] = " invoice_date>='".$start_date."' AND invoice_date<='".$end_date."'";

      }

      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);

        // $main_query=$main_query." firm in (".$firm.")";
        $where_cond2[] = " firm in (".$firm.")";
        


      }

      if(isset($_POST['ext_party']))
      {
        $ext_party="'".implode("','",$_POST['ext_party'])."'";
        $where_cond2[] = " party_name in (".$ext_party.")";

      }



      if(!empty($reportIdArr))
      {
        $ids = implode(',',$reportIdArr);
        $where_cond2[] = " ID NOT IN (".$ids.")";
      }


      
      if(!empty($where_cond))
      {
         $where2 = implode('AND',$where_cond2);
         $main_query2 = $main_query2.$where2.' order by invoice_date DESC';

      }
      else
      {
         $main_query2="select * from sales_report";

         if(!empty($reportIdArr))
          {
            $ids = implode(',',$reportIdArr);
            $main_query2 = $main_query2." where ID NOT IN (".$ids.")";
          }

          $main_query2.=" order by invoice_date DESC";

      }

     $row_arr2=array();
     $result2 = mysqli_query($conn, $main_query2);

     while($value = mysqli_fetch_assoc($result2))
     {
       $row_arr2[]=$value;
     }

    /* echo '<pre>';
     print_r($row_arr);
     exit;*/



        $_SESSION['column_data']=$_POST;
        $_SESSION['sales_rcv_export_data']=$row_arr;
        $_SESSION['sales_report_export_data']=$row_arr2;
       
       

  }





if(isset($_POST['export']))
  {
   
    $xls_arr=$_SESSION['export_data'];

   

    $filename = "payment_recivied_register_".date('d_m_Y') . ".xls";

    function filterCustomerData(&$str) 
    {
      $str = preg_replace("/\t/", "\\t", $str);
      $str = preg_replace("/\r?\n/", "\\n", $str);
      if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    //To define column name in first row.
    $column_names = false;
    // run loop through each row in $customers_data
    foreach($xls_arr as $row) {
      if(!$column_names) {
        echo implode("\t", array_keys($row)) . "\n";
        $column_names = true;
      }
      // The array_walk() function runs each array element in a user-defined function.
      array_walk($row, 'filterCustomerData');
      echo implode("\t", array_values($row)) . "\n";
    }
    exit;
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span>Payment Recivied Register</span></a>
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
                        <select name="firm[]" class="form-control" multiple> 
                              
                            
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
                              <select name="ext_party[]" class="form-control" multiple>
                                
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
                              <div class="start-dt-col">
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} ?>" autocomplete="off">
                            </div>

                            <div class="end-dt-col">
                              <label for="end_date">End Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} ?>" autocomplete="off">
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-4">
                              <label for="end_date" style="margin-top: 20px;">Show Only Out Standing :</label>
                                <input type="checkbox" id="outStandinding" name="out_standing" value="0" <?php if(isset($_POST['submit'])){if(isset($_POST['out_standing'])){echo 'checked';}}?>>
                            </div>
                        </div>
                          <div class="row">

                            <div class="col-md-12">
                                 <h6>Column For Excel Export :</h6>
                            </div>
                          
                         
                           <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_firm" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_firm'])){echo 'checked';}}else{echo 'checked';}?>>Firm
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_party" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_party'])){echo 'checked';}}else{echo 'checked';}?>>Billing Party
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_del_city" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_del_city'])){echo 'checked';}}else{echo 'checked';}?>>Delivery City
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_credit_days" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_credit_days'])){echo 'checked';}}else{echo 'checked';}?>>Credit Days
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_bill_date" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_bill_date'])){echo 'checked';}}else{echo 'checked';}?>>Bill Date
                                </label>
                              </div>
                           </div>


                           <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_bill_no" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_bill_no'])){echo 'checked';}}else{echo 'checked';}?>>Bill No.
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_final_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_final_amt'])){echo 'checked';}}else{echo 'checked';}?>>Final Amount
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_adhoc_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_adhoc_amt'])){echo 'checked';}}else{echo 'checked';}?>>Ad-Hoc Amount
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_debit_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_debit_amt'])){echo 'checked';}}else{echo 'checked';}?>>Debit Amount
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_gst_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_gst_amt'])){echo 'checked';}}else{echo 'checked';}?>>GST Amount
                                </label>
                              </div>
                           </div>


                           <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_tcs_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_tcs_amt'])){echo 'checked';}}else{echo 'checked';}?>>TCS Amount
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_tds_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_tds_amt'])){echo 'checked';}}else{echo 'checked';}?>>TDS Amount
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_other_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_other_amt'])){echo 'checked';}}else{echo 'checked';}?>>Other Amount
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_credit_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_credit_amt'])){echo 'checked';}}else{echo 'checked';}?>>Credit Note Amount
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_due_date" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_due_date'])){echo 'checked';}}else{echo 'checked';}?>>Due Date
                                </label>
                              </div>
                           </div>


                              <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_due_days" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_due_days'])){echo 'checked';}}else{echo 'checked';}?>>Due Days
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_rcvd_date" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_rcvd_date'])){echo 'checked';}}else{echo 'checked';}?>>Received Date
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_net_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_net_amt'])){echo 'checked';}}else{echo 'checked';}?>>Net Amount
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_rcvd_amt" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_rcvd_amt'])){echo 'checked';}}else{echo 'checked';}?>>Received Amount
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_outstanding" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_outstanding'])){echo 'checked';}}else{echo 'checked';}?>>Out Standing Amount
                                </label>
                              </div>

                          
                           </div>

                       
                          </div>
                           <div class="row mt-3">
                           <div class="form-group col-md-1">
                            <button type="submit" name="submit" class="btn btn-primary waves">Filter</button>
                          </div>
                          <div class="form-group col-md-1">
                            <button type="submit" name="clearFilter" class="btn btn-danger waves">Clear Filter</button>
                          </div>                         
                       </div>

                      </div>
                        </form>
                        
                     </div>
                </div>
            </div>

            


            <div style="margin-top: 20px;">
                <div class="card">
                   
                      <div class="card-body export-data expoert-register">

                        <?php
                          if(isset($_POST['submit']))
                          {
                            ?>
                            <div class="exprot-cta">
                           
                                
                              <a href="export.php" name="export" class="btn btn-info">Export To Excel</a>
                         
                         
                            </div>
                            <br>
                            <?php
                          }
                        ?>
                      <table id="example" class="registertable table table-striped table-bordered" style="width:100%">
                        <?php if (isset($_POST['out_standing']) != '1') 
                        {
                        ?>

                          <thead>
                          
                            <tr>
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>Billing Party</th>
                                <th>Delivery City</th>
                                <th>Credit Days</th>
                                <th>Bill Date</th>
                                <th>Bill No.</th>
                                <th>Bill Amount</th>
                                <th>Due Date</th>
                                <th>Due Days</th>
                                <th>Received Date</th>
                                <th>Ad-Hoc Amount</th>
                                <th>Credit Note Amount</th>
                                <th>Debit Note Amount</th>
                                <th>GST Amount</th>
                                <th>TCS Amount</th>
                                <th>TDS Amount</th>
                                <th>Other Amount</th>
                                <th>Net Amount</th>
                                <th>Received Amount</th>
                                <th>Out-Standing Amount</th>
                                <th>Show</th>
                                                               
                                </tr>
                        </thead>
                        <tfoot>
                          
                            
                          
                          <tr>
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>Billing Party</th>
                                <th>Delivery City</th>
                                <th>Credit Days</th>
                                <th>Bill Date</th>
                                <th>Bill No.</th>
                                <th>Bill Amount</th>
                                <th>Due Date</th>
                                <th>Due Days</th>
                                <th>Received Date</th>
                                <th>Ad-Hoc Amount</th>
                                <th>Credit Note Amount</th>
                                <th>Debit Note Amount</th>
                                <th>GST Amount</th>
                                <th>TCS Amount</th>
                                <th>TDS Amount</th>
                                <th>Other Amount</th>
                                <th>Net Amount</th>
                                <th>Received Amount</th>
                                <th>Out-Standing Amount</th>
                                <th>Show</th>
                          </tr>
                        </tfoot>
                        <tbody>
               <?php 

                if (isset($_POST['submit'])) {
                   $i=0;
                  if (count($row_arr)>0) 
                  {
                   
                    foreach ($row_arr as $key => $value) 
                    {                     
                    
                  ?>
                        
                          <tr>
                            <td><?php echo $i = $i+1 ?></td>
                            
                            <td><?php 

                            $sql4 = "select * from party where id='".$value['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }
                            echo  $pname; ?></td>


                            <td><?php
                              $Ex_party = "select * from external_party where id='".$value['pur_party']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                             echo  $Ex_partyrow['partyname']; ?></td>


                            <td><?php echo  $value['delivery_city']; ?></td>
                            <td><?php echo  $value['credit_days']; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($value['bill_date'])); ?></td>
                            <td><?php echo  $value['bill_no']; ?></td>
                            <td><?php echo  $value['total_value']; ?></td>


                             <td><?php echo date('d/m/Y', strtotime($value['due_date'])); ?></td>

                                  <?php
                                  
                                   if($value['due_date']!='' && $value['due_date']!='0000-00-00')
                                   {
                                         //count due days       
                                            date_default_timezone_set('Asia/Kolkata');
                                            $curDate=date('Y-m-d');
                                        
                                             //due days count
                                              $parakh_date=$value['parakh_date'];
                                              $date1 = date_create($curDate);
                                              $date2 = date_create($parakh_date);
                                              $diff = date_diff($date1,$date2);
                                              $due_days=$diff->format("%a")+1;
                                   } 
                                  ?>

                              <td><?php echo  $due_days ?></td>


                              <?php
                                $rcvd_date='';
                                if($value['received_date']!='' && $value['received_date']!='0000-00-00')
                                 {
                                    $rcvd_date=date('d/m/Y', strtotime($value['received_date']));
                                 }
                                ?>

                                <td><?php echo $rcvd_date  ?></td>




                            <!-- AD HOC DATA -->
                            <td>
                                <table class="table">
                                    <?php 
                                      $adhoc_data=json_decode($value['adhoc_data']);
                                      if(count($adhoc_data)>0)
                                      {
                                          foreach ($adhoc_data as $key=> $item) 
                                          {
                                            $key=$key+1;
                                            $GetDate = '';
                                            if ($item->date != '') 
                                            {
                                            $GetDate = date("d/m/Y", strtotime($item->date)); 
                                            }
                                          ?>

                                            <tr> 
                                              <td><?php echo $key.')'; ?></td>
                                              <td><?php echo $item->adhoc_amount; ?></td>
                                              <td><?php echo $GetDate; ?></td>  
                                            </tr> 
                                          <?php
                                          }
                                      }
                                    ?>
                                </table>
                            </td>


                            <td><?php echo  $value['credit_amt']; ?></td>


                             <!-- DEBIT DATA -->
                            <td>
                                <table class="table">
                                    <?php 
                                      $debit_data=json_decode($value['debit_data']);
                                      if(count($debit_data)>0)
                                      {
                                          foreach ($debit_data as $key=> $item) 
                                          {
                                            $key=$key+1;
                                            $GetDate = '';
                                            if ($item->date != '') 
                                            {
                                            $GetDate = date("d/m/Y", strtotime($item->date)); 
                                            }
                                          ?>

                                            <tr> 
                                              <td><?php echo $key.')'; ?></td>
                                              <td><?php echo $item->debit_amount; ?></td>
                                              <td><?php echo $GetDate; ?></td>  
                                            </tr> 
                                          <?php
                                          }
                                      }
                                    ?>
                                </table>
                            </td>

                            <!-- GST DATA -->
                             <td>
                                <table class="table">
                                    <?php 
                                      $gst_data=json_decode($value['gst_data']);
                                      if(count($gst_data)>0)
                                      {
                                          foreach ($gst_data as $key=> $item) 
                                          {
                                            $key=$key+1;
                                            $GetDate = '';
                                            if ($item->date != '') 
                                            {
                                            $GetDate = date("d/m/Y", strtotime($item->date)); 
                                            }
                                          ?>

                                            <tr> 
                                              <td><?php echo $key.')'; ?></td>
                                              <td><?php echo $item->gst_amount; ?></td>
                                              <td><?php echo $GetDate; ?></td>  
                                            </tr> 
                                          <?php
                                          }
                                      }

                                    ?>
                                </table>
                            </td>

                             <!-- TCS DATA -->
                            <td>
                              <table class="table">
                                    <?php 
                                      $tcs_data=json_decode($value['tcs_data']);
                                      if(count($tcs_data)>0)
                                      {
                                          foreach ($tcs_data as $key=> $item) 
                                          {
                                            $key=$key+1;
                                            $GetDate = '';
                                            if ($item->date != '') 
                                            {
                                            $GetDate = date("d/m/Y", strtotime($item->date)); 
                                            }
                                          ?>

                                            <tr> 
                                              <td><?php echo $key.')'; ?></td>
                                              <td><?php echo $item->tcs_amount; ?></td>
                                              <td><?php echo $GetDate; ?></td>  
                                            </tr> 
                                          <?php
                                          }
                                      }

                                    ?>
                                </table>
                            </td>

                            <td>
                              <table class="table">
                                    <?php 
                                      $tds_data=json_decode($value['tds_data']);
                                      if(count($tds_data)>0)
                                      {
                                          foreach ($tds_data as $key=> $item) 
                                          {
                                            $key=$key+1;
                                            $GetDate = '';
                                            if ($item->date != '') 
                                            {
                                            $GetDate = date("d/m/Y", strtotime($item->date)); 
                                            }
                                          ?>

                                            <tr> 
                                              <td><?php echo $key.')'; ?></td>
                                              <td><?php echo $item->tds_amount; ?></td>
                                              <td><?php echo $GetDate; ?></td>  
                                            </tr> 
                                          <?php
                                          }
                                      }

                                    ?>
                                </table>
                            </td>

                            <td>
                              <table class="table">
                                    <?php 
                                      $other_data=json_decode($value['other_data']);
                                      if(count($other_data)>0)
                                      {
                                          foreach ($other_data as $key=> $item) 
                                          {
                                            $key=$key+1;
                                            $GetDate = '';
                                            if ($item->date != '') 
                                            {
                                            $GetDate = date("d/m/Y", strtotime($item->date)); 
                                            }
                                          ?>

                                            <tr> 
                                              <td><?php echo $key.')'; ?></td>
                                              <td><?php echo $item->other_amount; ?></td>
                                              <td><?php echo $GetDate; ?></td>  
                                            </tr> 
                                          <?php
                                          }
                                      }

                                    ?>
                                </table>
                            </td>

                                <td><?php echo  $value['net_amt']; ?></td>
                                <td><?php echo  $value['total_received']; ?></td>
                                <td><?php echo  $value['OSAmount']; ?></td>
    
                          
                                 <td><a href="show.php?id=<?php echo $value['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a></td>                         
                           
                          </tr>

                      <?php 
                    }
                   }


                   //get record from sales report
                   if(count($row_arr2)>0)
                   {

                    foreach ($row_arr2 as $key => $value) 
                    {

                   ?>
                   <tr>
                      <td><?php echo $i=$i+1 ?></td>
                      <td><?php 

                            $sql4 = "select * from party where id='".$value['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }
                            echo  $pname; ?></td>


                            <td><?php
                              $Ex_party = "select * from external_party where id='".$value['party_name']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                             echo  $Ex_partyrow['partyname']; ?></td>

                      <td><?php echo $value['delivery_city'] ?></td>
                      <td><?php echo $value['credit_days'] ?></td>
                      <td>
                        <?php 
                            $GetDate = '';
                            if ($value['invoice_date'] != '') 
                            {
                            $GetDate = date("d/m/Y", strtotime($value['invoice_date'] )); 
                            }

                          echo $GetDate
                        ?>
                          
                      </td>
                       <td><?php echo $value['invice_no'] ?></td>
                      <td><?php echo $value['total_value'] ?></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td><?php echo $value['total_value'] ?></td>
                      
                       <td><a href="sales-report-show.php?id=<?php echo $value['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a></td>  
                   </tr>
                   <?php   
                        
                    }
                   }



                        }


                          ?>

                          
                          
                          
                        </tbody>
                          
                        <?php 
                        }
                        else
                        {?>
                            
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>Billing Party</th>
                                <th>Delivery City</th>
                                <th>Credit Days</th>
                                <th>Bill Date</th>
                                <th>Bill No.</th>
                                 <th>Bill Amount</th>
                                <th>Due Date</th>
                                <th>Due Days</th>
                                <th>Out-Standing Amount</th>
                                

                                <th>Total Amount</th>
                                <th>Credit Note Amount</th>
                                <th>Ad-Hoc Amount</th>
                                <th>Debit Note Amount</th>
                                <th>GST Amount</th>
                                 <th>TCS Amount</th>
                                 <th>TDS Amount</th>
                                 <th>Other Amount</th>
                                  <th>Net Amount</th>
                                <th>Received Amount</th>


                                <th>Show</th>
                                                               
                               
                                </tr>
                        </thead>
                        <tfoot>
                          
                            
                          
                          <tr>
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>Billing Party</th>
                                <th>Delivery City</th>
                                <th>Credit Days</th>
                                <th>Bill Date</th>
                                <th>Bill No.</th>
                                <th>Bill Amount</th>
                                <th>Due Date</th>
                                <th>Due Days</th>
                                <th>Out-Standing Amount</th>

                                 <th>Total Amount</th>
                                <th>Credit Note Amount</th>
                                <th>Ad-Hoc Amount</th>
                                <th>Debit Note Amount</th>
                                <th>GST Amount</th>
                                 <th>TCS Amount</th>
                                 <th>TDS Amount</th>
                                 <th>Other Amount</th>
                                  <th>Net Amount</th>
                                <th>Received Amount</th>

                                <th>Show</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) 
                          {
                            $i=0;

                            if (count($row_arr)>0) 
                            {
                              
                                foreach ($row_arr as $key => $value) 
                                {                            
                                ?>
                                    <tr>
                                      <td><?php echo $i = $i+1 ?></td>
                                      
                                      <td><?php 

                                      $sql4 = "select * from party where id='".$value['firm']."'";
                                      $result4 = mysqli_query($conn, $sql4);

                                      $row10 = mysqli_fetch_assoc($result4);
                                      // print_r($row10);
                                      $pname='';
                                      if(isset($row10))
                                      {
                                        $pname=$row10['party_name'];
                                      }
                                      echo  $pname; ?></td>


                                      <td><?php
                                        $Ex_party = "select * from external_party where id='".$value['pur_party']."'";
                                          $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                          $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                                       echo  $Ex_partyrow['partyname']; ?></td>



                                      <td><?php echo  $value['delivery_city']; ?></td>
                                      <td><?php echo  $value['credit_days']; ?></td>
                                      <td><?php echo date("d/m/Y", strtotime($value['bill_date']));  ?></td>
                                      <td><?php echo  $value['bill_no']; ?></td>
                                      <td><?php echo  $value['total_value']; ?></td>
                                      
                                      <td><?php echo  date("d/m/Y", strtotime($value['due_date']));?></td>


                                        <?php
                                       $due_days=$value['credit_days'];
                                       if($value['due_date']!='' && $value['due_date']!='0000-00-00')
                                       {
                                                //count due days
                                                  
                                                date_default_timezone_set('Asia/Kolkata');
                                                $curDate=date('Y-m-d');
                                              
                                                 
                                                  $parakh_date=$value['parakh_date'];
                                                  $date1 = date_create($curDate);
                                                  $date2 = date_create($parakh_date);
                                                  $diff = date_diff($date1,$date2);
                                                  $due_days=$diff->format("%a")+1;
                                       } 
                                      ?>


                                      <td><?php echo  $due_days; ?></td>
                                      <td><?php echo  $value['OSAmount']; ?></td>


                                      <td><?php echo  $value['total_value']; ?></td>
                                      <td><?php echo  $value['credit_amt']; ?></td>

                                          <!-- AD HOC DATA -->
                                      <td>
                                          <table class="table">
                                              <?php 
                                                $adhoc_data=json_decode($value['adhoc_data']);
                                                if(count($adhoc_data)>0)
                                                {
                                                    foreach ($adhoc_data as $key=> $item) 
                                                    {
                                                      $key=$key+1;
                                                      $GetDate = '';
                                                      if ($item->date != '') 
                                                      {
                                                      $GetDate = date("d/m/Y", strtotime($item->date)); 
                                                      }
                                                    ?>

                                                      <tr> 
                                                        <td><?php echo $key.')'; ?></td>
                                                        <td><?php echo $item->adhoc_amount; ?></td>
                                                        <td><?php echo $GetDate; ?></td>  
                                                      </tr> 
                                                    <?php
                                                    }
                                                }
                                              ?>
                                          </table>
                                      </td>


                                       <!-- DEBIT DATA -->
                                      <td>
                                          <table class="table">
                                              <?php 
                                                $debit_data=json_decode($value['debit_data']);
                                                if(count($debit_data)>0)
                                                {
                                                    foreach ($debit_data as $key=> $item) 
                                                    {
                                                      $key=$key+1;
                                                      $GetDate = '';
                                                      if ($item->date != '') 
                                                      {
                                                      $GetDate = date("d/m/Y", strtotime($item->date)); 
                                                      }
                                                    ?>

                                                      <tr> 
                                                        <td><?php echo $key.')'; ?></td>
                                                        <td><?php echo $item->debit_amount; ?></td>
                                                        <td><?php echo $GetDate; ?></td>  
                                                      </tr> 
                                                    <?php
                                                    }
                                                }
                                              ?>
                                          </table>
                                      </td>

                                      <!-- GST DATA -->
                                       <td>
                                          <table class="table">
                                              <?php 
                                                $gst_data=json_decode($value['gst_data']);
                                                if(count($gst_data)>0)
                                                {
                                                    foreach ($gst_data as $key=> $item) 
                                                    {
                                                      $key=$key+1;
                                                      $GetDate = '';
                                                      if ($item->date != '') 
                                                      {
                                                      $GetDate = date("d/m/Y", strtotime($item->date)); 
                                                      }
                                                    ?>

                                                      <tr> 
                                                        <td><?php echo $key.')'; ?></td>
                                                        <td><?php echo $item->gst_amount; ?></td>
                                                        <td><?php echo $GetDate; ?></td>  
                                                      </tr> 
                                                    <?php
                                                    }
                                                }

                                              ?>
                                          </table>
                                      </td>

                                       <!-- TCS DATA -->
                                      <td>
                                        <table class="table">
                                              <?php 
                                                $tcs_data=json_decode($value['tcs_data']);
                                                if(count($tcs_data)>0)
                                                {
                                                    foreach ($tcs_data as $key=> $item) 
                                                    {
                                                      $key=$key+1;
                                                      $GetDate = '';
                                                      if ($item->date != '') 
                                                      {
                                                      $GetDate = date("d/m/Y", strtotime($item->date)); 
                                                      }
                                                    ?>

                                                      <tr> 
                                                        <td><?php echo $key.')'; ?></td>
                                                        <td><?php echo $item->tcs_amount; ?></td>
                                                        <td><?php echo $GetDate; ?></td>  
                                                      </tr> 
                                                    <?php
                                                    }
                                                }

                                              ?>
                                          </table>
                                      </td>

                                      <td>
                                        <table class="table">
                                              <?php 
                                                $tds_data=json_decode($value['tds_data']);
                                                if(count($tds_data)>0)
                                                {
                                                    foreach ($tds_data as $key=> $item) 
                                                    {
                                                      $key=$key+1;
                                                      $GetDate = '';
                                                      if ($item->date != '') 
                                                      {
                                                      $GetDate = date("d/m/Y", strtotime($item->date)); 
                                                      }
                                                    ?>

                                                      <tr> 
                                                        <td><?php echo $key.')'; ?></td>
                                                        <td><?php echo $item->tds_amount; ?></td>
                                                        <td><?php echo $GetDate; ?></td>  
                                                      </tr> 
                                                    <?php
                                                    }
                                                }

                                              ?>
                                          </table>
                                      </td>

                                      <td>
                                        <table class="table">
                                              <?php 
                                                $other_data=json_decode($value['other_data']);
                                                if(count($other_data)>0)
                                                {
                                                    foreach ($other_data as $key=> $item) 
                                                    {
                                                      $key=$key+1;
                                                      $GetDate = '';
                                                      if ($item->date != '') 
                                                      {
                                                      $GetDate = date("d/m/Y", strtotime($item->date)); 
                                                      }
                                                    ?>

                                                      <tr> 
                                                        <td><?php echo $key.')'; ?></td>
                                                        <td><?php echo $item->other_amount; ?></td>
                                                        <td><?php echo $GetDate; ?></td>  
                                                      </tr> 
                                                    <?php
                                                    }
                                                }

                                              ?>
                                          </table>
                                      </td>

                                         <td><?php echo  $value['net_amt']; ?></td>
                                      <td><?php echo  $value['total_received']; ?></td>
                                      
                                      

                                          
                                      

                                  


                                     <td><a href="out_standing_show.php?id=<?php echo $value['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a></td>                          
                                    </tr>

                                <?php 
                                }
                             }


                        //get record from sales report
                       if(count($row_arr2)>0)
                       {
                        foreach ($row_arr2 as $key => $value) 
                        {

                       ?>
                       <tr>
                          <td><?php echo $i=$i+1 ?></td>
                          <td><?php 

                                $sql4 = "select * from party where id='".$value['firm']."'";
                                $result4 = mysqli_query($conn, $sql4);

                                $row10 = mysqli_fetch_assoc($result4);
                                // print_r($row10);
                                $pname='';
                                if(isset($row10))
                                {
                                  $pname=$row10['party_name'];
                                }
                                echo  $pname; ?></td>


                                <td><?php
                                  $Ex_party = "select * from external_party where id='".$value['party_name']."'";
                                    $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                    $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                                 echo  $Ex_partyrow['partyname']; ?></td>

                          <td><?php echo $value['delivery_city'] ?></td>
                          <td><?php echo $value['credit_days'] ?></td>
                          <td>
                            <?php 
                                $GetDate = '';
                                if ($value['invoice_date'] != '') 
                                {
                                $GetDate = date("d/m/Y", strtotime($value['invoice_date'] )); 
                                }

                              echo $GetDate
                            ?>
                              
                          </td>
                           <td><?php echo $value['invice_no'] ?></td>
                          <td><?php echo $value['total_value'] ?></td>
                          <td></td>
                          <td></td>
                         <td><?php echo $value['total_value'] ?></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          
                           <td><a href="sales-report-show.php?id=<?php echo $value['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a></td>  
                       </tr>
                       <?php   
                            
                        }
                       }




                          }
                          ?>
                       
                        </tbody>

                        <?php 
                        } ?>
                        
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
    <script type="text/javascript">

       

    $(document).ready(function() {

      $('#outStandinding').val(this.unchecked);

    $('#outStandinding').change(function() {
        if(this.checked) {
            $(this).val('1');
        }else{

              $(this).val('0');

        }   
    });
      



      } );
    </script>
  </body>
</html>

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
  $dir = "/file_storage/"; // file storage in root folder of site
  if (isset($_GET['id'])) {

  $id = $_GET['id'];
 $sql = "SELECT d.*,p.party_name,p.id FROM debit_report d, party p where d.firm=p.id AND d.id='".$id."'";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
    }
    //print_r($row);

}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Debit Report</title>
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
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Debit Report</span></a>
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
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
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
         $sqlLastChange="select username,updated_at from debit_report where id='".$id."'";

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
              <div class="card-header">Debit Report</div>
              <div class="card-body">

              

                  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">

                  <div class="row">
                    <div class="col-md-6">
                       <div class="form-group">
                      <label for="party">External Party</label>
                         <input type="text" class="form-control" name="party" pid="firm" value="<?php $party = "select * from external_party where id='".$row['party']."'";
                          $partyresult = mysqli_query($conn, $party);

                          $partyrow = mysqli_fetch_assoc($partyresult);

                          $ex_party='';
                          if(isset($partyrow))
                          {
                            $ex_party=$partyrow['partyname'];
                          }
                          echo $ex_party; ?>" readonly>
                      </div>
                      
                    </div>

                    <div class="col-md-6">
                          <div class="form-group">
                          <label for="invoice_no">Invoice No</label>
                          <input type="text" class="form-control" name="invoice_no" placeholder="Invoice No." id="invoice_no" value="<?php echo $row['invoice_no']; ?>" readonly="">
                        </div>
                    </div>


                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="firm">Firm</label>
                            <input type="text" class="form-control" name="firm" placeholder="Firm" id="firm" value="<?php echo $row['party_name']; ?>" readonly>
                        </div>                  
                    </div>

                    <?php
                      $debit_date='';
                      if($row['debit_date']!='' && $row['debit_date']!='0000-00-00')
                      {
                      $debit_date=date("d/m/Y", strtotime($row['debit_date']));
                      }
                      ?>

                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="debit_date">Debit Report Date</label>
                            <input type="text" class="form-control " name="debit_date" placeholder=" Debit Report Date" id="debit_date" value="<?php echo $debit_date; ?>" readonly>
                        </div>                  
                    </div>
                    
                  </div>

                  <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="lot_no">LOT No.</label>
                          <input type="text" class="form-control" name="lot_no" placeholder="LOT No." id="lot_no" value="<?php echo $row['lot_no']; ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                            <div class="form-group">
                            <label for="pr_start">PR No. Start</label>
                            <input type="text" class="form-control" name="pr_start" placeholder="PR No. Start" id="pr_start" value="<?php echo $row['pr_start']; ?>" readonly>
                          </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                          <label for="pr_end">PR No. End</label>
                          <input type="text" class="form-control" name="pr_end" placeholder="PR No.End" id="pr_end" value="<?php echo $row['pr_end']; ?>" readonly>
                        </div>
                    </div>
                    
                  </div>

                  <div class="row">

                    <div class="col-md-4">
                          <div class="form-group">
                          <label for="broker">Broker</label>
                          <input type="text" class="form-control" name="broker" placeholder="Broker" id="broker" value="<?php 

                        $broker = "select * from broker where id='".$row['broker']."'";
                          $broker_result = mysqli_query($conn, $broker);

                          $broker_row = mysqli_fetch_assoc($broker_result);

                          $broker_ids='';
                          if(isset($broker_row))
                          {
                            $broker_ids=$broker_row['name'];
                          }
                          echo $broker_ids;?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">                          
                        <div class="form-group">
                          <label for="gross_amt">Gross Amount</label>
                          <input type="text" class="form-control" name="gross_amt" placeholder="Gross Amount" id="gross_amt" value="<?php echo $row['gross_amt']; ?>" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                          <div class="form-group">
                          <label for="cndy_rate">Candy Rate</label>
                          <input type="text" class="form-control" name="cndy_rate" placeholder="Candy Rate" id="cndy_rate" value="<?php echo $row['candy_rate']; ?>"readonly>
                        </div>
                    </div>
                  </div>


                  <div class="row">

                   
                    <div class="col-md-4">
                          
                          <div class="form-group">
                          <label for="ad_hoc">Ad-Hoc </label>
                          <input type="text" class="form-control" name="ad_hoc" placeholder="Ad-Hoc " id="ad_hoc" value="<?php echo $row['ad_hoc']; ?>" readonly>
                        </div>
                   
                    </div>

                    <div class="col-md-4">

                      <?php
                      $ad_hoc_date='';
                      if($row['ad_hoc_date']!='' && $row['ad_hoc_date']!='0000-00-00')
                      {
                      $ad_hoc_date=date("d/m/Y", strtotime($row['ad_hoc_date']));
                      }
                      ?>
                          
                          <div class="form-group">
                          <label for="ad_hoc_date">Ad-Hoc Payment Date</label>
                          <input type="text" class="form-control " name="ad_hoc_date" placeholder="Ad-Hoc Payment Date" id="ad_hoc_date" value="<?php echo $ad_hoc_date; ?>" readonly>
                        </div>
                   
                    </div>
                  

                     <div class="col-md-4">
                      <div class="form-group">
                          <label for="weight">Weight</label>
                          <input type="text" class="form-control" name="weight" placeholder="Enter Weight" id="weight" value="<?php echo $row['weight']; ?>" readonly>
                        </div>
                      
                    </div>

                    <div class="col-md-4">

                      <div class="form-group">
                          <label for="original_rate">Original Rate</label>
                          <input type="text" class="form-control" name="original_rate" placeholder="Enter Original Weight" id="original_rate" value="<?php echo $row['candy_rate']; ?>" readonly>
                        </div>
                      
                    </div>
                    
                  </div>

                  <br>

                  <h4>RD</h4>
                  <div class="row"> 

                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="rd_diff">Difference</label>
                            <input type="text" class="form-control" name="rd_diff" placeholder="Difference" id="rd_diff" value="<?php echo $row['rd_diff']; ?>" readonly>
                          </div> 
                    </div>                  
                  
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="rd_cndy">Candy</label>
                            <input type="text" class="form-control" name="rd_cndy" placeholder="Candy" id="rd_cndy" value="<?php echo $row['rd_cndy']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="rd_amt">Amount</label>
                            <input type="text" class="form-control" name="rd_amt" placeholder="Amount" id="rd_amt" value="<?php echo $row['rd_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div>   


                      <br>
                     <h4>Length</h4>
                  <div class="row"> 
                       


                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="len_diff">Difference</label>
                            <input type="text" class="form-control" name="len_diff" placeholder="Difference" id="len_diff" value="<?php echo $row['len_diff']; ?>" readonly>
                          </div> 
                    </div>                  
                 
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="len_cndy">Candy</label>
                            <input type="text" class="form-control" name="len_cndy" placeholder="Candy" id="len_cndy" value="<?php echo $row['len_cndy']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="len_amt">Amount</label>
                            <input type="text" class="form-control" name="len_amt" placeholder="Amount" id="len_amt" value="<?php echo $row['len_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div>   


                         <br>
                     <h4>Mic</h4>
                  <div class="row"> 
                                 
                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="mic_diff">Difference</label>
                            <input type="text" class="form-control" name="mic_diff" placeholder="Difference" id="mic_diff" value="<?php echo $row['mic_diff']; ?>" readonly>
                          </div> 
                    </div>                  
                 
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="mic_cndy">Candy</label>
                            <input type="text" class="form-control" name="mic_cndy" placeholder="Candy" id="mic_cndy" value="<?php echo $row['mic_cndy']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="mic_amt">Amount</label>
                            <input type="text" class="form-control" name="mic_amt" placeholder="Amount" id="mic_amt" readonly value="<?php echo $row['mic_amt']; ?>">
                            </div> 
                      </div>                    
                    </div>   


                          <br>
                     <h4>Trash</h4>
                  <div class="row"> 
                                 
                        <div class="col-md-6">
                          <div class="form-group">
                          <label for="trs_con">Condition</label>
                          <input type="text" class="form-control" name="trs_con" placeholder="Condition" id="trs_con" value="<?php echo $row['trs_con']; ?>" readonly>
                        </div>                    
                    </div>

                    <div class="col-md-6">                          
                          <div class="form-group">
                          <label for="trs_lab">Lab</label>
                          <input type="text" class="form-control" name="trs_lab" placeholder="Lab" id="trs_lab" value="<?php echo $row['trs_lab']; ?>" readonly>
                        </div> 
                    </div>  
                    
                
                  </div>

                   <div class="row">
                      <div class="col-md-6">

                          <div class="form-group">
                            <label for="trs_diff">Difference</label>
                            <input type="text" class="form-control" name="trs_diff" placeholder="Difference" id="trs_diff" value="<?php echo $row['trs_diff']; ?>" readonly>
                          </div> 
                           
                      </div>
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="trs_amt">Amount</label>
                            <input type="text" class="form-control" name="trs_amt" placeholder="Amount" id="trs_amt" value="<?php echo $row['trs_amt']; ?>"readonly>
                            </div> 
                      </div>                    
                    </div> 


                        <br>
                     <h4>Moisture</h4>
                  <div class="row"> 
                                 
                        <div class="col-md-6">
                          <div class="form-group">
                          <label for="mois_con">Condition</label>
                          <input type="text" class="form-control" name="mois_con" placeholder="Condition" id="mois_con" value="<?php echo $row['mois_con']; ?>" readonly>
                        </div>                    
                    </div>

                    <div class="col-md-6">                          
                          <div class="form-group">
                          <label for="mois_lab">Lab</label>
                          <input type="text" class="form-control" name="mois_lab" placeholder="Lab" id="mois_lab" value="<?php echo $row['mois_lab']; ?>" readonly>
                        </div> 
                    </div>  
                    
               
                  </div>

                   <div class="row">
                      <div class="col-md-6">

                        <div class="form-group">
                            <label for="mois_diff">Difference</label>
                            <input type="text" class="form-control" name="mois_diff" placeholder="Difference" id="mois_diff" value="<?php echo $row['mois_diff']; ?>" readonly>
                          </div> 
                            
                      </div>
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="mois_amt">Amount</label>
                            <input type="text" class="form-control" name="mois_amt" placeholder="Amount" id="mois_amt" value="<?php echo $row['mois_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div> 


                      <br>
                     <h4>Sample</h4>                 
                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="smp_kg">KG</label>
                            <input type="text" class="form-control" name="smp_kg" placeholder="KG" id="smp_kg" value="<?php echo $row['sample_kg']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="smp_amt">Amount</label>
                            <input type="text" class="form-control" name="smp_amt" placeholder="Amount" id="smp_amt" value="<?php echo $row['sample_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div> 


                    <br>
                     <h4>Extra Tare</h4>                 
                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="tare_kg">KG</label>
                            <input type="text" class="form-control" name="tare_kg" placeholder="KG" id="tare_kg" value="<?php echo $row['tare_kg']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="tare_amt">Amount</label>
                            <input type="text" class="form-control" name="tare_amt" placeholder="Amount" id="tare_amt" value="<?php echo $row['tare_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div> 


                    
                    <br>
                    <h5>Brokerage</h5> 
                      <label class="radio-inline">
                        <input type="radio" name="brokerage_option" value="dynamic" <?php if($row['brok_option']=='dynamic'){echo 'checked';} ?> disabled> Dynamic
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="brokerage_option" value="manual" <?php if($row['brok_option']=='manual'){echo 'checked';} ?> disabled> Manual
                      </label> 

                   <div class="row dynamicBrokerage">
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="brok_bales">Bales</label>
                            <input type="text" class="form-control" name="brok_bales" id="brok_bales" readonly value="<?php echo $row['brok_bales']; ?>" >
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="brok_per_bales">Per Bales</label>
                            <input type="text" class="form-control" name="brok_per_bales" placeholder="Per Bales" id="brok_per_bales" value="<?php echo $row['brok_per_bales']; ?>" readonly>
                            </div> 
                      </div>   
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="brok_dynamic_amt">Brokerage Amount</label>
                            <input type="text" class="form-control" name="brok_dynamic_amt" placeholder="Amount" id="brok_dynamic_amt" value="<?php echo $row['brok_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div> 

                    <div class="row manualBrokerage">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="brok_reason">Reason</label>
                            <input type="text" class="form-control" name="brok_reason" placeholder="Reason" id="brok_reason" value="<?php echo $row['brok_reason']; ?>" readonly>
                            </div> 
                      </div>
                        
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="brok_manual_amt">Amount</label>
                            <input type="text" class="form-control" name="brok_manual_amt" placeholder="Enter Amount" id="brok_manual_amt" value="<?php echo $row['brok_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div>

                    <input type="hidden" name="brok_amt" id="brok_amt" value="<?php echo $row['brok_amt']; ?>">

                       <br>
                     <h4>Weight Shoratage</h4>
                  <div class="row"> 
                                 
                        

                    <div class="col-md-4">                          
                          <div class="form-group">
                          <label for="wght_our_slip">Our Slip</label>
                          <input type="text" class="form-control" name="wght_our_slip" placeholder="Our Slip" id="wght_our_slip" value="<?php echo $row['our_slip']; ?>" readonly>
                        </div> 
                    </div>  
                    

                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="wght_diff">Difference</label>
                            <input type="text" class="form-control" name="wght_diff" placeholder="Difference" id="wght_diff" value="<?php echo $row['slip_diff']; ?>"  readonly>
                          </div> 
                    </div>                  
                  
                       
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="wght_shortage_amt">Shortage Amount</label>
                            <input type="text" class="form-control" name="wght_shortage_amt" placeholder="Amount" id="wght_shortage_amt" value="<?php echo $row['shortage_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div> 


                  <?php 
                  if($row['repress_total']!=0)
                  {
                    ?>


                     <br>
                     <h4>Re-pressing</h4>                 
                   <div class="row">
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="repress_no_of_bales">No. Of Bales</label>
                            <input type="text" class="form-control" name="repress_no_of_bales" placeholder="No. Of Bales" id="repress_no_of_bales" value="<?php echo $row['repress_bales']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="repress_per_bales">Per Bales</label>
                            <input type="text" class="form-control" name="repress_per_bales" placeholder="Per Bales" id="repress_per_bales" value="<?php echo $row['repress_per_bales']; ?>" readonly>
                            </div> 
                      </div>   
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="repress_total">Total</label>
                            <input type="text" class="form-control" name="repress_total" placeholder="Total" id="repress_total" value="<?php echo $row['repress_total']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div>

                  <?php
                  }
                  ?> 


                  <?php 
                  if($row['other_check']!=false)
                  {
                    ?>


                      <br>
                    
                     <div class="checkbox">
                      <label style="font-size: 20px; font-weight: 600" class="checkbox-inline">Other <input type="checkbox" name="other_check" id="other_check" 
                        <?php if($row['other_check']=='true'){ echo 'checked';} ?>
                        disabled></label>
                    </div> 

                   <div class="row otherSection">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="other_reason">Reason</label>
                            <input type="text" class="form-control" name="other_reason" placeholder=" Reason" id="other_reason" value="<?php echo $row['other_reason']; ?>" readonly>
                            </div> 
                      </div>
                        
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="other_amount">Amount</label>
                            <input type="text" class="form-control" name="other_amount" placeholder="Amount" id="other_amount" value="<?php echo $row['other_amount']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div>

                    <?php 
                  }
                    ?> 



                    <?php 
                  if($row['rate_diff_amount']!=0)
                  {
                    ?>
                     <br>
                     <h4>Rate Difference</h4>                 
                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="rate_diff_candy">Candy</label>
                            <input type="text" class="form-control" name="rate_diff_candy" placeholder="Candy" id="rate_diff_candy" value="<?php echo $row['rate_diff_candy']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="rate_diff_amt">Rate Difference Amount</label>
                            <input type="text" class="form-control" name="rate_diff_amt" placeholder="Rate Difference" id="rate_diff_amt" value="<?php echo $row['rate_diff_amount']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div> 

                    <?php 
                      }
                    ?> 






                     <br>
                     <h4>Final Calculations</h4>  
                    

                     <br> 

                      <h5>Interest</h5> 

                      <label class="radio-inline">
                        <input type="radio" name="interst_option" value="dynamic" <?php if($row['int_option']=='dynamic'){echo 'checked';} ?> disabled> Dynamic
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="interst_option" value="manual" <?php if($row['int_option']=='manual'){echo 'checked';} ?> disabled> Manual
                      </label> 

                     <div class="row dynamicIntClass">
                          <div class="col-md-4">
                                <div class="form-group">
                                <label for="dynamic_int">Interest</label>
                                <input type="text" class="form-control" name="dynamic_int" placeholder="Interest" value="<?php echo $row['interest']; ?>" id="dynamic_int" readonly>
                                </div> 
                          </div>
                      </div>

                        <div class="row manualIntClass">

                            <div class="col-md-3">
                              <div class="form-group">
                              <label for="int_amount">Amount</label>
                              <input type="text" class="form-control" name="int_amount" id="int_amount" value="<?php echo $row['int_amount']; ?>" readonly>
                              </div> 
                            </div>


                            <div class="col-md-3">
                            <div class="form-group">
                            <label for="int_days">Days</label>
                            <input type="text" class="form-control" name="int_days" placeholder="Days" id="int_days" value="<?php echo $row['int_days']; ?>" readonly>
                            </div> 
                            </div>
                        
                            <div class="col-md-3">
                              <div class="form-group">
                            <label for="int_rate">Rate</label>
                            <input type="text" class="form-control" name="int_rate" placeholder="Rate" id="int_rate" value="<?php echo $row['int_rate']; ?>" readonly>
                            </div> 
                            </div>

                            <div class="col-md-3">
                              <div class="form-group">
                            <label for="manual_int">Total Intrest</label>
                            <input type="text" class="form-control" name="manual_int" id="manual_int" placeholder="Total Intrest" value="<?php echo $row['interest']; ?>"  readonly>
                            </div> 
                            </div> 

                            <input type="hidden" name="final_int" id="final_int" value="<?php echo $row['interest']; ?>">

                        </div>

                                         
                   
                   <div class="row">
                      <div class="col-md-12">
                              <div class="form-group">
                            <label for="final_deb_amt">Debit Amount</label>
                            <input type="text" class="form-control" name="final_deb_amt" placeholder="Debit Amount" id="final_deb_amt" value="<?php echo $row['debit_amount']; ?>" readonly>
                            </div> 
                      </div>
                    </div>

                        <div class="row">
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="tax">Tax(%)</label>
                            <input type="text" class="form-control" name="tax" placeholder="Tax in %" id="tax" value="<?php echo $row['tax']; ?>" readonly>
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="tax_amount">Tax Amount</label>
                            <input type="text" class="form-control" name="tax_amount" placeholder="Tax Amount" id="tax_amount" value="<?php echo $row['tax_amount']; ?>" readonly>
                            </div> 
                      </div>   
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="final_debit_with_tax">Final Debit Amount With Tax</label>
                            <input type="text" class="form-control" name="final_debit_with_tax" placeholder="Final Debit Amount" id="final_debit_with_tax" value="<?php echo $row['final_debit_amount']; ?>" readonly>
                            </div> 
                      </div>
                        <div class="col-md-4">
                              <div class="form-group">
                            <label for="tds_amount">TDS Amount</label>
                            <input type="text" class="form-control" name="tds_amount" id="tds_amount" value="<?php echo $row['tds_amount'] ?>" readonly>
                            </div> 
                      </div>                       
                    </div> 


                    <div class="row">
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="final_bal_pay">Balance Payable</label>
                            <input type="text" class="form-control bold" name="final_bal_pay" placeholder="Balance Payable" id="final_bal_pay" value="<?php echo $row['balance_pay']; ?>" readonly>
                            </div> 
                      </div> 
                         <div style="margin-top: 30px;" class="form-check col-md-2">
                        <label class="form-check-label">
                          <input type="checkbox" class="form-check-input" name="is_paid" value="1" <?php if($row['is_paid']=='1') { echo 'checked'; } ?> onclick="return false">Paid
                        </label>
                      </div> 
                    </div>


                      <br>

                                      <div class="row">
                    
                                        <?php
                                        if($row['docimg'] != ''){
                                         $prev = explode(',',$row['docimg']);
                                        $prev_img_title = explode(',',$row['img_title']);
                                        foreach ($prev as $key => $imging) {
                                          $attend =  $dir.$imging;

                                          if($attend)
                                              {
                                              $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));

                                              $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');


                                              if(in_array($attendExt, $attend_allowExt)) 
                                              {



                                                ?>
                                                          <div class="form-group col-md-4 field-show-image pl-0">
                                                            <div class="image-upload">  
                                                              <div class="label">
                                                              <h6 class="title">Document File <?= $key+1 ?></h6>
                                                            
                                                            </div>
                                                            <div class="filed-form-control">  
                                                            <img src="<?php echo $dir.$imging ?>"  data-toggle="modal" data-target="#myModal" id="1" onerror="this.onerror=null; this.src='../../image/no-image.jpg'" height="300" width="300">
                                                              </div>
                                                            <div class="text-center mt-3"> (Click Image to Open)</div>

                                                 
                                                            </div>
                                                            <br>
                              <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                                                          </div>

                                                <?php
                                               
                                              }
                                              else
                                              {
                                                ?>
                                                 <div class="form-group col-md-4 field-show-image pl-0">
                                                            <div class="image-upload">  
                                                              <div class="label">
                                                              <h6 class="title"> Document File <?= $key+1 ?></h6>
                                                            
                                                            </div>
                                                            <div class="filed-form-control">  
                                                   <img src="<?php echo $dir.$imging ?>"   id="1" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" height="300" width="300">
                                                <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                                                 
                                                            </div>
                                                            <br>
                                                            <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                                                          </div>
                                                        </div>
                                                <?php
                                              }

                                            }
                                          }
                                          }

                                 ?>                        
                      </div>

                  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img class="img-responsive" src="" width="600" height="600" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
              </div>
            </div>
          </div>
        
      </div>
</div>
</div>
   
  
  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });

            $(document).ready(function () {
          $('#myModal').on('show.bs.modal', function (e) {
              var image = $(e.relatedTarget).attr('src');
              $(".img-responsive").attr("src", image);
          });
        });


      //--------------------------



      //Other Section
      if($("#other_check").is(':checked'))
      {
          $(".otherSection").show();
          $("#other_check").attr('value', 'true');
      }
      else
      {
          $(".otherSection").hide();
          $("#other_reason").val('');
          $("#other_amount").val('');
          $(this).attr('value', 'false');
      }

      


     //Interset Config setting
      var getRadioVal=$('input[type=radio][name=interst_option]:checked').val();
      if(getRadioVal=='dynamic')
      {
          $('.dynamicIntClass').show();
          $('.manualIntClass').hide();

          $('#int_days').val('');
          $('#int_rate').val('');
          $('#manual_int').val('');

      }
      else
      {
          $('.manualIntClass').show();
          $('.dynamicIntClass').hide();
      }

       //Borkrage Config setting
      var getRadioVal=$('input[type=radio][name=brokerage_option]:checked').val();
      if(getRadioVal=='dynamic')
      {
          $('.dynamicBrokerage').show();
          $('.manualBrokerage').hide();

          $('#brok_reason').val('');
          $('#brok_manual_amt').val('');
          

      }
      else
      {
          $('.manualBrokerage').show();
          $('.dynamicBrokerage').hide();

          $('#brok_bales').val('');
          $('#brok_per_bales').val('');
          $('#brok_dynamic_amt').val('');

      }

   

        });

      
  function readURL(input) {
    var url = input.value;
    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) {
        var reader = new FileReader();

         reader.onload = function (e) {
                imgId = '#preview-'+$(input).attr('id');
                $(imgId).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
    }else{
          imgId = '#preview-'+$(input).attr('id');
          $(imgId).attr('src', '../../image/no-prev.jpg');
          //$(imgId).find(".msg").html("This is not Image");
         //$('.imagepreview').attr('src', '/assets/no_preview.png');
    }
} 
  function NumericValidate(key) {
    var keycode = (key.which) ? key.which : key.keyCode;

    if (keycode >= 48 && keycode <= 57)  
    {     
           return true;    
    }
    else
    {
        return false;
    }
         
}          

</script>
  </body>
</html>

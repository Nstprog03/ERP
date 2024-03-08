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
  $dir = "/file_storage/";

   $getFirm=$_SESSION["pur_firm"];
  $getFirmID=$_SESSION["pur_firm_id"];

  $getYear=$_SESSION['pur_financial_year'];

  $year_array=explode("/",$getYear);

  $shortYear='';
  $getFullYear='';

  foreach ($year_array as $key => $value) {
    $shortYear=$shortYear.date("y", strtotime($value));

      if($key==0)
      {
         $getFullYear=$getFullYear.date("Y", strtotime($value));
      }
      else 
      {
         $getFullYear=$getFullYear.'-'.date("Y", strtotime($value));
      }    
  }

  if (isset($_GET['id'])) {
    
  $id = $_GET['id'];
  $sql = "SELECT d.*,p.party_name,p.id FROM debit_report d, party p where d.firm=p.id AND d.id='".$id."'";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
    }
    // print_r($row);
    // exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Edit Debit Report</title>
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

      $(".datepicker").datepicker({
                  dateFormat: "dd/mm/yy",
                  changeMonth: true,
                  changeYear: true,
                  maxDate: new Date('<?php echo($year_array[1]) ?>'),
                  minDate: new Date('<?php echo($year_array[0]) ?>')
                     
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Debit Report Database</span></a>
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
              <div class="card-header">Edit Debit Report</div>
              <div class="card-body">

                <form class="" action="update.php" method="post" enctype="multipart/form-data">

                  <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                  <input type="hidden" name="page" value="<?php echo $page ?>">



                  <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="party">Select External Party</label>   
                          <?php
                           $sql = "SELECT DISTINCT(party) FROM pur_report where firm='".$_SESSION['pur_firm_id']."' AND financial_year='".$_SESSION['pur_financial_year_id']."'";

                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select id="epartySelect" name="party" data-live-search="true" class="form-control searchDropdown" required>
                            <option value="" disabled selected>Select Party</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {

                                  

                                  $party_sql="SELECT * FROM external_party WHERE id='".$result['party']."'";
                                        $party_result = mysqli_query($conn, $party_sql);
                                          $party_row = $party_result->fetch_assoc();
                                  $isPartySelected='';
                                  if($party_row['id']==$row['party'])
                                  {
                                    $isPartySelected='selected';
                                  }
                                
                                  echo "<option  value='".$party_row['id']."'".$isPartySelected.">".$party_row['partyname']."</option>";
                                       
                              }
                            ?>                              
                            </select>
                      </div>


                      
                    </div>

                     <div class="col-md-6">
                          <div class="form-group">
                          <label for="invoice_no">Invoice No</label>
                       
                          <select id="invoice_no" name="invoice_no" class="form-control" required>
                            <option value="" disabled selected>Select Invoice No.</option>
                          </select>

                          <input type="hidden" id="pur_conf_no" name="pur_conf_no" value="<?php echo $row['conf_no']; ?>">

                          <input type="hidden" id="pur_report_id" name="pur_report_id" value="<?php echo $row['pur_report_id']; ?>">

                        </div>
                    </div>

                    <div class="col-md-6">
                         <div class="form-group">
                            <label for="firm">Firm</label>
                            <input type="text" class="form-control" name="firm" placeholder="Firm" id="firm" value="<?php echo $row['party_name']; ?>" readonly>

                            <input type="hidden" name="firm_id" value="<?php echo $row['firm']; ?>">

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
                            <input type="text" class="form-control datepicker" name="debit_date" placeholder="Enter Debit Report Date" id="debit_date" autocomplete="off" value="<?php echo $debit_date; ?>">
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
                          <input type="text" class="form-control" placeholder="Broker" id="broker" value="<?php 

                        $broker = "select * from broker where id='".$row['broker']."'";
                          $broker_result = mysqli_query($conn, $broker);

                          $broker_row = mysqli_fetch_assoc($broker_result);

                          $broker_ids='';
                          if(isset($broker_row))
                          {
                            $broker_ids=$broker_row['name'];
                          }
                          echo $broker_ids;?>" readonly>

                          <input type="hidden" name="broker" id="broker_id" value="<?php echo $row['broker'] ;?>">
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
                          <input type="text" class="form-control" name="ad_hoc" placeholder="Enter Ad-Hoc " id="ad_hoc" value="<?php echo $row['ad_hoc']; ?>" onkeypress="return NumericValidate(event,this)">
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
                          <input type="text" class="form-control datepicker" name="ad_hoc_date" autocomplete="off" placeholder="Enter Ad-Hoc Payment Date" id="ad_hoc_date" value="<?php echo $ad_hoc_date; ?>">
                        </div>
                   
                    </div>
                 

                     <div class="col-md-4">
                      <div class="form-group">
                          <label for="weight">Weight</label>
                          <input type="text" class="form-control" name="weight" placeholder="Weight" id="weight" value="<?php echo $row['weight']; ?>" readonly onkeypress="return NumericValidate(event,this)">
                        </div>
                      
                    </div>

                    <div class="col-md-4">

                      <div class="form-group">
                          <label for="original_rate">Original Rate</label>
                          <input type="text" class="form-control" name="original_rate" placeholder="Enter Original Weight" id="original_rate" value="<?php echo $row['candy_rate']; ?>"readonly>
                        </div>
                      
                    </div>
                    
                  </div>

                  <br>

                  <h4>RD</h4>
                  <div class="row"> 
                                 
                        <div class="col-md-4">
                          <div class="form-group">
                          <label for="rd_con">Condition</label>
                          <input type="text" class="form-control" name="rd_con" placeholder="Condition" id="rd_con" value="<?php echo $row['rd_con']; ?>" onkeypress="return NumericValidate(event,this)" readonly>
                        </div>                    
                        </div>

                        <div class="col-md-4">                          
                              <div class="form-group">
                              <label for="rd_lab">Lab</label>
                              <input type="text" class="form-control" name="rd_lab" placeholder="Enter Lab" id="rd_lab" value="<?php echo $row['rd_lab']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                        </div>  
                    
                        <div class="col-md-4">
                              <div class="form-group">
                                <label for="rd_diff">Difference</label>
                                <input type="text" class="form-control" name="rd_diff" placeholder="Difference" id="rd_diff" value="<?php echo $row['rd_diff']; ?>" readonly>
                              </div> 
                        </div>                  
                  </div>

                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="rd_cndy">Candy</label>
                            <input type="text" class="form-control" name="rd_cndy" placeholder="Enter Candy" id="rd_cndy" value="<?php echo $row['rd_cndy']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>
                      <div class="col-md-6">
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
                          <label for="len_con">Condition</label>
                          <input type="text" class="form-control" name="len_con" placeholder="Condition" id="len_con" value="<?php echo $row['len_con']; ?>" onkeypress="return NumericValidate(event,this)" readonly>
                          </div>                    
                        </div>

                        <div class="col-md-4">                          
                              <div class="form-group">
                              <label for="len_lab">Lab</label>
                              <input type="text" class="form-control" name="len_lab" placeholder="Enter Lab" id="len_lab" value="<?php echo $row['len_lab']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                        </div>  
                      

                        <div class="col-md-4">
                              <div class="form-group">
                                <label for="len_diff">Difference</label>
                                <input type="text" class="form-control" name="len_diff" placeholder="Difference" id="len_diff" value="<?php echo $row['len_diff']; ?>" readonly>
                              </div> 
                        </div>                  
                  </div>

                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="len_cndy">Candy</label>
                            <input type="text" class="form-control" name="len_cndy" placeholder="Enter Candy" id="len_cndy" value="<?php echo $row['len_cndy']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>
                      <div class="col-md-6">
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
                          <label for="mic_con">Condition</label>
                          <input type="text" class="form-control" name="mic_con" placeholder="Condition" id="mic_con" value="<?php echo $row['mic_con']; ?>" onkeypress="return NumericValidate(event,this)" readonly>
                        </div>                    
                    </div>

                    <div class="col-md-4">                          
                          <div class="form-group">
                          <label for="mic_lab">Lab</label>
                          <input type="text" class="form-control" name="mic_lab" placeholder="Enter Lab" id="mic_lab" value="<?php echo $row['mic_lab']; ?>" onkeypress="return NumericValidate(event,this)">
                        </div> 
                    </div>  
                    

                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="mic_diff">Difference</label>
                            <input type="text" class="form-control" name="mic_diff" placeholder="Difference" id="mic_diff" value="<?php echo $row['mic_diff']; ?>" readonly>
                          </div> 
                    </div>                  
                  </div>

                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="mic_cndy">Candy</label>
                            <input type="text" class="form-control" name="mic_cndy" placeholder="Enter Candy" id="mic_cndy" value="<?php echo $row['mic_cndy']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>
                      <div class="col-md-6">
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
                            <input type="text" class="form-control" name="trs_con" placeholder="Condition" id="trs_con" value="<?php echo $row['trs_con']; ?>" onkeypress="return NumericValidate(event,this)" readonly>
                          </div>                    
                      </div>

                      <div class="col-md-6">                          
                            <div class="form-group">
                            <label for="trs_lab">Lab</label>
                            <input type="text" class="form-control" name="trs_lab" placeholder="Enter Lab" id="trs_lab" value="<?php echo $row['trs_lab']; ?>" onkeypress="return NumericValidate(event,this)">
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
                            <input type="text" class="form-control" name="mois_con" placeholder="Condition" id="mois_con" value="<?php echo $row['mois_con']; ?>" onkeypress="return NumericValidate(event,this)" readonly>
                          </div>                    
                      </div>

                      <div class="col-md-6">                          
                            <div class="form-group">
                            <label for="mois_lab">Lab</label>
                            <input type="text" class="form-control" name="mois_lab" placeholder="Enter Lab" id="mois_lab" value="<?php echo $row['mois_lab']; ?>" onkeypress="return NumericValidate(event,this)">
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
                            <input type="text" class="form-control" name="smp_kg" placeholder="Enter KG" id="smp_kg" value="<?php echo $row['sample_kg']; ?>" onkeypress="return NumericValidate(event,this)">
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
                            <input type="text" class="form-control" name="tare_kg" placeholder="Enter KG" id="tare_kg" value="<?php echo $row['tare_kg']; ?>" onkeypress="return NumericValidate(event,this)">
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
                        <input type="radio" name="brokerage_option" value="dynamic" <?php if($row['brok_option']=='dynamic'){echo 'checked';} ?>> Dynamic
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="brokerage_option" value="manual" <?php if($row['brok_option']=='manual'){echo 'checked';} ?>> Manual
                      </label> 

                   <div class="row dynamicBrokerage">
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="brok_bales">Bales</label>
                            <input type="text" class="form-control" name="brok_bales" placeholder="Bales" id="brok_bales" readonly value="<?php echo $row['brok_bales']; ?>" >
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="brok_per_bales">Per Bales</label>
                            <input type="text" class="form-control" name="brok_per_bales" placeholder="Ente Per Bales" id="brok_per_bales" value="<?php echo $row['brok_per_bales']; ?>" onkeypress="return NumericValidate(event,this)">
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
                            <input type="text" class="form-control" name="brok_reason" placeholder="Enter Reason" id="brok_reason" value="<?php echo $row['brok_reason']; ?>">
                            </div> 
                      </div>
                        
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="brok_manual_amt">Amount</label>
                            <input type="text" class="form-control" name="brok_manual_amt" placeholder="Enter Amount" id="brok_manual_amt" value="<?php echo $row['brok_amt']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>                    
                    </div>

                    <input type="hidden" name="brok_amt" id="brok_amt" value="<?php echo $row['brok_amt']; ?>">


                       <br>
                     <h4>Weight Shoratage</h4>
                  <div class="row"> 
                                 
                    <div class="col-md-4">
                          <div class="form-group">
                          <label for="wght_seller_slip">Seller Slip</label>
                          <input type="text" class="form-control" name="wght_seller_slip" placeholder="Seller Slip" id="wght_seller_slip" value="<?php echo $row['seller_slip']; ?>" readonly>
                        </div>                    
                    </div>

                    <div class="col-md-4">                          
                          <div class="form-group">
                          <label for="wght_our_slip">Our Slip</label>
                          <input type="text" class="form-control" name="wght_our_slip" placeholder="Enter Our Slip" id="wght_our_slip" value="<?php echo $row['our_slip']; ?>" onkeypress="return NumericValidate(event,this)">
                        </div> 
                    </div>  
                    

                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="wght_diff">Difference</label>
                            <input type="text" class="form-control" name="wght_diff" placeholder="Difference" id="wght_diff" value="<?php echo $row['slip_diff']; ?>" readonly>
                          </div> 
                    </div>                  
                  </div>

                   <div class="row">
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="wght_allow">Allowable</label>
                            <input type="text" class="form-control" name="wght_allow" placeholder="Enter Allowable" id="wght_allow" value="<?php echo $row['allowable']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>
                      <div class="col-md-4">                          
                          <div class="form-group">
                          <label for="wght_shortage">Shortage</label>
                          <input type="text" class="form-control" name="wght_shortage" placeholder="Enter Shortage" id="wght_shortage" readonly value="<?php echo $row['shortage']; ?>">
                        </div> 
                      </div>  
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="wght_shortage_amt">Shortage Amount</label>
                            <input type="text" class="form-control" name="wght_shortage_amt" placeholder="Amount" id="wght_shortage_amt" value="<?php echo $row['shortage_amt']; ?>" readonly>
                            </div> 
                      </div>                    
                  </div> 




                     <br>
                     <h4>Re-pressing</h4>                 
                   <div class="row">
                      <div class="col-md-4">
                            <div class="form-group">
                            <label for="repress_no_of_bales">No. Of Bales</label>
                            <input type="text" class="form-control" name="repress_no_of_bales" placeholder="Enter No. Of Bales" id="repress_no_of_bales" onkeypress="return NumericValidate(event,this)" value="<?php echo $row['repress_bales']; ?>">
                            </div> 
                      </div>
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="repress_per_bales">Per Bales</label>
                            <input type="text" class="form-control" name="repress_per_bales" placeholder="Ente Per Bales" id="repress_per_bales" onkeypress="return NumericValidate(event,this)" value="<?php echo $row['repress_per_bales']; ?>">
                            </div> 
                      </div>   
                      <div class="col-md-4">
                              <div class="form-group">
                            <label for="repress_total">Total</label>
                            <input type="text" class="form-control" name="repress_total" placeholder="Total" id="repress_total" value="<?php echo $row['repress_total']; ?>" readonly>
                            </div> 
                      </div>                    
                  </div> 


                      <br>
                    
                     <div class="checkbox">
                      <label style="font-size: 20px; font-weight: 600" class="checkbox-inline">Other <input type="checkbox" name="other_check" id="other_check" 
                        <?php if($row['other_check']=='true'){ echo 'checked';} ?>
                        ></label>
                    </div> 

                   <div class="row otherSection">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="other_reason">Reason</label>
                            <input type="text" class="form-control" name="other_reason" placeholder="Enter Reason" id="other_reason" value="<?php echo $row['other_reason']; ?>">
                            </div> 
                      </div>
                        
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="other_amount">Amount</label>
                            <input type="text" class="form-control" name="other_amount" placeholder="Enter Amount" id="other_amount" value="<?php echo $row['other_amount']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>                    
                    </div> 

                     <br>
                     <h4>Rate Difference</h4>                 
                   <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                            <label for="rate_diff_candy">Candy</label>
                            <input type="text" class="form-control" name="rate_diff_candy" placeholder="Enter Candy" id="rate_diff_candy" value="<?php echo $row['rate_diff_candy']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                      </div>
                      <div class="col-md-6">
                              <div class="form-group">
                            <label for="rate_diff_amt">Rate Difference Amount</label>
                            <input type="text" class="form-control" name="rate_diff_amt" placeholder="Rate Difference" id="rate_diff_amt" value="<?php echo $row['rate_diff_amount']; ?>" readonly>
                            </div> 
                      </div>                    
                    </div>  






                     <br>
                     <h4>Final Calculations</h4>  
                     

                     <br> 

                      <h5>Interest</h5> 

                      <label class="radio-inline">
                        <input type="radio" name="interst_option" value="dynamic" <?php if($row['int_option']=='dynamic'){echo 'checked';} ?>> Dynamic
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="interst_option" value="manual" <?php if($row['int_option']=='manual'){echo 'checked';} ?>> Manual
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
                              <input type="text" class="form-control" name="int_amount" placeholder="Enter Amount" id="int_amount" onkeypress="return NumericValidate(event,this)" value="<?php echo $row['int_amount']; ?>">
                              </div> 
                            </div>


                            <div class="col-md-3">
                            <div class="form-group">
                            <label for="int_days">Days</label>
                            <input type="text" class="form-control" name="int_days" placeholder="Enter Days" id="int_days" value="<?php echo $row['int_days']; ?>" onkeypress="return NumericValidate(event,this)">
                            </div> 
                            </div>
                        
                            <div class="col-md-3">
                              <div class="form-group">
                            <label for="int_rate">Rate</label>
                            <input type="text" class="form-control" name="int_rate" placeholder="Enter Rate" id="int_rate" value="<?php echo $row['int_rate']; ?>" onkeypress="return NumericValidate(event,this)">
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


                      <center>
                     <button type="button" id="btn_final_cal" class="btn btn-success">Calculate</button> 
                     </center> 
                     


                     <br>
                     <h4>Final Calculations</h4>  
                     <br> 

                                         
                   
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
                            <input type="text" class="form-control" name="tax" placeholder="Enter Tax in %" id="tax" value="<?php echo $row['tax']; ?>" onkeypress="return NumericValidate(event,this)">
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
                            <input type="text" class="form-control" name="tds_amount" placeholder="Enter TDS Amount" id="tds_amount" onkeypress ="return NumericValidate(event,this)" value="<?php echo $row['tds_amount'] ?>">
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
                          <input type="checkbox" class="form-check-input" name="is_paid" value="1" <?php if($row['is_paid']=='1') { echo 'checked'; } ?>>Paid
                        </label>
                      </div> 
                    </div>

                  <div class="row dynamicWrapper" style="margin-left: 0;">

                  <?php

                  if ($row['docimg'] != '') {
               
              
                  $prev = explode(',',$row['docimg']);
                  $prev_img_title = explode(',',$row['img_title']);

                  foreach ($prev as $key => $imging){
                    if($imging)
                      $attend =  $dir.$imging;
                    {
                      $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
                      $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

                      if(in_array($attendExt, $attend_allowExt)) 
                      { ?>

                      
                        <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_<?= $key+1 ?>">
                          <label class="image-label" for="docimg">Document File <?= $key+1 ?></label>
                            <div class="image-upload dynamic_field">
                              <?php if( $key != 0) {?>
                                <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
                              <?php } ?>
                              <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$prev[$key] ?>" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />

                              <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $prev[$key]; ?>">

                              <input type="file" class="form-control" id="img<?= $key+1 ?>" onchange="readURL(this);" name="docimg[<?= $key?>]" value="">
                              
                              <br>

                              <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
                            </div>



                        </div>
                        <?php
                     
                  
                  }else{
                    ?>

                    <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_<?= $key+1 ?>">
                          <label class="image-label" for="docimg">Document File <?= $key+1 ?></label>
                            <div class="image-upload dynamic_field">
                              <?php if( $key != 0) {?>
                                <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
                              <?php } ?>
                              <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$imging ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
                              <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $imging; ?>">
                              <div class="filed-form-control">  
                                                   
                                <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                                                 
                              </div>

                              <input type="file" class="form-control" id="img<?= $key+1?>" onchange="readURL(this);" name="docimg[<?= $key?>]" value="">
                              <br>
                              <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
                            </div>



                        </div>

                    <?php
                  }
                }
              }
            }else{?>

                <div class=" form-group  col-sm-4 pl-0 imgcount dynamic_field_1">
                      <label class="image-label" for="docimg">Document File 1</label>
                        <div class="image-upload dynamic_field">
                        
                          <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
                          <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="docimg[1]" value="">
                          <br>
                          <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
                        </div>



                    </div>


            <?php }?>

                    <div class="form-group form-group col-sm-4 pl-0">
                      <label class="image-label" for="docimg">Add more</label>
                       <div class="image-upload">
                        
                      <button type="button" class=" btn btn-defult" id="add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
                      </button>
                      

                    </div>

                    </div>
                    </div>




                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves" id="submt_btn">Update</button>
                    </div>
                </form>
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
      var delCount=0;
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');

                
                
            });
            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;
              // alert(i);
              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docimg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docimg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

           
            $('.imgcount').last().after(varietyfieldHTML);


            });
        });

        function removeImg(e,index) {
         $(e).parent('div').parent('div').remove();
         delCount=delCount+1; 
        }
      </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });


      //--------------------------


        //party select
       $('#epartySelect').on('change', function() {

         var selectedInvoice="<?php echo $row['invoice_no'] ?>";

          $.ajax({
            type: "POST",
            url: 'getInvoiceEdit.php',
            data: {party:this.value,selectedInvoice:selectedInvoice},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                console.log(jsonData);

                $('#invoice_no').find('option').not(':first').remove();

                $.each(jsonData,function(index,obj)
                {
                 var option_data="<option data-reportid="+obj.pur_report_id+" data-confno="+obj.pur_conf_no+" value="+obj.invoice_no+">"+obj.invoice_no+"</option>";
                  $(option_data).appendTo('#invoice_no'); 
                });  

                $('#invoice_no').prop('selectedIndex',0);

                //reset all input field
                 //reset all input garbed field

                $('#lot_no').val('');
                $('#pr_start').val('');
                $('#pr_end').val('');
                $('#broker').val('');
                $('#gross_amt').val('');
                $('#cndy_rate').val('');
                $('#invoice_no').val('');
                $('#original_rate').val('');
                $('#brok_bales').val('');
                $('#weight').val('');
                $('#wght_seller_slip').val('');
                 $('#rd_con').val('');
                 $('#len_con').val('');
                 $('#mic_con').val('');
                 $('#trs_con').val('');
                 $('#mois_con').val('');
                 $('#pur_conf_no').val('');
                
               
                      
            }
          });

      });




       //invoice select
      $('#invoice_no').on('change', function() {

          //error section hide for check invoice
         $('span.error-keyup-inv').hide();


        var id=$(this).find(':selected').attr('data-reportid');
        var conf_no=$(this).find(':selected').attr('data-confno');

    

        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {conf_no:conf_no,id:id},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                console.log(jsonData);


                //purchase report data
                $('#lot_no').val(jsonData.pur_data.lot_no);
                $('#pr_start').val(jsonData.pur_data.pr_no_start);
                $('#pr_end').val(jsonData.pur_data.pr_no_end);
               
                $('#weight').val(jsonData.pur_data.weight);
                $('#cndy_rate').val(jsonData.pur_data.cndy_rate);
                $('#invoice_no').val(jsonData.pur_data.invoice_no);
                $('#original_rate').val(jsonData.pur_data.cndy_rate);
                $('#brok_bales').val(jsonData.pur_data.bales);
                $('#pur_report_id').val(jsonData.pur_data.id);

                 $('#broker').val(jsonData.pur_data.broker_name);
                $('#broker_id').val(jsonData.pur_data.broker_id);
                
                var weight = jsonData.pur_data.weight;
                var candy_rate = jsonData.pur_data.cndy_rate;


                // weight calculation
                if (weight === '') {
                  weight = 0;
                } 
                if (candy_rate === '') {
                  candy_rate = 0;
                } 
                var total_gross =  parseFloat(weight)*(parseFloat(candy_rate)*0.2812/100);
                if(isNaN(total_gross))
                {
                  total_gross=0;
                }
                $('#gross_amt').val(total_gross.toFixed(2));

                $('#wght_seller_slip').val(jsonData.pur_data.weight);



                //confirmation Data
                 $('#rd_con').val(jsonData.conf_data.pro_rd);
                 $('#len_con').val(jsonData.conf_data.pro_length);
                 $('#mic_con').val(jsonData.conf_data.pro_mic);
                 $('#trs_con').val(jsonData.conf_data.pro_trash);
                 $('#mois_con').val(jsonData.conf_data.pro_mois);
                 $('#pur_conf_no').val(jsonData.conf_data.pur_conf);

                
           }
       });
        
      });




      //On Weight Change Take Same Value in Seller Slip Field
       $('#weight').on('input', function() {

            var w =$('#weight').val();
             $('#wght_seller_slip').val(w);
      });



      //calculate RD
      $('#rd_con').on('input', function() {
            calculateRD();
      });

      $('#rd_lab').on('input', function() {
            calculateRD();
      });

      $('#rd_cndy').on('input', function() {
            calculateRD();
      });

      function calculateRD()
      {
        var rd_con=$('#rd_con').val();
        var rd_lab=$('#rd_lab').val();
        var rd_cndy=$('#rd_cndy').val();

        var org_rate=$('#original_rate').val();
        var grs_amt=$('#gross_amt').val();
        var weight=$('#weight').val();


        var cal1=parseFloat(rd_con-rd_lab).toFixed(2);

        var cal2=parseFloat(grs_amt-(weight*(org_rate-rd_cndy)*0.2812/100)).toFixed(2);

        console.log('rd amt:'+cal2)


        if(cal1>0)
        {
           $('#rd_diff').val(cal1);
        }
        else
        {
          $('#rd_diff').val(0);
        }

        if(cal2>0)
        {
          $('#rd_amt').val(cal2);
        }
        else
        {
          $('#rd_amt').val(0);
        }

      }



      //calculate Length

       $('#len_con').on('input', function() {
            calculateLength();
      });

      $('#len_lab').on('input', function() {
            calculateLength();
      });

      $('#len_cndy').on('input', function() {
            calculateLength();
      });

      function calculateLength()
      {
        var len_con=$('#len_con').val();
        var len_lab=$('#len_lab').val();
        var len_cndy=$('#len_cndy').val();

        var org_rate=$('#original_rate').val();
        var grs_amt=$('#gross_amt').val();
        var weight=$('#weight').val();


        var cal1=parseFloat(len_con-len_lab).toFixed(2);

        var cal2=parseFloat(grs_amt-(weight*(org_rate-len_cndy)*0.2812/100)).toFixed(2);

        console.log('len amt'+cal2)


        if(cal1>0)
        {
           $('#len_diff').val(cal1);
        }
        else
        {
          $('#len_diff').val(0);
        }

        if(cal2>0)
        {
          $('#len_amt').val(cal2);
        }
        else
        {
          $('#len_amt').val(0);
        }

      }




      //calculate Mic

       $('#mic_con').on('input', function() {
            calculateMic();
      });

      $('#mic_lab').on('input', function() {
            calculateMic();
      });

      $('#mic_cndy').on('input', function() {
            calculateMic();
      });

      function calculateMic()
      {
        var mic_con=$('#mic_con').val();
        var mic_lab=$('#mic_lab').val();
        var mic_cndy=$('#mic_cndy').val();

        var org_rate=$('#original_rate').val();
        var grs_amt=$('#gross_amt').val();
        var weight=$('#weight').val();


        var cal1=parseFloat(mic_con-mic_lab).toFixed(2);

        var cal2=parseFloat(grs_amt-(weight*(org_rate-mic_cndy)*0.2812/100)).toFixed(2);

        console.log('mic amt'+cal2)


        if(cal1>0)
        {
           $('#mic_diff').val(cal1);
        }
        else
        {
          $('#mic_diff').val(0);
        }

        if(cal2>0)
        {
          $('#mic_amt').val(cal2);
        }
        else
        {
          $('#mic_amt').val(0);
        }

      }





      //Calculate Trash

       $('#trs_con').on('input', function() {
            calculateTrs();
      });

      $('#trs_lab').on('input', function() {
            calculateTrs();
      });

     

      function calculateTrs()
      {
        var trs_con=$('#trs_con').val();
        var trs_lab=$('#trs_lab').val();
       
        
        var grs_amt=$('#gross_amt').val();
       


        var cal1=parseFloat(trs_lab-trs_con).toFixed(2);

        var cal2=parseFloat(grs_amt*cal1/100).toFixed(2);

        console.log('trs amt'+cal2)


        if(cal1>0)
        {
           $('#trs_diff').val(cal1);
        }
        else
        {
          $('#trs_diff').val(0);
        }

        if(cal2>0)
        {
          $('#trs_amt').val(cal2);
        }
        else
        {
          $('#trs_amt').val(0);
        }

      }



       //Calculate Moisture

       $('#mois_con').on('input', function() {
            calculateMois();
      });

      $('#mois_lab').on('input', function() {
            calculateMois();
      });

     

      function calculateMois()
      {
        var mois_con=$('#mois_con').val();
        var mois_lab=$('#mois_lab').val();
       

     
        var grs_amt=$('#gross_amt').val();
       


        var cal1=parseFloat(mois_lab-mois_con).toFixed(2);

        var cal2=parseFloat(grs_amt*cal1/100).toFixed(2);

        console.log('mois amt'+cal2)


        if(cal1>0)
        {
           $('#mois_diff').val(cal1);
        }
        else
        {
          $('#mois_diff').val(0);
        }

        if(cal2>0)
        {
          $('#mois_amt').val(cal2);
        }
        else
        {
          $('#mois_amt').val(0);
        }

      }



      //Calculate Sample

       $('#smp_kg').on('input', function() {
            calculateSmp();
      });

      function calculateSmp()
      {
        var smp_kg=$('#smp_kg').val();
        var org_rate=$('#original_rate').val();
       
        var cal=parseFloat(smp_kg*(org_rate*0.2812/100)).toFixed(2);


        console.log('Sample amt'+cal)


        if(cal>0)
        {
           $('#smp_amt').val(cal);
        }
        else
        {
          $('#smp_amt').val(0);
        }

      }




      //Calculate Tare

       $('#tare_kg').on('input', function() {
            calculateTare();
      });

      function calculateTare()
      {
        var tare_kg=$('#tare_kg').val();
        var org_rate=$('#original_rate').val();
       
        var cal=parseFloat(tare_kg*(org_rate*0.2812/100)).toFixed(2);


        console.log('tare amt'+cal)


        if(cal>0)
        {
           $('#tare_amt').val(cal);
        }
        else
        {
          $('#tare_amt').val(0);
        }

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

      $('input[type=radio][name=brokerage_option]').change(function() {
            if (this.value == 'dynamic') 
            {
               $('.dynamicBrokerage').show();
               $('.manualBrokerage').hide();

                 $('#brok_reason').val('');
                 $('#brok_manual_amt').val('');


            }
            else if (this.value == 'manual') 
            {
               $('.manualBrokerage').show();
               $('.dynamicBrokerage').hide();

              
               $('#brok_per_bales').val('');
               $('#brok_dynamic_amt').val('');

            }
        });




      //Calculate Dynamic Brokerage

       $('#brok_bales').on('input', function() {
            calculateBrokerage();
      });

       $('#brok_per_bales').on('input', function() {
            calculateBrokerage();
      });

      function calculateBrokerage()
      {
        var brok_bales=$('#brok_bales').val();
        var brok_per_bales=$('#brok_per_bales').val();
       
        var cal=parseFloat(brok_bales*brok_per_bales).toFixed(2);


        console.log('Brokerage amt'+cal)


        if(cal>0)
        {
          $('#brok_dynamic_amt').val(cal);
           $('#brok_amt').val(cal);
        }
        else
        {
          $('#brok_dynamic_amt').val(0);
          $('#brok_amt').val(0);
        }

      }

      //Manual Brokrage Set
      $('#brok_manual_amt').on('input', function() {

            var bma=$('#brok_manual_amt').val();
            $('#brok_amt').val(bma);
      });



      //Calculate Weight Shortage

       $('#wght_seller_slip').on('input', function() {
            calculateWeightShoratage();
      });

       $('#wght_our_slip').on('input', function() {
            calculateWeightShoratage();
      });

       $('#wght_allow').on('input', function() {
            calculateWeightShoratage();
      });
       

      function calculateWeightShoratage()
      {
        var wght_seller_slip=$('#wght_seller_slip').val();
        var wght_our_slip=$('#wght_our_slip').val();
        var wght_allow=$('#wght_allow').val();

        var org_rate=$('#original_rate').val();


        var cal=parseFloat(wght_seller_slip-wght_our_slip).toFixed(2);
        var cal2=parseFloat(cal-wght_allow).toFixed(2);
        var cal3=parseFloat(cal2*(org_rate*0.2812/100)).toFixed(2);


        console.log('wight Shoratage amt '+cal)


        if(cal>0)
        {
           $('#wght_diff').val(cal);
        }
        else
        {
          $('#wght_diff').val(0);
        }

        if(cal2 > 0)
        {
          $('#wght_shortage').val(cal2);
        }
        else
        {
          $('#wght_shortage').val(0);
        }

        if(cal3>0)
        {
          $('#wght_shortage_amt').val(cal3);
        }
         else
        {
          $('#wght_shortage_amt').val(0);
        }

      }


        //Repress calculation

       $('#repress_no_of_bales').on('input', function() {
            calculateRepress();
      });

       $('#repress_per_bales').on('input', function() {
            calculateRepress();
      });

      function calculateRepress()
      {
        var repress_bales=$('#repress_no_of_bales').val();
        var repress_per_bales=$('#repress_per_bales').val();
       
        var cal=parseFloat(repress_bales*repress_per_bales).toFixed(2);


        console.log('repress total'+cal)


        if(cal>0)
        {
           $('#repress_total').val(cal);
        }
        else
        {
          $('#repress_total').val(0);
        }

      }


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

      $("#other_check").change(function() {
          if(this.checked) 
          {
              $(".otherSection").show();
               $(this).attr('value', 'true');
          }
          else
          {
              $(".otherSection").hide();
              $("#other_reason").val('');
              $("#other_amount").val('');
              $(this).attr('value', 'false');
          }

        });


        //Rate Difference Calculation

       $('#rate_diff_candy').on('input', function() {
            calculateRateDifference();

      });

      function calculateRateDifference()
      {
        var diff_candy=parseFloat($('#rate_diff_candy').val());
        var org_rate=parseFloat($('#original_rate').val());
        var gross_amt=parseFloat($('#gross_amt').val());
        var weight=parseFloat($('#weight').val());
       
        var cal=parseFloat(gross_amt-(weight*(org_rate-diff_candy)*0.2812/100)).toFixed(2);


        console.log('Rate Diff AMT '+cal)


        if(cal>0)
        {
           $('#rate_diff_amt').val(cal);
        }
        else
        {
          $('#rate_diff_amt').val(0);
        }

      }






     //Interset Config setting
      var getRadioVal=$('input[type=radio][name=interst_option]:checked').val();
      if(getRadioVal=='dynamic')
      {
          $('.dynamicIntClass').show();
          $('.manualIntClass').hide();

          calculateDynamicInterest();

          $('#int_amount').val('');
          $('#int_days').val('');
          $('#int_rate').val('');
          $('#manual_int').val('');

      }
      else
      {
          $('.manualIntClass').show();
          $('.dynamicIntClass').hide();
      }

      $('input[type=radio][name=interst_option]').change(function() {
            if (this.value == 'dynamic') 
            {
               $('.dynamicIntClass').show();
               $('.manualIntClass').hide();

               calculateDynamicInterest();

                $('#int_amount').val('');
                $('#int_days').val('');
                $('#int_rate').val('');
                $('#manual_int').val('');


            }
            else if (this.value == 'manual') 
            {
               $('.manualIntClass').show();
               $('.dynamicIntClass').hide();
            }
        });


      // Manual Interest Calculation
       $('#int_days').on('input', function() {
            calculateManualInterest();
            $( "#btn_final_cal" ).click();
      });

       $('#int_rate').on('input', function() {
            calculateManualInterest();
            $( "#btn_final_cal" ).click();
      });
       $('#int_amount').on('input', function() {
            calculateManualInterest();
            $( "#btn_final_cal" ).click();
      });

      function calculateManualInterest()
      {
        var days=Number($('#int_days').val());
        var rate=Number($('#int_rate').val());
        var amount=Number($('#int_amount').val());
       
        var cal=parseFloat(amount*days*rate/36000).toFixed(2);

        console.log('manual Interest total'+cal)

        if(cal>0)
        {
           $('#manual_int').val(cal);
           $('#final_int').val(cal);
        }
        else
        {
          $('#manual_int').val(0);
          $('#final_int').val(0);
        }

      }
      // Dynamic Interest Calculation
      function calculateDynamicInterest()
      {
        var ad_hoc=Number($('#ad_hoc').val());
        cal_int=parseFloat(ad_hoc)*15*15/36000;
        $('#dynamic_int').val(cal_int);
        $('#final_int').val(cal_int);

      }


       $('#ad_hoc').on('input', function() {

         calculateDynamicInterest();
          BalPayableCalculate();

      });





      //final Calculations

       $('#btn_final_cal').on('click', function() {

        //enable Submit Button
        $('#submt_btn').prop('disabled', false);


        var ad_hoc=parseFloat($('#ad_hoc').val());


         //Final Intreset Set (Dynamic / Manual)

         var getRadioVal=$('input[type=radio][name=interst_option]:checked').val();


         var cal_int=0.00;
         if(getRadioVal=='dynamic')
         {
            cal_int=parseFloat($('#dynamic_int').val()).toFixed(2);
         }
         else
         {
            var cal_int=parseFloat($('#manual_int').val()).toFixed(2);
         }

        if(cal_int>0)
        {
            $('#final_int').val(cal_int);
        }
        else
        {
          $('#final_int').val(0);
        }




        var gross_amt=parseFloat($('#gross_amt').val());
        var wght_shortage_amt=parseFloat($('#wght_shortage_amt').val());
        var brok_amt=parseFloat($('#brok_amt').val());
        var tare_amt=parseFloat($('#tare_amt').val());
        var smp_amt=parseFloat($('#smp_amt').val());
        var mois_amt=parseFloat($('#mois_amt').val());
        var trs_amt=parseFloat($('#trs_amt').val());
        var len_amt=parseFloat($('#len_amt').val());
        var rd_amt=parseFloat($('#rd_amt').val());

        var mic_amt=parseFloat($('#mic_amt').val());
        var rate_diff_amt=parseFloat($('#rate_diff_amt').val());
        var repress_total=parseFloat($('#repress_total').val());



         var final_debit_with_tax = $('#final_debit_with_tax').val();



      

        //Debit Amount Calculation
        var cal_deb_amt=
        parseFloat(cal_int)+
        parseFloat(wght_shortage_amt)+
        parseFloat(brok_amt)+
        parseFloat(tare_amt)+
        parseFloat(smp_amt)+
        parseFloat(mois_amt)+
        parseFloat(trs_amt)+
        parseFloat(len_amt)+
        parseFloat(rd_amt)+
        parseFloat(rate_diff_amt)+
        parseFloat(repress_total)+
        parseFloat(mic_amt);

        if($("#other_check").prop('checked') == true)
        {
            var other_amt=$("#other_amount").val();
            cal_deb_amt=parseFloat(cal_deb_amt)+parseFloat(other_amt);
        }

        if(isNaN(cal_deb_amt))
          {
            cal_deb_amt=0;
          }

         $('#final_deb_amt').val(cal_deb_amt.toFixed(2));


         //Balance Payable Calculate
         BalPayableCalculate();

     

      });


         $('#tds_amount').on('input', function() {

          BalPayableCalculate();

         
          
      });


       function BalPayableCalculate()
       {

        var gross_amt=$('#gross_amt').val();
       var final_debit_with_tax = $('#final_debit_with_tax').val();
        var ad_hoc=$('#ad_hoc').val();


        if(isNaN(final_debit_with_tax) || final_debit_with_tax=='')
        {
          final_debit_with_tax=0;
        }

        if(isNaN(ad_hoc) || ad_hoc=='')
        {
          ad_hoc=0;
        }



          var tds_amount = $('#tds_amount').val();

          if(tds_amount=='')
          {
            tds_amount=0;
          }
          


          var cal_bal_pay=parseFloat(gross_amt)-parseFloat(ad_hoc)-parseFloat(final_debit_with_tax)-parseFloat(tds_amount);

            console.log('Payable Amomunt '+cal_bal_pay)

            //if value in minus then display 0
            /*if(cal_bal_pay>0)
            {
              $('#final_bal_pay').val(cal_bal_pay.toFixed(2));
            }
            else
            {
               $('#final_bal_pay').val(0);
            }*/

            if(isNaN(cal_bal_pay) || cal_bal_pay=='')
            {
              cal_bal_pay=0;
            }

            $('#final_bal_pay').val(cal_bal_pay.toFixed(2));

       }




         // Tax Calculation
       $('#tax').on('input', function() {
            calculateTax();
            BalPayableCalculate();
      });

      function calculateTax()
      {
        var tax=parseFloat($('#tax').val());
        var debitAmt=parseFloat($('#final_deb_amt').val());
        
       
        var cal_tax=parseFloat(debitAmt*tax/100).toFixed(2);
        console.log('tax amt '+cal_tax)

        var cal2=parseFloat(debitAmt)+parseFloat(cal_tax);

        console.log('final debit with tax '+cal2)

        if(cal_tax>0)
        {
           $('#tax_amount').val(cal_tax);
        }
        else
        {
          $('#tax_amount').val(0);
        }

        if(cal2>0)
        {
           $('#final_debit_with_tax').val(cal2.toFixed(2));
        }
        else
        {
          $('#final_debit_with_tax').val(0);
        }

      }




       // Ad-Hoc validation
      $('#ad_hoc').keyup(function(){
        var ad_hoc = parseInt($(this).val());
        var gross_amt = parseInt($('#gross_amt').val());
        $('span.error-keyup-1').hide();
        if(gross_amt < ad_hoc)
        {
          $(this).after('<span class="error error-keyup-1 text-danger">Ad-Hoc Should no be greater than Available Gross Amount..</span>'); 
          $("#btn_final_cal").attr("disabled", true);  
        } 
        else{
          $("#btn_final_cal").attr("disabled", false);  
        }       
      });


});


loadDefault();
function loadDefault()
{

  var SelectedParty = $('#epartySelect :selected').val();
  var selectedInvoice="<?php echo $row['invoice_no'] ?>";

    $.ajax({
            type: "POST",
            url: 'getInvoiceEdit.php',
            data: {party:SelectedParty,selectedInvoice:selectedInvoice},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                console.log(jsonData);

                $('#invoice_no').find('option').not(':first').remove();

                $.each(jsonData,function(index,obj)
                {
                 var option_data="<option data-reportid="+obj.pur_report_id+" data-confno="+obj.pur_conf_no+" value="+obj.invoice_no+">"+obj.invoice_no+"</option>";
                  $(option_data).appendTo('#invoice_no'); 
                });  

                //$('#invoice_no').prop('selectedIndex',0);
                $('#invoice_no option[value='+selectedInvoice+']').attr('selected','selected');

                     
            }
          });




}











 function readURL(input) {
            var url = input.value;
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

            $(input).parent().find('span.error-keyup-110').hide();
            if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) 
            {

                var reader = new FileReader();

                const fsize = input.files[0].size;
                const file_size = Math.round((fsize / 1024));


               

                if(file_size>1150) //1.1 MB
                {
                  $(input).after('<span class="error error-keyup-110 text-danger">Image Size Should Be 1 MB or Lesser...</span>');
                  $(input).val(''); 

                   imgId = '#preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');

                }
                else
                {
                    reader.onload = function (e) {
                        imgId = '#preview-'+$(input).attr('id');
                        $(imgId).attr('src', e.target.result);
                    }

                     reader.readAsDataURL(input.files[0]);
                }
                

            }
            else
            {
                  imgId = '#preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');
                  //$(imgId).find(".msg").html("This is not Image");
                 //$('.imagepreview').attr('src', '/assets/no_preview.png');
            }
}  
 
function OnlyNumberValidation(key) {
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
  function NumericValidate(evt, element) {

     var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;       
}           

</script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>




  </body>
</html>



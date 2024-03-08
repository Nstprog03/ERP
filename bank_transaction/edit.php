<?php
   session_start();
   require_once('../db.php');
   if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
     header("location: ../login.php");
     exit;
   }
   
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];
    $getYear = $_SESSION['bank_transaction_financial_year'];
    $year_array = explode("/", $getYear);
   
   if (isset($_GET['id'])) {
     $id = $_GET['id'];
     $invoice = array();
     $ext_prt_id = '0';
     $sql = "select * from bank_transaction where id=".$id;
     $result = mysqli_query($conn, $sql);
     if (mysqli_num_rows($result) > 0) {
       $row = mysqli_fetch_assoc($result);
       if(isset($row['ext_party']) && $row['ext_party'] != "" && $row['ext_party'] != null){
         $ext_prt_id = $row['ext_party'];
       }
     }else {
       $errorMsg = 'Could not Find Any Record';
     }
   }
   
   if(isset($_POST['Submit'])){


       $firm_id = $_SESSION["bank_transaction_firm_id"];
       $bank_tran_financial_year_id = $_SESSION["bank_transaction_financial_year_id"];
       $username=$_SESSION['username'];
       
      $date =explode("/",$_POST['date']);

      $date = $date[2]."-".$date[1]."-".$date[0];
       
       
       $bank = $_POST['bank'];
       $bankbalance = $_POST['bankbalance'];
       $ext_party = 0;
       if(isset($_POST['ext_party'])){
         $ext_party = $_POST['ext_party'];
       }
       $table = $_POST['table'];

      $pay_to = '0';
      if (isset($_POST['pay_to']) && $table == "Other Payout")
      {
         $pay_to = $_POST['pay_to'];
      }
      else
      {
         $pay_to = "0";
      }

      $invoice_id = array();
      if (isset($_POST['invoice_id'])) {
         $invoice_id = $_POST['invoice_id'];
      }

      $invoice_no = array();
      if (isset($_POST['invoice_no'])) {
            $invoice_no = $_POST['invoice_no'];
      } 

      $payment = array();
      if (isset($_POST['payment'])) {
            $payment = $_POST['payment'];
      }

      $sub_tran_id = array();
      if(isset($_POST['sub_tran_id'])){
         $sub_tran_id = $_POST['sub_tran_id'];
      }

      $total_pay = "0";
      if (isset($_POST['total_payment']))
      {
          $total_pay = $_POST['total_payment'];
      }

      $quantity = 0;
      if(isset($_POST['quantity']) && $table == "URD Kapas purchase Payment"){
         $quantity = $_POST['quantity'];
      }

      $rate = 0;
      if(isset($_POST['rate']) && $table == "URD Kapas purchase Payment"){
         $rate = $_POST['rate'];
      }

      $remark = "";
      if(isset($_POST['remark'])){
         $remark = $_POST['remark'];
      }

      date_default_timezone_set('Asia/Kolkata');
      $timestamp = date("Y-m-d H:i:s");
   
     if(!isset($errorMsg)){

      $main_sql = "SELECT * FROM bank_transaction WHERE id = '".$id."'";
      $main_result = mysqli_query($conn,$main_sql);
      if(mysqli_num_rows($main_result) > 0){
         $main_row = mysqli_fetch_assoc($main_result);

         $minus = false;
         $old_pay = $main_row['total_payment'];
         if($old_pay <= $total_pay){
            $diffrance = $total_pay - $old_pay;
            $minus = true;
         }else{
            $diffrance = $old_pay - $total_pay;
         }

         $sql = "update bank_transaction
            set date = '".$date."',
            bank = '".$bank."',
            bankbalance = '".$bankbalance."',
            bank_balance_id = '".$_POST['bank_balance_id']."',
            ext_party='".$ext_party."',
            table_indicator='".$table."',
            total_payment='".$total_pay."',
            pay_to = '".$pay_to."',
            quantity = '".$quantity."',
            rate = '".$rate."',
            remark = '".$remark."',
            username='".$username."',
            updated_at='".$timestamp."'
            where id=".$id;

         $result = mysqli_query($conn, $sql);
         if(isset($result)){

            $sql2  = "SELECT * FROM `bank_transaction` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $bank_tran_financial_year_id . "' ORDER BY  `date` ASC,`id` ASC";
            
            $result2 = mysqli_query($conn,$sql2);
            if(mysqli_num_rows($result2) > 0){
               while($row2 = mysqli_fetch_assoc($result2)){
                  if($date == $row2['date'] && $id >= $row2['id']){
                  }else{
                     if($minus == true){
                        $bal = $row2['bankbalance'] - $diffrance;
                     }else{
                        $bal = $row2['bankbalance'] + $diffrance;
                     }
                  
                  $sql3        = "UPDATE `bank_transaction` SET `bankbalance`='" . $bal . "' WHERE id = '" . $row2['id'] . "'";
                  $result3 = mysqli_query($conn, $sql3);
                  }
               }
            }
         
            $invoiceArr = array();
            $sql_sub_tran = "SELECT * FROM `bank_transaction_history` WHERE  `bank_transaction_id` = '".$id."'";
            $result_sub_tran = mysqli_query($conn,$sql_sub_tran);
            if(mysqli_num_rows($result_sub_tran) > 0){
               while($sub_tran_row = mysqli_fetch_assoc($result_sub_tran)){
                  $invoiceArr[] = $sub_tran_row['id'];
                  if(!in_array($sub_tran_row['id'],$sub_tran_id)){
                     $delete = "delete from bank_transaction_history where id = ".$sub_tran_row['id'];
                     $delete_result = mysqli_query($conn,$delete);
                  }
               }
            }

            foreach($invoice_id as $k => $val){
               if(isset($sub_tran_id[$k]) && in_array($sub_tran_id[$k],$invoiceArr)){
                  $update_sub_tran = "UPDATE `bank_transaction_history` SET `payment` = '".$payment[$k]."',`updated_at`='".$timestamp."' WHERE id = ".$sub_tran_id[$k];
                  $update_result = mysqli_query($conn,$update_sub_tran);
               }else{
                  $sub_transaction = "insert into bank_transaction_history (bank_transaction_id,invoice_id,invoice_no,payment,created_at,updated_at) 
                  values('".$id."','".$val."','".$invoice_no[$k]."','".$payment[$k]."','".$timestamp."','".$timestamp."')";
                  $result_sub = mysqli_query($conn,$sub_transaction);
               }
            }

            $sqlbank = "SELECT * FROM `bank_balance` WHERE  `bank_peyout` = '".$id."' AND `bank` = '".$bank."' AND `firm` = '".$firm_id."' AND `financial_year` = '".$bank_tran_financial_year_id."' ORDER BY `bank_balance`.`id` DESC";
            
            $result = mysqli_query($conn,$sqlbank);
            if(mysqli_num_rows($result) > 0){
               $bank_row = mysqli_fetch_assoc($result);

               $sql_bal    = "SELECT * FROM `bank_balance` WHERE date >= '" . $bank_row['date'] . "' AND `bank` = '" . $bank_row['bank'] . "' AND `firm` = '" . $bank_row['firm'] . "' AND `financial_year` = '" . $bank_row['financial_year'] . "' ORDER BY  `bank_balance`.`date` ASC,`bank_balance`.`id` ASC";
               $result_bal = mysqli_query($conn, $sql_bal);
               if (mysqli_num_rows($result_bal) > 0) {
                  while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                        if($date == $bal_row['date'] && $bank_row['id'] >= $bal_row['id']){
                        }else{
                           if($bank_row['payment'] <=  $total_pay){
                              $diff = $total_pay - $bank_row['payment'];
                              $pre_bal       = $bal_row['previous_balance'] - $diff;
                              $clo_bal       = $bal_row['total_balance'] - $diff;
                           }else{
                              $diff = $bank_row['payment'] - $total_pay;
                              $pre_bal       = $bal_row['previous_balance'] + $diff;
                              $clo_bal       = $bal_row['total_balance'] + $diff;
                           }
                           $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                           $update_result = mysqli_query($conn, $update);
                        }
                  }
               }
               if(isset($bank_row)){
                  $pri_blnc = $bank_row['previous_balance'];
                  $total_blnc = $pri_blnc - $total_pay; 
                  $update = "UPDATE `bank_balance` SET `total_balance`='".$total_blnc."',`payment`='".$total_pay."',`username`='".$username."',`updated_at`='".$timestamp."' WHERE id = ".$bank_row['id'];
                  $bank_result = mysqli_query($conn,$update);
               }
            }

         $successMsg = 'New record added successfully';
         header('Location: home.php');
         }
     }else{
       $errorMsg = 'Error '.mysqli_error($conn);
     }
   }
   }
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Bank Payout Edit</title>
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
      <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
      <script> 
         $(function(){
           $("#sidebarnav").load("../nav.html"); 
           $("#topnav").load("../nav2.html"); 
           
           $(".datepicker").datepicker({
                 dateFormat:'dd/mm/yy',
                 changeMonth: true,
                 changeYear: true,
                 maxDate: new Date('<?php echo ($year_array[1]) ?>'),
                 minDate: new Date('<?php echo ($year_array[0]) ?>')
             });
             $(".datepicker").keydown(false);
             $('.selectpicker').selectpicker();
         });
      </script>   
      <style>
         #External-Party .dropdown-menu{
            width:100px;   
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
                  <a class="navbar-brand" href="home.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Bank Payout</span></a>
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                  <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                     <ul class="navbar-nav mr-auto"></ul>
                     <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="btn btn-outline-danger" href="home.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
                     </ul>
                  </div>
               </div>
            </nav>
            <!-- last change on table START-->
            <div class="last-updates">
               <?php
                  $sqlLastChange="select username,updated_at from bank_transaction where id='".$row['id']."'";
                  
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
                  $table = $row['table_indicator'];
                  $exr_prtArr = array();
                  if(isset($table) && $table == "Debit Note Ad-Hoc" || $table == "Bales Payout"){
                     $sql2= "SELECT * FROM `debit_report` WHERE `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
                     $result2 = mysqli_query($conn,$sql2);
                     if(mysqli_num_rows($result2) > 0){
                        while($row2 = mysqli_fetch_assoc($result2)){
                           $exr_prtArr[] = $row2['party'];
                        }
                     }
                  }elseif(isset($table) && $table == "Transport Payout"){
                     $sql3="select * from pur_report where `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
                     $result3 = mysqli_query($conn, $sql3);
                     foreach ($conn->query($sql3) as $key => $row3) 
                     {
                           $exr_prtArr[] = $row3['party'];
                     }
                  }elseif(isset($table) && $table == "RD Kapas purchase Payment"){
                     $sql4="select * from rd_kapas_report where `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year_id` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
                     $result4 = mysqli_query($conn, $sql4);
                     foreach ($conn->query($sql4) as $key => $row4) 
                     {
                           $exr_prtArr[] = $row4['external_party'];
                     }
                  }
                  $ext_prt =  array_unique($exr_prtArr);
                  ?>
            </div>

            <!-- last change on table END-->            
            <div class="container-fluid">
               <div class="row justify-content-center">
                  <div class="card">
                     <div class="card-header">
                        Edit Bank Payout
                     </div>
                     <div class="card-body">
                        <form class="" action="" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="used_balance" value = "<?php if(isset($used_bal)){echo $used_bal; } ?>">
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="date">Date</label>
                                 <input type="text" class="form-control" placeholder="Enter Date" name="date" autocomplete="off" value="<?php echo date("d/m/Y", strtotime($row['date']));  ?>" readonly>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="bank_ac_number">Bank</label>
                                 <?php
                                    $sql = "select * from party where id = ". $_SESSION["bank_transaction_firm_id"];
                                    $result = mysqli_query($conn, $sql);
                                    if(mysqli_num_rows($result) > 0){
                                       $firm_row = mysqli_fetch_assoc($result);
                                       $bank = json_decode($firm_row['bankDetails'],true);
                                    }
                
                                    if(isset($bank)){                
                                       foreach ($bank as $result) 
                                       {
                                          if($row['bank'] == $result['bank_ac_number']){
                                             $bankData['ac_number'] = $result['bank_ac_number'];
                                             $bankData['bank_name'] = $result['bank_name'];
                                          }
                                       }
                                    }
                                 ?> 
                                 <input type="text" class="form-control" placeholder="Bank Balance"  value="<?php if(isset($bankData['bank_name'])){ echo $bankData['bank_name']; } ?>" readonly>
                                 <input type="hidden" name="bank" value ="<?php if(isset($bankData['ac_number'])){ echo $bankData['ac_number']; } ?>">
                                 <input type="hidden" name="bank_balance_id" id="balance_id" value ="<?php echo $row['bank_balance_id']; ?>">
                              </div>

                              <div class="form-group col-md-4">
                                 <label for="balance">Balance</label>
                                 <input type="text" class="form-control" placeholder="Bank Balance" name="bankbalance" id="bankbalance" value="<?php echo $row['bankbalance'] ?>" readonly>
                              </div>

                              <div class="form-group col-sm-4">
                                 <label for="table">Select Table</label>
                                 <input type="text" class="form-control" placeholder="Table" name="table" id="table" value="<?php echo $row['table_indicator'] ?>" readonly>
                                 <!-- <select name="table" id="table" class="form-control">
                                    <option selected="" disabled="">Select Option</option>
                                    <option <?php if($row['table_indicator'] == "Debit Note Ad-Hoc"){ echo "selected"; } ?> vlaue="Debit Note Ad-Hoc">Debit Note Ad-Hoc</option>
                                    <option <?php if($row['table_indicator'] == "Bales Payout"){ echo "selected"; } ?> vlaue="Bales Payout">Bales Payout</option>
                                    <option <?php if($row['table_indicator'] == "Transport Payout"){ echo "selected"; } ?> vlaue="Transport Payout">Transport Payout</option>
                                    <option <?php if($row['table_indicator'] == "RD Kapas purchase Payment"){ echo "selected"; } ?> vlaue="RD Kapas purchase Payment">RD Kapas purchase Payment</option>
                                    <option <?php if($row['table_indicator'] == "URD Kapas purchase Payment"){ echo "selected"; } ?> vlaue="URD Kapas purchase Payment">URD Kapas purchase Payment</option>
                                    <option <?php if($row['table_indicator'] == "Other Payout"){ echo "selected"; } ?> vlaue="Other Payout">Other Payout</option>
                                 </select> -->
                                 <span id="table_err" style="color: red;font-size: 12px;"></span>
                              </div>

                              <div class="col-md-4 calculate" style="display:none;">
                                 <div class="row">
                                    <div class="col-sm-6">
                                       <label for="quantity">Quantity</label>
                                       <input type="number" class="form-control" name="quantity"  placeholder="Quantity" id="quantity" value="<?= $row['quantity'] ?>" autocomplete="off">
                                    </div>
                                    <div class="col-sm-6">
                                       <label for="rate">Rate</label>
                                       <input type="number" class="form-control" name ="rate"  placeholder="Rate"  id="rate" value="<?= $row['rate'] ?>" autocomplete="off" >
                                    </div>
                                 </div>
                              </div>

                              <div class="form-group col-sm-4" id="pay_to" <?php if($row['table_indicator'] != "Other Payout"){echo 'style="display:none;"';} ?> >
                                 <label class="w-100" for="table">Pay To</label>
                                 <div class="form-check form-check-inline">
                                       <input class="form-check-input" type="radio" name="pay_to" id="inlineRadio1" <?php if($row['pay_to'] == '0'){ echo "checked";} ?> value="0">
                                       <label class="form-check-label" for="inlineRadio1">External Party</label>
                                 </div>

                                 <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pay_to" id="inlineRadio2" <?php if($row['pay_to'] == '1'){ echo "checked";} ?> value="1">
                                    <label class="form-check-label" for="inlineRadio2">Broker</label>
                                 </div>
                              </div>

                              <div class="form-group col-sm-4" id="External-Party">
                                 <label for="ext_prt">Select External Party/Transport</label>
                                 <a title='Add New External Party' class="btn btn-primary btn-sm float-right" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>                    
                                 <?php
                                    $sql = "select * from external_party";
                                    $result = mysqli_query($conn, $sql);
                                    ?>  
                                 <select name="ext_party" id="ext_party_id" class="form-control selectpicker" data-live-search="true">
                                    <option selected="" value=""  disabled="">Select Option</option>
                                 </select>
                              </div>
                              <div class="form-group col-sm-4">
                                 <label for="payment">Payment</label>
                                 <input type="text" class="form-control" name ="total_payment"  placeholder="Enter Payment" value="<?php echo $row['total_payment']; ?>" id="total_payment" autocomplete="off">
                                 <span id="total_err" style="color: red;font-size: 12px;"></span>
                              </div>
                           </div>
                           <br/>
                           <div class="InvoiceSection" style="<?php if($row['table_indicator'] == "Other Payout"){ echo "display:none"; } ?>">
                              <div class="row">
                                 <div class="form-group col-sm-4">
                                    <label for="ext_prt">Select Invoice No./Farmer Name</label>
                                    <a title='Add New Farmer' class="btn btn-primary btn-sm float-right" id="farmer_add" target="_blank" href="/farmer/create.php" style="display: none;"><i class="fa fa-user-plus"></i></a>
                                    <select id="invoice_no" class="form-control selectpicker" data-live-search="true">
                                       <option value="" selected="" disabled="">Select Option</option>
                                    </select>
                                 </div>
                                 <div class="form-group col-sm-4">
                                    <label for="payment">Payment</label>
                                    <input type="text" class="form-control"  placeholder="Enter Payment" value="" id="payment" autocomplete="off" >
                                    <span id="payment_err" style="color: red;font-size: 12px;"></span>
                                 </div>
                                 <div class="form-group col-md-1">
                                    <button type="button" class="btn btn-primary" style="margin-top:32px" id="add_invoice">Add</button>
                                 </div>
                              </div>
                              <br/>
                              <div class="dynamicSection">
                                 <?php
                                    $payArr = array();
                                    $sub_tran_sql = "SELECT * FROM `bank_transaction_history` WHERE `bank_transaction_id` = ".$row['id'];
                                    $sub_tran_result = mysqli_query($conn,$sub_tran_sql);
                                    if(mysqli_num_rows($sub_tran_result) > 0){
                                       while($data = mysqli_fetch_assoc($sub_tran_result)){ 
                                 ?>
                                       <div class="row">
                                          <div class="form-group col-md-4">
                                             <input type="hidden" name="sub_tran_id[]" value='<?php echo $data['id']; ?>'>
                                             <input type="hidden" name="invoice_id[]" class="invoice_id" value="<?php echo $data['invoice_id']; ?>">
                                             <input type="text" class="form-control invoice_no" name="invoice_no[]" value="<?php echo $data['invoice_no']; ?>" readonly>
                                          </div>
                                          <div class="col-md-4 form-group">
                                             <input type="text" name="payment[]" value="<?php echo $data['payment']; ?>" class="form-control avl_bal invoice_payment" readonly>
                                          </div>
                                          <div class="col-md-2">
                                             <a href="javascript:void(0);" class="btn btn-danger remove_invoice_btn">-</a>
                                          </div>
                                       </div>
                                 <?php
                                          }
                                       }
                                 ?>
                              </div>
                              <input type="hidden" id="totla_invoice_payment" value="<?php if(isset($payArr) && !empty($payArr)){ echo array_sum($payArr); } ?>">
                           </div>
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" rows="3" cols="60" placeholder="Enter Remark"><?= $row['remark'];?></textarea>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="form-group">
                                 <button type="submit" name="Submit" id="submit" class="btn btn-primary waves ml-3">Submit</button>
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
      <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
      <script>
         var ext_prt = <?php echo $ext_prt_id; ?>;
         $(document).ready(function () {

            setTimeout(() => {
               getInvoiceNo(); 
            }, 4000);

            setTimeout(() => {
               if($("#table").val() == "Other Payout"){
                  $("#External-Party").show();
                  $("#pay_to").show();
                  $("#farmer_add").hide();
                  $(".calculate").hide();
                  GetExtParty();
                  // $(".InvoiceSection").hide();
               }else if($("#table").val() == "URD Kapas purchase Payment"){
                  GetFarmer();
                  $(".calculate").show();
                  $("#farmer_add").show();
                  $(".InvoiceSection").show();
               }else{
                  GetExtParty();
                  $(".calculate").hide();
                  $("#farmer_add").hide();
                  $("#pay_to").hide();
               }
            }, 400);
         
            $('#ext_party_id').change( function() {
               var ext_prt_id = $(this).val();
               var table = $("#table").val();
               if(table == null){
                  $( "#table_err" ).addClass("error").text("Please Select Table").show();
                  $('#ext_party_id').prop('selectedIndex',0);
                  return;
               }
               $("#totla_invoice_payment").val("0");
               $(".dynamicSection").html("");
               $("#invoice_no option").not(':first').remove();
               $("#invoice_no").selectpicker("refresh");
               getInvoiceNo();
            });

            // $('#table').change(function() {
            //    var table =  $(this).val();

            //    $("#pay_to").hide();
            //    $("#External-Party").show();
            //    $('.dynamicSection').html("");
               
            //    $("#ext_party_id option").not(':first').remove();
            //    $("#invoice_no option").not(':first').remove();
            //    $('#ext_party_id').prop('selectedIndex', 0);
            //    $('#invoice_no').prop('selectedIndex', 0);

            //       if(table != null){
            //          $("#table_err").removeClass("error").text('');
            //          if(table == "Other Payout"){
            //             $("#farmer_add").hide();
            //             $(".InvoiceSection").hide();
            //             $("#pay_to").show();
            //          }else if(table == "URD Kapas purchase Payment"){
            //             GetFarmer();
            //             $("#farmer_add").show();
            //             $(".InvoiceSection").show();
            //          }else{
            //             $("#farmer_add").hide();
            //             $(".InvoiceSection").show();
            //          }
            //       }

            //       if(table != "URD Kapas purchase Payment"){
            //          GetExtParty();  
            //       }
            // });  
            
            $("input[type=radio][name=pay_to]").change(function() {
               $("#ext_party_id option").not(':first').remove();
               $("#invoice_no option").not(':first').remove();
               $('#ext_party_id').prop('selectedIndex', 0);
               $('#invoice_no').prop('selectedIndex', 0);
               $("#invoice_no").selectpicker("refresh");
               $("#ext_party_id").selectpicker("refresh");
               GetExtParty();
            });
            
            $("#payment").on("change keyup keydown", function(){
               CheckAmount();
               $( "#payment_err" ).removeClass("error").text("");
            });

            $("#total_payment").on("keypress keyup", function(){
               // CheckPayment();
               CheckAmount();
            });
            
             $('#add_invoice').on('click', function() 
             {
                var selectedInvoic=$('#invoice_no :selected').val();

                if(selectedInvoic == "" || selectedInvoic == null){
                  alert("Please Select Invoice");
                  return;
                }
                
                var Invoice =  selectedInvoic.split("/");
                
                var payment = $("#payment").val();

                var totla_invoice_payment = $("#totla_invoice_payment").val();

               var total = parseInt(totla_invoice_payment) + parseInt(payment);

               $("#totla_invoice_payment").val(total);
                
                if(payment == "" || payment == 0){
                   $( "#payment_err" ).addClass("error").text("Please Enter Payment").show();
                   return;
                }

                if(Invoice[0] != '')
                {
                      $('.dynamicSection').append('<div class="row"><div class="form-group col-md-4"><input type="hidden" name="invoice_id[]" class="invoice_id" value="'+Invoice[0]+'"><input type="text" class="form-control invoice_no" name="invoice_no[]" value="'+Invoice[1]+'" readonly></div><div class="col-md-4 form-group"><input type="text" name="payment[]" value="'+payment+'" class="form-control avl_bal invoice_payment" readonly></div><div class="col-md-2"><a href="javascript:void(0);" class="btn btn-danger remove_invoice_btn">-</a></div></div>');
                      $("#invoice_no option[value='"+selectedInvoic+"']").remove();
                      $('#invoice_no').prop('selectedIndex',0);
                      $('#add_invoice').prop('disabled',true);
                      $("#invoice_no").selectpicker("refresh");
                      $("#payment").val("");
                      
                }
             });
             
             $('.dynamicSection').on('click', '.remove_invoice_btn', function(e){
                var invoice_id = $(this).parent().parent().find(".invoice_id").val();
                var invoice_no = $(this).parent().parent().find(".invoice_no").val();
                var invoice_payment = $(this).parent().parent().find(".invoice_payment").val();

               var totla_invoice_payment = $("#totla_invoice_payment").val();

               var total = parseInt(totla_invoice_payment) - parseInt(invoice_payment);
               $("#totla_invoice_payment").val(total);
                
                var option_data="<option  value='"+invoice_id+"/"+invoice_no+"'>"+invoice_no+"</option>";
                $(option_data).appendTo('#invoice_no');
                $("#invoice_no").selectpicker("refresh");
                $(this).parent('div').parent('div').remove();
             });

            $("#quantity").on("keypress keyup", function() {
               GetPayment();
            });

            $("#rate").on("keypress keyup", function() {
               GetPayment();
            });
         });

         function GetExtParty(){
            var FormData = $("form").serialize();
            FormData +="&getParty=true";
            $.ajax({
               type: "POST",
               url: 'getData.php',
               data: FormData,
               success: function(response)
               {
                     var jsonData = JSON.parse(response);
                     
                        $.each(jsonData,function(index,obj)
                        {
                           var data = obj.split("/")
                           var option_data="<option ";
                           if(data[0] == ext_prt){
                              option_data+=" selected=''";
                           }
                           option_data+=" value='"+data[0]+"'>"+data[1]+"</option>";
                           $(option_data).appendTo('#ext_party_id');
                           $("#ext_party_id").selectpicker("refresh");
                        });
                        ext_prt = '0';
               }
            });
         }

         function getInvoiceNo(){
            var ext_prt_id = $("#ext_party_id").val();
            var table = $("#table").val();

            $.ajax({
               type: "POST",
               url: 'getData.php',
               data: {
                  getInvoiceNo:true,
                  ext_prt_id:ext_prt_id,
                  table:table
               },
               success: function(response)
               {
                  var jsonData = JSON.parse(response);
                  
                     $.each(jsonData,function(index,obj)
                     {
                           var data = obj.split("/")
                           var option_data="<option  value='"+data[0]+"/"+data[1]+"'>"+data[1]+"</option>";
                           $(option_data).appendTo('#invoice_no'); 
                           $("#invoice_no").selectpicker("refresh");
                     });
               }
            });
         }


         // function CheckPayment(){
         //    setTimeout(function(){
         //       var balance = $("#bankbalance").val();
         //       var payment = $("#total_payment").val();
         //       if(parseInt(balance) < parseInt(payment)){
         //          $('#submit').prop('disabled',true); 
         //       }else{
         //          $('#submit').prop('disabled',false); 
         //       }
         //    },110);
         // }

         function CheckAmount(){
            setTimeout(function(){
               var payment =  $('#payment').val();
               
               if(payment != "" && payment != 0){
                  $('#add_invoice').prop('disabled',false);
               }else{
                  $('#add_invoice').prop('disabled',true);
               }
               var FormData = $("form").serialize();
               FormData +="&invoicepayment="+payment+"";
               FormData +="&Checkpayment=true";

               $.ajax({
                  type:"POST",
                  url:"getData.php",
                  data:FormData,
                  success:function(response){
                     var resData = JSON.parse(response);
                     if(resData.status == "1"){submit
                        $('#add_invoice').prop('disabled',false); 
                        $('#submit').prop('disabled',false); 
                     }else{
                        $('#add_invoice').prop('disabled',true); 
                        $('#submit').prop('disabled',true); 
                     }
                  }
               });
            },100);
         }

         function GetFarmer(){
               $("#External-Party").hide();
               $.ajax({
                  type:"POST",
                  url:"getData.php",
                  data:{
                     GetFarmer : true,
                  },
                  success:function(response){
                     var resData = JSON.parse(response);
                     console.log(resData);
                     if(resData.status == true){
                        $.each(resData,function(index,obj)
                        {
                           if(index != parseInt(0)){
                              if(obj.id != null){
                                 // var option_data="<option value='"+obj.id+"/"+obj.farmer_name+"'>"+obj.farmer_name+"</option>";
                                 if(obj.vlg_name != ""){
                                    var option_data = "<option value='" + obj.id + "/" + obj.farmer_name + "'>" + obj.farmer_name+" ("+obj.vlg_name+")" + "</option>";
                                 }else{
                                    var option_data = "<option value='" + obj.id + "/" + obj.farmer_name + "'>" + obj.farmer_name + "</option>";
                                 }
                                 $(option_data).appendTo('#invoice_no'); 
                                 $("#invoice_no").selectpicker("refresh");
                              }
                           }
                        });
                     }
                  }
               });
            }

            function GetPayment(){
               setTimeout(() => {
                  var quantity = $("#quantity").val();
                  var rate = $("#rate").val();
                  if(quantity != "" && rate != ""){
                     var payment = parseFloat(quantity) * parseFloat(rate);
                     $("#total_payment").val(payment);
                  }else{
                     $("#total_payment").val("");
                  }
               }, 400);
            }
      </script>
   </body>
</html>
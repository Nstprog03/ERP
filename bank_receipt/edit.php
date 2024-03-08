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
     
     $sql = "select * from bank_receipt where id=".$id;
     $result = mysqli_query($conn, $sql);
     if (mysqli_num_rows($result) > 0) {
       $row = mysqli_fetch_assoc($result);
        $ext_prt_id = $row['party'];
     }else {
       $errorMsg = 'Could not Find Any Record';
     }
   }
   
   
   
   ?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Bank Receipt Edit</title>
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
   </head>
   <body>
      <div class="wrapper">
         <div id="sidebarnav"></div>
         <!-- Page Content  -->
         <div id="content">
            <div id="topnav"></div>
            <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
               <div class="container-fluid">
                  <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Bank Receipt</span></a>
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

                  if(isset($table) && $table == "Sales Recievable"){
                     $prt_sql = "SELECT * FROM `sales_report` WHERE `firm` = '".$firm."' AND `financial_year_id` = '". $f_year."'";
                     $prt_result = mysqli_query($conn,$prt_sql);

                     if(mysqli_num_rows($prt_result) > 0){
                         while($rows = mysqli_fetch_assoc($prt_result)){
                             $ExrPrtArr[] = $rows['party_name'];
                         }
                     }
                 }elseif(isset($table) && $table == "Kapasiya Sales" || $table == "Other"){
                     $prt_sql="select * from external_party";
                     $prt_result = mysqli_query($conn, $prt_sql);
                     foreach ($conn->query($prt_sql) as $key => $rows) 
                     {
                        $ExrPrtArr[] = $rows['id'];
                     }
                 }

                  $ext_prt =  array_unique($ExrPrtArr);
                  ?>
            </div>

            <!-- last change on table END-->            
            <div class="container-fluid">
               <div class="row justify-content-center">
                  <div class="card">
                     <div class="card-header">
                        Edit Bank Receipt
                     </div>
                     <div class="card-body">
                        <form class="" action="update.php" method="post" enctype="multipart/form-data">
                           <input type="hidden" name="id" value="<?php echo $id; ?>">
                           <div class="row">
                              <div class="form-group col-md-4">
                                 <label for="date">Date</label>
                                 <input type="text" class="form-control" placeholder="Enter Date" name="date" autocomplete="off" value="<?php echo date("d/m/Y", strtotime($row['date']));  ?>" readonly>
                              </div>
                              <div class="form-group col-md-4">
                                 <label for="bank_ac_number">Select Bank</label>
                                 <?php
                                 $sql = "select * from party where id = ". $_SESSION["bank_transaction_firm_id"];
                                 $result = mysqli_query($conn, $sql);
                                 if(mysqli_num_rows($result) > 0){
                                    $firm_row = mysqli_fetch_assoc($result);
                                    $bank = json_decode($firm_row['bankDetails'],true);
                                       if(isset($bank)){                
                                          foreach ($bank as $result) 
                                          {
                                             if($row['bank'] == $result['bank_ac_number']){
                                                $bankData['ac_number'] = $result['bank_ac_number'];
                                                $bankData['bank_name'] = $result['bank_name'];
                                             }
                                          }
                                       }
                                    }
                                 ?> 
                                 <input type="text" class="form-control" placeholder="Bank Balance"  value="<?php if(isset($bankData['bank_name'])){ echo $bankData['bank_name']; } ?>" readonly>
                                 <input type="hidden" name="bank" value ="<?php if(isset($bankData['ac_number'])){ echo $bankData['ac_number']; } ?>">
                              </div>

                              <div class="form-group col-md-4">
                                 <label for="balance">Balance</label>
                                 <input type="text" class="form-control" placeholder="Bank Balance" name="bankbalance" id="bankbalance" value="<?php echo $row['balance'] ?>" readonly>
                              </div>

                              <div class="form-group col-sm-4">
                                 <label for="table">Select Table</label>
                                 <input type="text" class="form-control" placeholder="Table" name="table" id="table" value="<?php echo $row['table_indicator'] ?>" readonly>
                                 <!-- <select name="table" id="table" class="form-control">
                                    <option selected="" disabled="">Select Option</option>
                                    <option <?php if($row['table_indicator'] == "Sales Recievable"){ echo "selected"; } ?> value="Sales Recievable">Sales Recievable</option>
                                    <option <?php if($row['table_indicator'] == "Kapasiya Sales"){ echo "selected"; } ?> value="Kapasiya Sales">Kapasiya Sales</option>
                                 </select> -->
                                 <span id="table_err" style="color: red;font-size: 12px;"></span>
                              </div>
                            
                              <div class="form-group col-sm-4" id="External-Party">
                                 <label for="ext_prt">Select External Party</label>
                                 <a title='Add New External Party' class="btn btn-primary btn-sm float-right" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>                    
                                 <?php
                                    $sql = "select * from external_party";
                                    $result = mysqli_query($conn, $sql);
                                    ?>  
                                 <select name="ext_party" id="ext_party_id" class="form-control selectpicker" data-live-search="true">
                                    <option  disabled="">Select Option</option>
                                    <?php                   
                                       foreach ($conn->query($sql) as $result) 
                                       {
                                          if(in_array($result['id'],$ext_prt)){
                                       ?>
                                             <option <?php if($row['party'] == $result['id']){ echo "selected";} ?>  value='<?php echo $result['id']?>'> <?php echo $result['partyname']?></option>
                                    <?php
                                          }
                                       }
                                    ?>  
                                 </select>
                              </div>
                              <div class="form-group col-sm-4">
                                 <label for="payment">Payment</label>
                                 <input type="text" class="form-control" name ="total_payment"  placeholder="Enter Payment" id="total_payment" value="<?php echo $row['total_payment']; ?>">
                                 <span id="amount_err" style="color: red;font-size: 12px;"></span>
                              </div>
                           </div>
                           
                           <div class="InvoiceSection" style="<?php if($row['table_indicator'] != "Sales Recievable" ){ echo "display:none"; } ?>">
                              <div class="row">
                                 <div class="form-group col-sm-4">
                                    <label for="ext_prt">Select Invoice No.</label>
                                    <select id="invoice_no" class="form-control">
                                       <option selected="" disabled="">Select Option</option>
                                    </select>
                                 </div>
                                 <div class="form-group col-sm-4">
                                    <label for="payment">Payment</label>
                                    <input type="text" class="form-control"  placeholder="Enter Payment" value="" id="payment" autocomplete="off" >
                                    <span id="amount_er" style="color: red;font-size: 12px;"></span>
                                 </div>
                                 <div class="form-group col-md-1">
                                    <button type="button" class="btn btn-primary" style="margin-top:32px" disabled="" id="add_invoice">Add</button>
                                 </div>
                              </div>
                              <br/>
                              <div class="dynamicSection">
                                 <?php
                                    if($row['table_indicator'] == "Sales Recievable"){
                                       $select = "SELECT * FROM `bank_receipt_payment_data` WHERE `bank_receipt_id` = ".$row['id'];
                                       $sql_result = mysqli_query($conn,$select);
                                       if(mysqli_num_rows($sql_result) > 0){
                                          foreach($conn->query($select) as $data){
                                 ?>
                                       <div class="row">
                                          <div class="form-group col-md-4">
                                             <input type="hidden" name="receipt_id[]" value="<?php echo $data['id']; ?>">
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
                                    }
                                 ?>
                              </div>
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
         $(document).ready(function () {

            if(4("#table").val() != "Sales Recievable"){
               getInvoiceNo();
            }
            

            $('#ext_party_id').change( function() {
               var ext_prt_id = $(this).val();
               var table = $("#table").val();

               if(table == null){
                  $( "#table_err" ).addClass("error").text("Please Select Table").show();
                  $('#ext_party_id').prop('selectedIndex',0);
                  return;
               }

               $(".dynamicSection").html("");
               $("#invoice_no option").remove();
               var option_data="<option  value=''  selected='' disabled=''>Select Option</option>";
               $(option_data).appendTo('#invoice_no');
               
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
                              var option_data="<option  value='"+data[0]+"'>"+data[1]+"</option>";
                              $(option_data).appendTo('#invoice_no'); 
                        });
                  }
               });
            });
               
            $('#invoice_no').change( function() {
               var invoice = $(this).val();
               $('#add_invoice').prop('disabled',false);

               $.ajax({
                  type: "POST",
                  url: 'getData.php',
                  data: {
                     getInvoicePayment:true,
                     invoice:invoice
                  },
                  success: function(response)
                  {
                     var jsonData = JSON.parse(response);
                     if(jsonData.status == true){
                        $("#payment").val(jsonData.payment).trigger("change");
                     }else{
                        $("#payment").val(jsonData.payment).trigger("change");
                        $('#add_invoice').prop('disabled',true);
                     }
                  }
               });
            });

            
            $('#add_invoice').on('click', function() 
            {
                  var invoice_id=$('#invoice_no :selected').val();
                  var invoice=$('#invoice_no option:selected').html();
                                    
                  var payment = $("#payment").val();
      
               if(payment == "" || payment == 0){
                  $('#add_invoice').prop('disabled',true);
                  $( "#payment_err" ).addClass("error").text("Please Enter Payment").show();
                  return;
               }
                  
                  if(invoice_id != '')
                  {
                        $('.dynamicSection').append('<div class="row"><div class="form-group col-md-4"><input type="hidden" name="invoice_id[]" class="invoice_id" value="'+invoice_id+'"><input type="text" class="form-control invoice_no" name="invoice_no[]" value="'+invoice+'" readonly></div><div class="col-md-4 form-group"><input type="text" name="payment[]" value="'+payment+'" class="form-control avl_bal invoice_payment" readonly></div><div class="col-md-2"><a href="javascript:void(0);" class="btn btn-danger remove_invoice_btn">-</a></div></div>');
                        $("#invoice_no option[value='"+invoice_id+"']").remove();
                        $('#invoice_no').prop('selectedIndex',0);
                        $('#add_invoice').prop('disabled',true);
                        $("#payment").val("");
                  }
            });
               
            $('.dynamicSection').on('click', '.remove_invoice_btn', function(e){
               var invoice_id = $(this).parent().parent().find(".invoice_id").val();
               var invoice_no = $(this).parent().parent().find(".invoice_no").val();
               var invoice_payment = $(this).parent().parent().find(".invoice_payment").val();
               
               var option_data="<option  value='"+invoice_id+"'>"+invoice_no+"</option>";
               $(option_data).appendTo('#invoice_no');
               $(this).parent('div').parent('div').remove();
            });

            $("#payment").on("change keyup keydown", function(){
               CheckAmount();
            });

            $("#total_payment").on("keypress keyup", function(){
               CheckAmount();
            });
         });

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
                           var option_data="<option  value='"+data[0]+"'>"+data[1]+"</option>";
                           $(option_data).appendTo('#invoice_no'); 
                     });
               }
            });
         }

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


         

      </script>
   </body>
</html>
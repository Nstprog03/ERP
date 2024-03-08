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
//dd/mm/yyy
function convertDate2($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }
    return $final_date;
}
function convertDate($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('/', '-', $date);
    $final_date = date('Y-m-d', strtotime($final_date));
  }
    return $final_date;
}

  
   
  if(isset($_POST['ajaxcalc']))
  {
   
    if(isset($_POST['net_amt']) && isset($_POST['amt']))
    {
      $amtArr=$_POST['amt'];

      $gd_value=$_POST['goods_value'];

      $dbt_amt=$_POST['debit_amt'];

      $party_tds_amt=$_POST['party_tds_amt'];

      if($dbt_amt=='')
      {
        $dbt_amt=0;
      }

      if($party_tds_amt=='')
      {
        $party_tds_amt=0;
      }

      $netAmt=$gd_value-$dbt_amt;

      $netAmt=$netAmt-$party_tds_amt;

      $netAmt=round($netAmt);

      $main_net_amt=$netAmt;
      $main_net_amt = number_format(($main_net_amt),2,'.', '');


      //$netAmt=$_POST['net_amt'];


      foreach ($amtArr as  $value) 
      {
        if ($value == '') {
          $value  = 0;
        }
        $netAmt = number_format(($netAmt-$value),2,'.', '');
      }
      if(isset($_POST['b2b_amount']))
      {
        $b2b_total=array_sum($_POST['b2b_amount']);
         $netAmt = number_format(($netAmt-$b2b_total),2,'.', '');
      }

      $response['finalcal']=$netAmt;
      $response['net_amt']=$main_net_amt;


      echo json_encode($response); 
      exit;
    }

  }



  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from rd_kapas_payment where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['submit'])){
    $goods_value = $_POST['goods_value'];
    $debit_amt = $_POST['debit_amt'];
    $party_tds_amt = $_POST['party_tds_amt'];
    $net_amt = $_POST['net_amt'];
    $pay_amt = $_POST['pay_amt'];

    $user= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    $dynamic_field=array();

    $label=$_POST['lable'];
    $amt=$_POST['amt'];
    $date=$_POST['dyn_date'];

    foreach ($label as $key => $value) {
        $final_date = '';     
        if($date[$key]!='')
        {
          $final_date = str_replace('/', '-', $date[$key]);
          $final_date = date('Y-m-d', strtotime($final_date));
        }
        $dynamic_field[$key]['lable'] = $label[$key];
        $dynamic_field[$key]['amt'] = $amt[$key];
        $dynamic_field[$key]['date'] = $final_date;
     }

     $dynamic_field= json_encode($dynamic_field);

     //bill 2 bill payment dynamic data
   $b2bArr=array();
   if(isset($_POST['b2b_id']))
   {
    foreach ($_POST['b2b_id'] as $key => $id) 
    {
      $b2bArr[$key]['b2b_id']=$id;
      $b2bArr[$key]['b2b_label']=$_POST['b2b_label'][$key];
      $b2bArr[$key]['b2b_amount']=$_POST['b2b_amount'][$key];
      $b2bArr[$key]['b2b_date']=convertDate($_POST['b2b_date'][$key]);
    }
   }
   $b2bArr= json_encode($b2bArr);
  
  
    if(!isset($errorMsg)){
    $sql = "update rd_kapas_payment
                  set 
                    goods_value = '".$goods_value."',
                    debit_amt = '".$debit_amt."',  
                    party_tds_amt = '".$party_tds_amt."',                   
                    net_amt = '".$net_amt."',
                    pay_amt = '".$pay_amt."',
                    username = '".$user."',
                    dynamic_field = '".$dynamic_field."',
                     bill2bill_dynamic_data = '".$b2bArr."',
                    updated_at = '".$timestamp."'

          where id=".$_GET['id'];
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");
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
    <title>RD Kapas Purchase Payment Edit</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit RD Kapas Purchase Payment</span></a>
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
          $sqlLastChange="select username,updated_at from rd_kapas_payment where id='".$row['id']."'";

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
              <div class="card-header">RD Kapas Purchase Payment Database</div>
              <div class="card-body">
                

                
                    <form id="main_form" class="" action="" method="post" enctype="multipart/form-data"> 
                      <div class="row">

                          <?php 
                      $sql_ext="select partyname from external_party where id='".$row['party']."'";
                      $result_ext = mysqli_query($conn, $sql_ext);
                      $row_ext=mysqli_fetch_assoc($result_ext);
                      ?>

                      <div class="form-group col-md-4">
                        <label for="pur_party">Party</label>
                        <input type="text" name="pur_party" class="form-control" value="<?php echo $row_ext['partyname']; ?>" readonly>
                      </div>
                      <div class="form-group col-md-4">
                        <label for="pur_invoice_no">Invoice No</label>
                        <input type="text" class="form-control" name="pur_invoice_no" value="<?php echo $row['invoice_no']; ?>" readonly>
                      </div>

                      <?php 
                      $sql_firm="select party_name from party where id='".$row['firm_id']."'";
                      $result_firm = mysqli_query($conn, $sql_firm);
                      $rowfirm=mysqli_fetch_assoc($result_firm);
                     
                      ?>



                      <div class="form-group col-md-4">
                        <label for="firm">Firm</label>
                        <input type="text" class="form-control"  value="<?php echo $rowfirm['party_name']; ?>" readonly>
                      </div>
                    </div>
                    <div class="row">

                        <?php
                        $report_date='';
                        if($row['report_date']!='' && $row['report_date']!='0000-00-00')
                        {
                         $report_date = date("d/m/Y", strtotime($row['report_date']));
                        }
                      ?>

                      <div class="form-group col-md-4">
                        <label for="report_date">Report Date</label>
                        <input type="text" class="form-control" name="report_date" value="<?php echo $report_date; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="tax_amt">Tax Amount</label>
                        <input type="text" class="form-control" name="tax_amt" value="<?php echo $row['tax_amt']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="tcs_amt">TCS Amount</label>
                        <input type="text" class="form-control" name="tcs_amt" value="<?php echo $row['tcs_amt']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="goods_value">Goods value</label>
                        <input type="text" class="form-control" name="goods_value" value="<?php echo $row['goods_value']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="debit_amt">Debit Amount</label>
                        <input type="text" class="form-control" id="debit_amt" name="debit_amt" value="<?php echo $row['debit_amt']; ?>" onkeypress="return NumericValidate(event,this)" placeholder="Enter Debit Amount">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="party_tds_amt">Party TDS Amount</label>
                        <input type="text" class="form-control" id="party_tds_amt" name="party_tds_amt" value="<?php echo $row['party_tds_amt']; ?>" onkeypress="return NumericValidate(event,this)" placeholder="Enter Party TDS Amount">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="net_amt">Net Amount</label>
                        <input type="text" class="form-control" id="net_amt" name="net_amt" value="<?php echo $row['net_amt']; ?>" readonly>
                      </div>

                    </div>

                
                          

                    <?php

                      if ($row['dynamic_field'] != '' ) {
                          
                          $dynamic_fieldArr = json_decode($row['dynamic_field']);?>
                          <div class="add_dyamic">
                          <?php foreach ($dynamic_fieldArr as $key => $value) {

                            ?>
                      

                        <div class="row">


                        <?php if ($key == 0) {?>
                                                  <div class="form-group col-md-3">
                          <label for="lable">Label</label>
                          <input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name" value="<?php echo $value->lable; ?>">
                        </div>

                        <div class="form-group col-md-3">
                          <label for="amt">Amount</label>
                          <input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" value="<?php echo $value->amt; ?>" name="amt[]" placeholder="Enter Amount">
                        </div>

                        <?php 
                        $GetDate = '';
                        if ($value->date != '') {
                          $GetDate = date("d/m/Y", strtotime($value->date)); 
                        } ?>
                        <div class="form-group col-md-3">
                          <label for="dyn_date">Date</label>
                          <input type="text" class="form-control datepicker" id="date" name="dyn_date[]" placeholder="Enter Date" value="<?php echo $GetDate; ?>">
                        </div>
                          <div class="form-group col-md-3" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary add_button"> +</button>
                        </div>  
                        <?php }else{?>

                          <script type="text/javascript">
                             $("#date<?php echo $key; ?>").datepicker({dateFormat:'dd/mm/yy',
                                dateFormat:'dd/mm/yy',
                                changeMonth: true,
                                changeYear: true,
                              });
                          </script>
                          <div class="form-group col-md-3">
                            <label for="lable">Label</label>
                            <input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name" value="<?php echo $value->lable; ?>">
                          </div>

                          <div class="form-group col-md-3">
                            <label for="amt">Amount</label>
                            <input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" name="amt[]" value="<?php echo $value->amt; ?>" placeholder="Enter Amount">
                          </div>

                          <?php 
                            $GetDate = '';
                            if ($value->date != '') {
                              $GetDate = date("d/m/Y", strtotime($value->date)); 
                            }
                          ?>

                          <div class="form-group col-md-3">
                            <label for="net_amt">Date</label>
                            <input type="text" class="form-control datepicker" id="date<?php echo $key; ?>" name="dyn_date[]" placeholder="Enter Date" value="<?php  echo $GetDate; ?>">
                          </div>
                              <div class="form-group col-md-3" style="margin-top: 30px;"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div>

                        <?php } ?>
                        

                      </div>
                     
                          <?php }?>
                        </div>
                      <?php }



                     ?>

                      
                       <div class="row">
                  
                      <div class="form-group col-md-4">
                        <label for="b2bSelect"><b>Bill 2 Bill Payment</b></label>
                        <select class="form-control b2bSelect" id="b2bSelect">
                          <option disabled="" value="" selected="">Select Option</option>
                        </select>                
                      </div>
                      <div class="col-md-4 " >
                          <button type="button" style="margin-top: 32px;" class="btn btn-primary b2baddBtn" disabled="">Add</button>
                      </div>
                            

                  </div>


                   <div class="b2b_dyamic">
                    <?php
                      $BillArr=json_decode($row['bill2bill_dynamic_data'],true);
                      if(isset($BillArr))
                      {
                         foreach ($BillArr as $key => $item) 
                         {
                           if($key==0)
                           {
                          ?>

                            <div class="row b2bRow">
                              <div class="form-group col-md-3">
                                <label for="lable">Label</label>
                                <input type="text" class="form-control b2b_label"  name="b2b_label[]" value="<?php echo $item['b2b_label'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="b2b_amount">Amount</label>
                                <input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="<?php echo $item['b2b_amount'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="b2b_date">Date</label>
                                <input type="text" class="form-control b2b_date" name="b2b_date[]" value="<?php echo convertDate2($item['b2b_date']) ?>" readonly>
                              </div>
                              
                              <div class="form-group col-md-3" style="margin-top: 30px;">
                                <a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a>
                              </div>
                                <input type="hidden" name="b2b_id[]" value="<?php echo $item['b2b_id'] ?>" class="b2b_id"/>
                            </div>

                          <?php
                           }
                           else
                           {
                          ?>

                          <div class="row b2bRow">
                              <div class="form-group col-md-3">
                                <input type="text" class="form-control b2b_label"  name="b2b_label[]" value="<?php echo $item['b2b_label'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="<?php echo $item['b2b_amount'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-3">
                                <input type="text" class="form-control b2b_date" name="b2b_date[]" value="<?php echo convertDate2($item['b2b_date']) ?>" readonly>
                              </div>
                              
                              <div class="form-group col-md-3">
                                <a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a>
                              </div>
                                <input type="hidden" name="b2b_id[]" value="<?php echo $item['b2b_id'] ?>" class="b2b_id"/>
                            </div>
                          <?php
                           }
                         }
                      }
                    ?>


                    </div>
                    <br>

                    <hr>
                      <div class="row"></div>
                        
                        <div class="form-group">
                          <label for="pay_amt">Amount to be pay</label>
                          <input type="text" class="form-control " id="pay_amt" name="pay_amt" placeholder="Amount to be pay" value="<?php echo $row['pay_amt']; ?>" readonly="">
                        </div>


                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                      </div>
                      </form>
                  
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

    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
         
    <script>
      var bill2billDataArr='';

    function changeParty() {
      $('select[name=pur_invoice_no] option').hide();
      $('select[name=pur_invoice_no] option[data-party='+$("select[name=pur_party] option:selected").attr("data-value")+']').show();
    }
    $(document).ready(function() {

        $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
            });

        $('#b2bSelect').on('change', function() {
                 $('.b2baddBtn').attr("disabled",false);
            });


        $(".datepicker").keydown(false);

        var add_dyamic = $('.add_dyamic');
        var i = $("input[name='lable[]'").length;
        $('.add_button').click(function(){
          
          i = parseInt(i)+1;
          var Amount_pay =  $('#pay_amt').val();
          if (Amount_pay === '0.00') {

              alert('Sorry You Can Not Add New Fileds Beacuse Amount To Be Pay Is Zero')

          }else{

            var addFileds = '<div class="row"><div class="form-group col-md-3"><label for="lable">Label</label><input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name"></div><div class="form-group col-md-3"><label for="amt">Amount</label><input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" name="amt[]" placeholder="Enter Amount"></div><div class="form-group col-md-3"><label for="net_amt">Date</label><input type="text" class="form-control" id="date'+i+'" name="dyn_date[]" placeholder="Enter Date"></div><div class="form-group col-md-3" style="margin-top: 30px;"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div></div>';
              $(add_dyamic).append(addFileds);
              $("#date"+i).datepicker({dateFormat:'dd/mm/yy',
                dateFormat:'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
              }).datepicker("setDate", new Date());
              $("#date"+i).keydown(false);
              Amout_pay(); 
          }
        });

      
       
        $(add_dyamic).on('click', '.remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove();
            Amout_pay(); 

        });



        //bill 2 bill dynamic section
         $('.b2baddBtn').click(function(){

            var selectedB2b=$('#b2bSelect :selected').val();



            if(selectedB2b!='')
            {
              var count = $('.b2bRow').length;

              var data=bill2billDataArr.find(item => item.id === selectedB2b);
              
             


              var date=changeDateFormat(data.date);

              if(count==0)
              {
                var addFileds = '<div class="row b2bRow"><div class="form-group col-md-3"><label for="lable">Label</label><input type="text" class="form-control b2b_label"  name="b2b_label[]" value="'+data.label+'" readonly></div><div class="form-group col-md-3"><label for="b2b_amount">Amount</label><input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="'+data.payment+'" readonly></div><div class="form-group col-md-3"><label for="b2b_date">Date</label><input type="text" class="form-control b2b_date" name="b2b_date[]"value="'+date+'" readonly></div><div class="form-group col-md-3" style="margin-top: 30px;"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div><input type="hidden" name="b2b_id[]" value="'+data.id+'" class="b2b_id"/></div>';
              }
              else
              {
                var addFileds = '<div class="row b2bRow"><div class="form-group col-md-3"><input type="text" class="form-control b2b_label"  name="b2b_label[]" value="'+data.label+'" readonly></div><div class="form-group col-md-3"><input type="text" class="form-control b2b_amount" name="b2b_amount[]" value="'+data.payment+'" readonly></div><div class="form-group col-md-3"><input type="text" class="form-control b2b_date" name="b2b_date[]"value="'+date+'" readonly></div><div class="form-group col-md-3"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div><input type="hidden" name="b2b_id[]" value="'+data.id+'" class="b2b_id"/></div>';
              }
  
              
                $('.b2b_dyamic').append(addFileds);
                getB2bData(); 
                Amout_pay();
            }
        });


        $('.b2b_dyamic').on('click', '.remove_btn', function(e){
              e.preventDefault();
           $(this).parent('div').parent('div').remove(); 
           Amout_pay();
           getB2bData(); 

        });





         var timer = null;
       $('#debit_amt').on('input', function() 
       {

          clearTimeout(timer); 
          timer = setTimeout(AjxCal, 1000)

        });

        $('#party_tds_amt').on('input', function() 
       {

          clearTimeout(timer); 
          timer = setTimeout(AjxCal, 1000)

        });


        
    });

    var net_amt = $('#net_amt').val();
    
    var timer = null;
    function Amout_pay(e) 
    {
      clearTimeout(timer); 
      timer = setTimeout(AjxCal, 1000)

    }

    function AjxCal() {
      var formdata = $('#main_form').serialize();
        formdata += "&ajaxcalc=1";
         $.ajax({
          data : formdata,
          method : 'post',
          dataType : "json",
          success: function(result){
            $("#pay_amt").val(result.finalcal);
             $("#net_amt").val(result.net_amt);
            console.log(result);
          }
        });
    }
  
getB2bData(); 
  //get bill 2 bill data
function getB2bData()
{
    var report_id = "<?php echo $row['rd_kapas_report_id'] ?>";


    var alreadyUsed=[];
    $('.b2b_id').each(function(index){
      alreadyUsed[index]=this.value;
    });

    if(report_id!='')
    {
        $.ajax({
            type: "POST",
            url: 'getInvoice.php',
            data: {
              report_id:report_id,
              getB2Bdata:true
            },
            success: function(response)
            {

              console.log(response)
              var jsonData = JSON.parse(response);

              bill2billDataArr=jsonData;
                
              $('#b2bSelect').find('option').not(':first').remove();
              $.each(jsonData, function(index,item) 
              {

                if(!alreadyUsed.includes(item.id))
                {
                  var date=changeDateFormat(item.date);                   
                  $('#b2bSelect').append($("<option />").val(item.id).text(item.label+' - Rs.'+item.payment+' ('+date+')'));
                }
                
              }); 
              $('#b2bSelect').val(''); 

           }
        });
    }
}



function changeDateFormat(inputDate){  // dd/mm/yyyy
    var splitDate = inputDate.split('-');
    if(splitDate.count == 0){
        return null;
    }

    var year = splitDate[0];
    var month = splitDate[1];
    var day = splitDate[2]; 

    return day + '/' + month + '/' + year;
}



   
    function NumericValidate(evt, element) {

     var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
        return false;
      else 
      {
        var len = $(element).val().length;
        var index = $(element).val().indexOf('.');
        if (index > 0 && charCode == 46) 
        {
          return false;
        }
        if (index > 0)
        {
          var CharAfterdot = (len + 1) - index;
          if (CharAfterdot > 3)
          {
            return false;
          }
        }
      }



  return true;       
}  
    </script>
  </body>
</html>

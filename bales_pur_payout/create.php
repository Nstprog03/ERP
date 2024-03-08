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

  if(isset($_POST['ajaxcalc']))
  {
   
    if(isset($_POST['net_amt']) && isset($_POST['amt']) && isset($_POST['ad_hoc']))
    {
      $amtArr=$_POST['amt'];
      $netAmt=$_POST['net_amt'];
      
      //$ad_hoc=$_POST['ad_hoc'];
      //$netAmt=$netAmt-$ad_hoc;

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

      echo json_encode(array("finalcal"=>$netAmt)); 
      exit;
    }

  }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bales Payout Database Create</title>


    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

        <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
   <link rel="stylesheet" href="../../style4.css">
    <link rel="stylesheet" href="../../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
     <script> 
    $(function(){
      $("#sidebarnav").load("../../nav.html"); 
      $("#topnav").load("../nav2.html"); 

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Bales Purchase Payout Database</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>


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
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">Bales Purchase Payout Database</div>
              <div class="card-body">
                <form class="" id="pur_payout_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                     
                      <?php

                            $getFinancialYear=$_SESSION['pur_financial_year'];
                             $getFinancialYearID=$_SESSION['pur_financial_year_id'];
                             $firm_id= $_SESSION["pur_firm_id"];
                            
                            $sql2 = "select DISTINCT party from debit_report where firm='".$firm_id."' AND financial_year='".$getFinancialYearID."'";
                            $result2 = mysqli_query($conn, $sql2);
                            ?>
                            <div class="form-group">
                              <label for="pur_party">Select Party</label>
                             

                           <select name="pur_party" data-live-search="true" class="form-control searchDropdown" id="changeparty">
                           <option value="" disabled selected>Choose option</option>
                            <?php                   
                              foreach ($conn->query($sql2) as $result2) 
                              {
                                $party_sql="SELECT * FROM external_party WHERE id='".$result2['party']."'";
                                        $party_result = mysqli_query($conn, $party_sql);
                                          $party_row = $party_result->fetch_assoc();

                                    echo "<option data-value='".str_replace(' ','',$party_row['id'])."' value='" .$result2['party']. "'>" .$party_row['partyname']. "</option>";
                              }
                            ?>                              
                            </select>
                      </div>                      
                            <div class="form-group">
                              <label for="pur_invoice_no">Select Invoice No</label>
                           <select name="pur_invoice_no" id="pur_invoice_no" class="form-control">
                           <option value="" disabled selected>Choose option</option>
                                                  
                            </select>
                      </div>
                    <div class="form-group">
                      <button type="submit" id="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>

                <?php
                if(isset($_POST['Submit'])){
                  
                  if(!empty($_POST['pur_party']) && !empty($_POST['pur_invoice_no'])) {
                    $dah_pur_invoice_no = $_POST['pur_invoice_no'];
                    $dah_pur_party = $_POST['pur_party'];

                    $sql3 = 'select * from pur_report where party="'.$dah_pur_party.'" AND invoice_no ="'.$dah_pur_invoice_no.'"';
                    
                    $debit_report = 'select * from debit_report where party="'.$dah_pur_party.'" AND invoice_no ="'.$dah_pur_invoice_no.'"';
                    $debit_report1 = mysqli_query($conn, $debit_report);

                    $sql4 = 'select * from pur_pay where party="'.$dah_pur_party.'" AND invice_no="'.$dah_pur_invoice_no.'"';
                    $result3 = mysqli_query($conn, $sql3);
                    if (mysqli_num_rows($result3) > 0) {
                      foreach ($conn->query($sql3) as $result3) 
                      {
                    ?>
                    <hr>
                    <form id="main_form" class="" action="add.php" method="post" enctype="multipart/form-data">
                    <div class="row"> 
                      <div class="form-group col-md-6">
                        <label for="pur_party"> Party</label>
                        <?php 

                          $party_sql1="SELECT * FROM external_party WHERE id='".$dah_pur_party."'";
                          $party_result1 = mysqli_query($conn, $party_sql1);
                          $party_row1 = $party_result1->fetch_assoc();

                          ?>
                        <input type="text"  class="form-control" id="pur_party" readonly="" value="<?php 

                        echo $party_row1['partyname']; ?>" >

                        <input type="hidden" name="pur_party" value="<?php echo $party_row1['id'] ?>">
                      </div>
                      <input type="hidden" name="pur_report_id" value="<?php echo $result3['id']; ?>">

                      

                      <div class="form-group col-md-6">
                        <label for="pur_invoice_no"> Invoice No</label>
                        <input type="text" name="pur_invoice_no" value="<?php echo $dah_pur_invoice_no; ?>" class="form-control" readonly>
                      </div>
                    </div>
                      <?php 
                      $sql4 = 'select * from pur_pay where party="'.$dah_pur_party.'" AND invoice_no="'.$dah_pur_invoice_no.'"';
                      $result4 = mysqli_query($conn, $sql4);
                      if (mysqli_num_rows($result4) > 0) { 
                        foreach ($conn->query($sql4) as $result4){
                        ?>
                        <div class="form-group">
                          <h3>You have already created payout entry. Please go to edit page: <a href="edit.php?id=<?php echo $result4['id'] ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a></h3>
                        </div>
                      <?php
                        } 
                      }
                      else{

                      ?>
                      <div class="row">
                        <div class="form-group col-md-2">
                              <label for="gross_amt">Gross Amount</label>
                              <input type="text" id="gross_amt" name="gross_amt" class="form-control" readonly="" value="<?php echo $result3['grs_amt'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label for="tax_amt">Tax Amount</label>
                              <input type="text" id="tax_amt" name="tax_amt" class="form-control" readonly="" value="<?php echo $result3['txn_amount'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label for="tcs_amt">TCS Amount</label>
                              <input type="text" id="tcs_amt" name="tcs_amt" class="form-control" readonly="" value="<?php echo $result3['tcs_amount'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label for="other_amt">Other Amount</label>
                              <input type="text" id="other_amt" name="other_amt" class="form-control" readonly="" value="<?php echo $result3['other_amt'] ?>">
                            </div>
                        
                            <div class="form-group col-md-4">
                              <label for="invoice_amt">Invoice Amount</label>
                              <input type="text" id="invoice_amt" name="invoice_amt" class="form-control" readonly="" value="<?php echo $result3['netpayableamt'] ?>">
                            </div>




                            <?php
                              if (mysqli_num_rows($debit_report1) > 0) 
                              { 
                                  foreach ($conn->query($debit_report) as $debit_report1)
                                  { 

                                    $debit_date='';
                                    if($debit_report1['debit_date']!='' && $debit_report1['debit_date']!='0000-00-00')
                                    {
                                     $debit_date = date("d/m/Y", strtotime($debit_report1['debit_date']));
                                    }

                                    ?>




                                    <div class="form-group col-md-4">
                                      <label for="final_debit_amount">Final Debit Amount With Tax</label>
                                      <input type="text" name="final_debit_amount" class="form-control" id="final_debit_amount" readonly="" value="<?php echo $debit_report1['final_debit_amount']; ?>">
                                    </div>

                                     <div class="form-group col-md-4">
                                      <label for="debit_report_date">Debit Report Date</label>
                                      <input type="text" name="debit_report_date" class="form-control"  value="<?php echo $debit_date; ?>" readonly="">
                                    </div>

                                     <div class="form-group col-md-4">
                                      <label for="tds_amount">TDS Amount</label>
                                      <input type="text" name="tds_amount" id="tds_amount" class="form-control"  value="<?php echo $debit_report1['tds_amount']; ?>" readonly="">
                                    </div>



                                    <input type="hidden" id="debit_report_id" name="debit_report_id" value="<?php echo $debit_report1['id']; ?>">



                                    <?php 
                                  }
                              }else{?>
                                  <div class="form-group col-md-4">
                                      <label for="final_debit_amount">Final Debit Amount With Tax</label>
                                      <input type="text" name="final_debit_amount" class="form-control" value="" readonly="">
                                    </div>

                                    <div class="form-group col-md-4">
                                      <label for="debit_report_date">Debit Report Date</label>
                                      <input type="text" name="debit_report_date" class="form-control" value="" readonly="">
                                    </div>

                                     <div class="form-group col-md-4">
                                      <label for="tds_amount">TDS Amount</label>
                                      <input type="text" name="tds_amount" id="tds_amount" class="form-control"  value="" readonly="">
                                    </div>

                             <?php  } ?>

                             
                    
                    <div class="col-md-4">
                          
                          <div class="form-group">
                          <label for="ad_hoc">Ad-Hoc </label>
                          <input type="text" class="form-control" name="ad_hoc" placeholder="Enter Ad-Hoc " value="<?php echo $debit_report1['ad_hoc'] ?>" id="ad_hoc"  readonly="">
                        </div>
                   
                    </div>

                    <div class="col-md-4">
                          
                          <div class="form-group">
                          <label for="ad_hoc_date">Ad-Hoc Payment Date </label>
                          <input type="text" class="form-control" name="ad_hoc_date" placeholder="Enter Ad-Hoc Payment Date" id="ad_hoc_date" value="<?php echo date("d/m/Y", strtotime($debit_report1['ad_hoc_date'])); ?>" readonly="">
                        </div>
                   
                    </div>

                     <div class="form-group col-md-4">
                                <label for="net_amt">Net Amount</label>
                                <input type="text" class="form-control" id="net_amt" name="net_amt" readonly>
                              </div>

                  </div>

                  


                  
                       <div class="add_dyamic">

                        <div class="row">

                        <div class="form-group col-md-3">
                          <label for="lable">Label</label>
                          <input type="text" class="form-control" id="lable" name="lable[]" placeholder="Enter Label Name">
                        </div>

                        <div class="form-group col-md-3">
                          <label for="amt">Amount</label>
                          <input type="text" class="form-control amt" onkeyup="Amout_pay(this)"  onkeypress="return NumericValidate(event,this)" id="amt" name="amt[]" placeholder="Enter Amount">
                        </div>


                        <div class="form-group col-md-3">
                          <label for="net_amt">Date</label>
                          <input type="text" class="form-control datepicker" id="date" name="dyn_date[]" placeholder="Enter Date">
                        </div>

                        <div class="form-group col-md-3" style="margin-top: 30px;">
                          <button type="button" class=" btn btn-primary add_button"> +</button>
                        </div>

                      </div>
                      </div>
                      <hr>

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

                    </div>
                    <br>

                    <hr>



                      <div class="row"></div>
                        
                        <div class="form-group">
                          <label for="pay_amt">Amount to be pay</label>
                          <input type="text" class="form-control bold" id="pay_amt" name="pay_amt" placeholder="Amount to be pay" readonly="">
                        </div>
   


                   
                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                      </div>



                      <script type="text/javascript">
                        

                      </script>



                    </form>
                   <?php 
                      }
                    }
                    }else{
                      echo "No data Found!";
                    }
                  } else {
                    echo 'Please select the value.';
                  }
                  
                }
              ?>
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

     <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script>


      var bill2billDataArr='';

       var invoice_amt = $('#invoice_amt').val();
      var ad_hoc = $('#ad_hoc').val();
      var final_debit_amount = $('#final_debit_amount').val();
      var tds_amount = $('#tds_amount').val();

      if(invoice_amt=='')
      {
        invoice_amt=0;
      }
      if(ad_hoc=='')
      {
        ad_hoc=0;
      }
      if(final_debit_amount=='')
      {
        final_debit_amount=0;
      }
      if(tds_amount=='')
      {
        tds_amount=0;
      }

      var net_amt = parseFloat(invoice_amt)-parseFloat(final_debit_amount)-parseFloat(ad_hoc)-parseFloat(tds_amount);

      net_amt= Math.round(net_amt);

      $('#net_amt').val(net_amt.toFixed(2));

    
      //var pay_amt = parseFloat(invoice_amt)-parseFloat(ad_hoc);

      $('#pay_amt').val(net_amt.toFixed(2));


    function changeParty() {
      $('select[name=pur_invoice_no] option').hide();
      $('select[name=pur_invoice_no] option[data-party='+$("select[name=pur_party] option:selected").attr("data-value")+']').show();
    }

   



    $(document).ready(function() {

         $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
            })
           .datepicker("setDate", new Date());
           $(".datepicker").keydown(false);


            $('#b2bSelect').on('change', function() {
                 $('.b2baddBtn').attr("disabled",false);
            });



        var add_dyamic = $('.add_dyamic');
        var i = 0; 
        $('.add_button').click(function(){
          
          i = parseInt(i)+1;
          var Amount_pay =  $('#pay_amt').val();
          if (parseInt(Amount_pay) === 0) {

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

      
      

        $('#changeparty').on('change', function() {

        var value=this.value;
        var $dropdown = $("#pur_invoice_no");
        
        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {party:value},
            success: function(response)
            {

              console.log(response)

                var jsonData = JSON.parse(response);
                
                $dropdown.find('option').not(':first').remove();
                $.each(jsonData, function(index,value) {
                       
                $dropdown.append($("<option />").val(value).text(value));
                });

                 
           }
        });


        
      });


    });


  getB2bData(); 
  //get bill 2 bill data
function getB2bData()
{
    var report_id = $('#debit_report_id').val();


    var alreadyUsed=[];
    $('.b2b_id').each(function(index){
      alreadyUsed[index]=this.value;
    });


    if(report_id!='' && report_id!=undefined)
    {
        $.ajax({
            type: "POST",
            url: 'getData.php',
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





    var net_amt = $('#net_amt').val();
    
     var timer = null;
    function Amout_pay(e) {
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
          }
        });
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


    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>



  </body>
</html>

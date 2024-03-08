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

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RD Kapas Purchase Payment Create</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New RD Kapas Purchase Payment</span></a>
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
              <div class="card-header">RD Kapas Purchase Payment</div>
              <div class="card-body">
                <form class="" id="pur_payout_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                     

                      <?php
                            $sql = "select distinct(external_party) from rd_kapas_report where firm='".$_SESSION['pur_firm_id']."' AND financial_year_id='".$_SESSION['pur_financial_year_id']."'";
                            $result = mysqli_query($conn, $sql);
                            ?>
                            <div class="row">
                            <div class="form-group col-md-6">
                              <label for="pur_party">Select Party</label>
                           <select id="epartySelect" name="pur_party" data-live-search="true" class="form-control searchDropdown">
                           <option value="" disabled selected>Choose option</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {

                                $sql_ext="select id,partyname from external_party where id='".$result['external_party']."'";
                                $result_ext= mysqli_query($conn, $sql_ext);
                                $row_ext=mysqli_fetch_assoc($result_ext);

                                echo "<option value='" .$row_ext['id']. "'>" .$row_ext['partyname']. "</option>";
                              }
                            ?>                              
                            </select>
                      </div>                      
                      <div class="form-group col-md-6">
                              <label for="pur_invoice_no">Select Invoice No</label>
                           <select id="pur_invoice_no" name="pur_invoice_no" class="form-control">
                           <option value="" disabled selected>Choose option</option>
                                                     
                            </select>
                      </div>

                      <input type="hidden" id="report_record_id" name="report_record_id" value="">

                    </div>
                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>

                <?php
                if(isset($_POST['Submit']))
                {
                  
                  if(!empty($_POST['pur_party']) && !empty($_POST['pur_invoice_no'])) 
                  {
                   
                    $report_record_id=$_POST['report_record_id'];

                    $sql3 = "select * from rd_kapas_report where id='".$report_record_id."'";

                    $result3 = mysqli_query($conn, $sql3);
                    if (mysqli_num_rows($result3) > 0) {
                      foreach ($conn->query($sql3) as $result3) 
                      {
                    ?>
                    <hr>
                    <form id="main_form" class="" action="add.php" method="post" enctype="multipart/form-data"> 
                      <div class="row">

                        <input type="hidden" id="rd_kapas_report_id" name="rd_kapas_report_id" value="<?php echo $report_record_id; ?>">

                        <?php
                        $sql_ext_party = "select * from external_party where id='".$_POST['pur_party']."'";
                        $result_ext_party = mysqli_query($conn, $sql_ext_party);
                        $row_ext_party=mysqli_fetch_array($result_ext_party);
                        ?> 

                      <div class="form-group col-md-4">
                        <label for="pur_party">Party</label>
                        <input type="text"  class="form-control" value="<?php echo $row_ext_party['partyname']; ?>" readonly>
                        <input type="hidden" name="pur_party" value="<?php echo $_POST['pur_party']; ?>">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="pur_invoice_no">Invoice No</label>
                        <input type="text" class="form-control" name="pur_invoice_no" value="<?php echo $_POST['pur_invoice_no']; ?>" readonly>
                      </div>
                    </div>


                      <?php 
                      $sql4 = "select * from rd_kapas_payment where rd_kapas_report_id='".$report_record_id."'";
                      $result4 = mysqli_query($conn, $sql4);
                      if (mysqli_num_rows($result4) > 0) { 
                        foreach ($conn->query($sql4) as $result4)
                        {
                        ?>
                        <div class="form-group">
                          <h3>You have already created entry for this invoice. Please go to edit page: <a href="edit.php?id=<?php echo $result4['id'] ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a></h3>
                        </div>
                      <?php
                        } 
                      }
                      else
                      {

                      ?>


                      <div class="row">

                      <div class="form-group col-md-4">
                        <label for="firm">Firm</label>
                        <input type="text" class="form-control"  value="<?php echo $_SESSION['pur_firm']; ?>" readonly>
                        <input type="hidden" name="firm_id"  value="<?php echo $_SESSION['pur_firm_id']; ?>">

                        <input type="hidden" name="financial_year_id"  value="<?php echo $_SESSION['pur_financial_year_id']; ?>">
                      </div>
                  
                      <?php
                        $report_date='';
                        if($result3['report_date']!='' && $result3['report_date']!='0000-00-00')
                        {
                         $report_date = date("d/m/Y", strtotime($result3['report_date']));
                        }
                      ?>

                      <div class="form-group col-md-4">
                        <label for="report_date">Report Date</label>
                        <input type="text" class="form-control" name="report_date" value="<?php echo $report_date; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="tax_amt">Tax Amount</label>
                        <input type="text" class="form-control" name="tax_amt" value="<?php echo $result3['tax_amt']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="tcs_amt">TCS Amount</label>
                        <input type="text" class="form-control" name="tcs_amt" value="<?php echo $result3['tcs_amt']; ?>" readonly>
                      </div>



                      <div class="form-group col-md-4">
                        <label for="goods_value">Goods value</label>
                        <input type="text" class="form-control" name="goods_value" value="<?php echo $result3['gd_value']; ?>" readonly>
                      </div>

                      <div class="form-group col-md-4">
                        <label for="debit_amt">Debit Amount</label>
                        <input type="text" class="form-control" id="debit_amt" name="debit_amt" value="" onkeypress="return NumericValidate(event,this)" placeholder="Enter Debit Amount">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="party_tds_amt">Party TDS Amount</label>
                        <input type="text" class="form-control" id="party_tds_amt" name="party_tds_amt" value="" onkeypress="return NumericValidate(event,this)" placeholder="Enter Party TDS Amount">
                      </div>

                      <div class="form-group col-md-4">
                        <label for="net_amt">Net Amount</label>
                        <input type="text" class="form-control" id="net_amt" name="net_amt" value="<?php echo round($result3['net_amt']); ?>" readonly>
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
                          <input type="text" class="form-control " id="pay_amt" name="pay_amt" placeholder="Amount to be pay" value="" readonly="">
                        </div>


                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary waves">Submit</button>
                      </div>
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

      $('#pay_amt').val($('#net_amt').val());


      //party select
       $('#epartySelect').on('change', function() 
       {
          $.ajax({
            type: "POST",
            url: 'getInvoice.php',
            data: {party:this.value},
            success: function(response)
            {
                console.log(response);

                var jsonData = JSON.parse(response);
                console.log(jsonData);

                $('#pur_invoice_no').find('option').not(':first').remove();

                $.each(jsonData,function(index,obj)
                {
                 var option_data="<option data-report-id="+obj.id+" value="+obj.invoice_no+">"+obj.invoice_no+"</option>";
                  $(option_data).appendTo('#pur_invoice_no'); 
                });  

                $('#pur_invoice_no').prop('selectedIndex',0);  
                      
            }
          });

      });

        //invoice select
       $('#pur_invoice_no').on('change', function() 
       {
          var getRecordId=$(this).find(':selected').attr('data-report-id');
          $('#report_record_id').val(getRecordId);
         

      });





    $(document).ready(function() {

       $('#b2bSelect').on('change', function() {
                 $('.b2baddBtn').attr("disabled",false);
            });


        $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
            })
           .datepicker("setDate", new Date());

          $(".datepicker").keydown(false);

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
            $("#net_amt").val(result.net_amt);
          }
        });
    }
  

 getB2bData(); 
  //get bill 2 bill data
function getB2bData()
{
    var report_id = $('#rd_kapas_report_id').val();


    var alreadyUsed=[];
    $('.b2b_id').each(function(index){
      alreadyUsed[index]=this.value;
    });

    if(report_id!='' && report_id!=undefined)
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
    </script>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

  </body>
</html>

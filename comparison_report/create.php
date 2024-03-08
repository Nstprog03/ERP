<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

//get external party array
$extPartyArr=array();
$sqlEx = "select id,partyname from external_party";
$resultEx = mysqli_query($conn, $sqlEx);
if(mysqli_num_rows($resultEx)>0)
{
  while ($rowEx=mysqli_fetch_assoc($resultEx)) {
     $extPartyArr[]=$rowEx;
  }
}

//get firm array
$firmArr=array();
$sqlFirm = "select id,party_name from party";
$resultFirm = mysqli_query($conn, $sqlFirm);
if(mysqli_num_rows($resultFirm)>0)
{
  while ($rowFirm=mysqli_fetch_assoc($resultFirm)) {
     $firmArr[]=$rowFirm;
  }
}



?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Comparison Report Create</title>
 
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New Comparison Report</span></a>
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

      <div class="container-fluid">
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">Create New Comparison Report</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">


                  <div class="row">
                    <div class="col-md-12">
                        <h6>Sales Report :</h6>
                        <hr/>
                    </div>


                     <div class="form-group col-md-5">
                              <label for="sales_conf_no">Select External Party with Confirmation</label>
                              <?php
                              $sql = "SELECT DISTINCT(conf_no),party_name FROM `sales_report`" ;
                              $result = mysqli_query($conn, $sql);
                                    
                                  ?>                      
                                   <select id="sales_conf_no" name="sales_conf_no" data-live-search="true" class="form-control searchDropdown">
                                    <option value="" disabled selected>Select External Party</option>
                                    <?php                   
                                      foreach ($conn->query($sql) as $result) 
                                      {

                                        $party_sql="SELECT * FROM external_party WHERE id='".$result['party_name']."'";
                                        $party_result = mysqli_query($conn, $party_sql);
                                          $party_row = $party_result->fetch_assoc();
                                         

                                        echo "<option  value='" .$result['conf_no']."'>" .$party_row['partyname'].' ('.$result['conf_no'].')'."</option>";
                                            
                                          
                                      }
                                    ?>                              
                                    </select>
                            </div>

                  </div>

                  <div class=" field_wrapper_dyamic">
                      <span class="row">
                        <div class="form-group col-md-4">
                          <label for="sales_lot_dropdown">Lot No</label>
                          <select class="form-control sales_lot_dropdown" id="sales_lot_dropdown">
                            <option disabled="" value="" selected="">Select Option</option>
                          </select>                
                        </div>
                        <div class="col-md-4 " >
                            <button type="button" style="margin-top: 32px;" class="btn btn-primary salesLotAddBtn" disabled="">Add</button>
                        </div>
                      </span>

                       <div class="dynamicSalesLotSection">

                       </div>




                    </div>



                    <div class="row">
                        <div class="form-group col-sm-4">
                          <label for="sales_bales">Bales</label>
                          <input id="sales_bales" type="text" class="form-control" name="sales_bales" placeholder="Enter Bales" onkeypress="return OnlyNumberValidation(event)" >
                        </div>
                        <div class="form-group col-sm-4">
                          <label for="sales_total_bales">Total Bales</label>
                          <input id="sales_total_bales" type="text" class="form-control" name="sales_total_bales" readonly="">
                        </div>
                       
                        <div class="form-group col-sm-4">
                          <label for="delivery_at">Delivery At</label>
                          <input id="delivery_at" type="text" class="form-control" name="delivery_at" readonly="">
                        </div>
                        <div class="form-group col-sm-4">
                          <label for="invoice_raise_name">Invoice Raise in the Name</label>
                          <input id="invoice_raise_name" type="text" class="form-control"  readonly="">
                          <input type="hidden" name="invoice_raise" id="invoice_raise_id" value="">
                        </div>
                    </div>

                  <br><br>
                   <hr/>

                  <div class="row">
                    <div class="col-md-12">
                        <h6>Purchase Report :</h6>
                        <hr/>
                    </div>
                  </div>


                

                    <div class="row">
                    
                    <div class="form-group col-md-5">
                              <label for="pur_conf_no">Select External Party</label>
                              <?php
                              $sql = "SELECT DISTINCT(conf_no),party FROM `pur_report` order by id DESC" ;
                              $result = mysqli_query($conn, $sql);
                              $purConfArr=array(); 
                              if(mysqli_num_rows($result)>0)
                              {
                                $i=0;
                                while ($rowConf=mysqli_fetch_assoc($result)) 
                                {
                                  
                                   $party_sql="SELECT * FROM external_party WHERE id='".$rowConf['party']."'";
                                    $party_result = mysqli_query($conn, $party_sql);
                                    $party_row = $party_result->fetch_assoc();

                                    $purConfArr[$i]['conf_no']=$rowConf['conf_no'];
                                    $purConfArr[$i]['ext_party_name']=$party_row['partyname'];
                                    $i++;
                                }
                              }


                                    
                                  ?>   

                                   <select id="pur_conf_no" name="pur_conf_no[]" data-live-search="true" class="form-control searchDropdown pur_conf_no">
                                    <option value="" disabled selected>Select External Party</option>

                                    <?php

                                    

                                    if(count($purConfArr)>0)
                                    {
                                        foreach ($purConfArr as $result) 
                                        {
                                          echo "<option  value='" .$result['conf_no']."'>" .$result['ext_party_name'].' ('.$result['conf_no'].')'."</option>";
                                        }
                                    }
                                    
                                    ?>                              
                                    </select>
                        </div>

                        </div>


                        <div class=" field_wrapper_dyamic">
                              <span class="row">
                                <div class="form-group col-md-4">
                                  <label for="purchase_lot_dropdown">Lot No</label>
                                  <select class="form-control purchase_lot_dropdown" id="purchase_lot_dropdown">
                                    <option disabled="" value="" selected="">Select Option</option>
                                  </select>                
                                </div>
                                <div class="col-md-4 " >
                                    <button type="button" style="margin-top: 32px;" class="btn btn-primary purchaseLotAddBtn" disabled="">Add</button>
                                </div>
                              </span>

                            <style type="text/css">
                              .purCard{
                                margin-top: 15px;
                              }
                            </style>

                       <div class="dynamicPurchaseLotSection">

       




                       </div>

                    </div>
                        
              
                       <hr/>
              

                    <div class="form-group">
                      <button type="submit" name="Submit" id="submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>
              </div>
            </div>
          
        </div>
      </div>

</div>
</div>

 <script type="text/javascript">



        var salesDataArr='';
        var purchaseDataArr=[];

     

        $(document).ready(function () {


          // sales section -----------------------------------
          $('#sales_conf_no').on('change', function() {

              $('.dynamicSalesLotSection').empty();
                $('#invoice_raise_name').val('');
              $('#invoice_raise_id').val('');
              $('#delivery_at').val('');
              getSalesData();
          });

           $('#sales_lot_dropdown').on('change', function() {

              $('.salesLotAddBtn').attr('disabled',false);
             
          });


          $('.salesLotAddBtn').click(function(){

              var arrIndex = $('#sales_lot_dropdown :selected').val();

              var addedLot = $('.lot_no').length;


              if(arrIndex!='')
              {
                    var data=salesDataArr[arrIndex];
                    var invoice_date=changeDateFormat(data.invoice_date)

                    if(addedLot==0)
                    {
                      var balesfieldHTML= '<div class="row"><div class="form-group col-md-2"><label>LOT No.</label><input type="text" class="form-control lot_no" readonly="" name="sales_lot_no[]" value="'+data.lot_no+'"></div><div class="form-group col-md-2"><label>LOT Bales</label><input type="text" class="form-control lot_bales" name="sales_lot_bales[]" placeholder=" Bales" value="'+data.lot_bales+'" readonly></div><div class="form-group col-md-2"><label>Invoice No.</label><input type="text" class="form-control sales_invoice_no" name="sales_invoice_no[]" value="'+data.invoice_no+'" readonly></div><div class="form-group col-md-2"><label>Invoice Date</label><input type="text" class="form-control" value="'+invoice_date+'" readonly></div><div class="form-group col-md-2"><label>Vehicle No</label><input type="text" class="form-control" value="'+data.veh_no+'" readonly></div><input type="hidden" id="sales_veh_id" name="sales_veh_id[]" value="'+data.veh_id+'"/><input type="hidden" id="sales_invoice_date" name="sales_invoice_date[]" value="'+data.invoice_date+'"/><input type="hidden" id="sales_report_id" name="sales_report_id[]" value="'+data.sales_report_id+'"/><input type="hidden" class="arrIndex" value="'+arrIndex+'"/><div class="col-md-2"><a href="javascript:void(0);" style="margin-top:28px;" class="btn btn-danger remove_btn">-</a></div></div>';


                      $('.dynamicSalesLotSection').append(balesfieldHTML);

                      $('#sales_lot_dropdown option[value="'+arrIndex+'"]').remove();
                      $('#sales_lot_dropdown').prop('selectedIndex',0); 

                     $('#invoice_raise_name').val(data.invoice_raise_name);
                     $('#invoice_raise_id').val(data.invoice_raise_id);
                     $('#delivery_at').val(data.delivery_at);

                    

                    }
                    else
                    {
                        alert('Sorry ! You Can Add Only 1 LOT...')
                    }
                   
              }
              
            
          });


            $('.dynamicSalesLotSection').on('click', '.remove_btn', function(e)
            {
              e.preventDefault();

            var arrIndex=$(this).parent().parent().find('.arrIndex').val();

            var data = salesDataArr[arrIndex];

             

             $('#sales_lot_dropdown').append( '<option value="'+arrIndex+'">'+data.lot_no+'</option>' );

             $("#sales_lot_dropdown option").sort(function(a, b) {
                  a = a.value;
                  b = b.value;
                  return a-b;
              }).appendTo('#sales_lot_dropdown');

              $('#sales_lot_dropdown').prop('selectedIndex',0);
              $(this).parent('div').parent('div').remove(); 

              $('#invoice_raise_name').val('');
              $('#invoice_raise_id').val('');
              $('#delivery_at').val('');

              
          });


          $('#sales_bales').on('input', function() {

               $('span.error-keyup-118').hide();
              $('#submit').attr('disabled',false);


              var enterBales=0;
              if(this.value!='')
              {
                enterBales=this.value
              }


              countSalesBales=0;
              $(".lot_bales").each(function( index ) 
              {
                  countSalesBales+=parseInt(this.value);
              });

              if(countSalesBales!=0)
              {

                if(parseInt(enterBales)>parseInt(countSalesBales))
                {
                  $(this).after('<span class="error error-keyup-118 text-danger">Bales Should Be Less Than LOT Bales.</span>');
                  $('#sales_total_bales').val('');
                  $('#submit').attr('disabled',true);
                }
                else
                {
                  var total_bales=parseInt(countSalesBales)-parseInt(enterBales);
                  $('#sales_total_bales').val(total_bales);
                }

                  
              }
             
          });





          // purchase section -----------------------------------

          $('#pur_conf_no').on('change', function() {

              
                getPurchaseData();
          });

           $('#purchase_lot_dropdown').on('change', function() {

              $('.purchaseLotAddBtn').attr('disabled',false);
             
          });


           var pindex=1;

           $('.purchaseLotAddBtn').click(function(){

              var arrIndex = $('#purchase_lot_dropdown :selected').val();

              if(arrIndex!='')
              {
                pindex+=1;

                  var pur_conf_no = $('#pur_conf_no :selected').val();

                  var data=purchaseDataArr[pur_conf_no][arrIndex];


                
                  //firm option bind
                  var firmArr=<?php echo json_encode($firmArr) ?>;
                  var firmOptions='';
                  $.each(firmArr, function(i, item) {
                      firmOptions+='<option value="'+item.id+'">'+item.party_name+'</option>';
                    });
                    

                      var balesfieldHTML= '<div class="card purCard"><div class="card-header">Lot No. '+data.lot_no+'<div style="float: right;"><a href="javascript:void(0);" class="btn btn-sm btn-danger pur_remove_btn">-</a></div></div><div class="card-body"><div class="row"><div class="form-group col-md-4"><label>LOT No.</label><input type="text" class="form-control pur_lot_no" readonly="" name="pur_lot_no[]" value="'+data.lot_no+'"></div><div class="form-group col-md-4"><label>LOT Qty.</label><input type="text" class="form-control pur_lot_qty" name="pur_lot_qty[]" placeholder="LOT Qty." value="'+data.lot_bales+'" onkeyup="purLotQtyChange(this)" readonly></div><div class="form-group col-md-4"><label>Invoice No.</label><input type="text" class="form-control pur_invoice_no" name="pur_invoice_no[]" value="'+data.invoice_no+'" readonly></div><div class="form-group col-sm-4"><label for="used_firm_bales">Use of Firm Bales :</label><input type="text" class="form-control" value="'+data.firm_name+'" readonly=""></div><div class="form-group col-md-4"><label for="total_dispatch_bales">Total No Of Bales Dispatch</label><input type="text" class="form-control disBales" name="total_dispatch_bales[]"  placeholder="Total No Of Bales Dispatch" value="" onkeypress="return OnlyNumberValidation(event,this)" onkeyup="return dispatchBalesValidation(this)"></div><div class="form-group col-sm-4"><label for="party_with_conf">External Party & Conf No.</label><input type="text" name="ext_conf_no[]" class="form-control" value="'+data.ext_conf_no+'" readonly=""></div><input type="hidden" id="purchase_report_id" name="purchase_report_id[]" class="purchase_report_id" value="'+data.purchase_report_id+'"/><input type="hidden" class="pconf_no" value="'+pur_conf_no+'" name="pconf_no[]"/><input type="hidden" class="used_firm_bales" name="used_firm_bales[]" value="'+data.firm_id+'"/><input type="hidden" class="PurArrIndex" value="'+arrIndex+'"/></div></div></div>';

                          var script="<script>";
                          script+='$(".searchDropdown'+pindex+'").selectpicker();<';
                          script+="/script>";

                          balesfieldHTML+=script; 

                    

                     $('.dynamicPurchaseLotSection').append(balesfieldHTML);

                      $('#purchase_lot_dropdown option[value="'+arrIndex+'"]').remove();
                      $('#purchase_lot_dropdown').prop('selectedIndex',0); 




                   
              }
              
            
          });

           $('.dynamicPurchaseLotSection').on('click', '.pur_remove_btn', function(e)
            {

              e.preventDefault();

               var selectedConfNo = $('#pur_conf_no :selected').val();



               var pur_conf_no=$(this).parent().parent().parent().find('.pconf_no').val();
               var arrIndex=$(this).parent().parent().parent().find('.PurArrIndex').val();






               if(pur_conf_no==selectedConfNo)
               {
                  var data = purchaseDataArr[pur_conf_no][arrIndex];

                  $('#purchase_lot_dropdown').append( '<option value="'+arrIndex+'">'+data.lot_no+'</option>' );

                   $("#purchase_lot_dropdown option").sort(function(a, b) {
                        a = a.value;
                        b = b.value;
                        return a-b;
                    }).appendTo('#purchase_lot_dropdown');

               }
              

               $('#purchase_lot_dropdown').prop('selectedIndex',0);
                $(this).parent('div').parent('div').parent('div').remove(); 
        
              
          });



           //validation on form submit
           $('form').on('submit', function() {


             var countPurLot = $(".disBales").length;

             if(parseInt(countPurLot)!=0)
             {
                countSalesQty=$("#sales_total_bales").val();
          
                countPurchaseQty=0;
                $(".disBales").each(function( index ) 
                {
                    countPurchaseQty+=parseInt(this.value)
                });

             

                if(countPurchaseQty!=countSalesQty)
                {
                  alert('Sales LOT Qty & Purchase LOT Qty Should Be Equal.');
                  return false;
                }
             }

             
            });

          
        });


         //sales section ---------------------------------------------

        function getSalesData()
        {
         
           var sales_conf_no = $('#sales_conf_no :selected').val();

            
                  $.ajax({
                  type: "POST",
                  url: 'getDataSalesReport.php',
                  data: {conf_no:sales_conf_no},
                  success: function(response)
                  {
                      var jsonData = JSON.parse(response);
                      console.log(jsonData);

                      salesDataArr=jsonData;

                      $('#sales_lot_dropdown').find('option').not(':first').remove();
                      for (var i=0;i<salesDataArr.length;i++)
                      {
                       $('<option/>').val(i).html(salesDataArr[i].lot_no).appendTo('#sales_lot_dropdown');
                      }
                      $('#sales_lot_dropdown').val('');

                 }
                });
        }

        


        //purchase section ---------------------------------------------
        function getPurchaseData()
        {
         
           var pur_conf_no = $('#pur_conf_no :selected').val();

            
                  $.ajax({
                  type: "POST",
                  url: 'getDataPurchaseReport.php',
                  data: {conf_no:pur_conf_no},
                  success: function(response)
                  {
                      var jsonData = JSON.parse(response);
                     // console.log(jsonData);

                      purchaseDataArr[pur_conf_no]=jsonData;

                      var LotList=purchaseDataArr[pur_conf_no];

                      $('#purchase_lot_dropdown').find('option').not(':first').remove();
                      for (var i=0;i<LotList.length;i++)
                      {
                       $('<option/>').val(i).html(LotList[i].lot_no).appendTo('#purchase_lot_dropdown');
                      }
                      $('#purchase_lot_dropdown').val('');

                 }
                });

                  
        }

        function dispatchBalesValidation(e)
        {
          var lot_qty=$(e).parent('div').parent('div').find('.pur_lot_qty').val();

          

          $('span.error-keyup-560').hide();
          $('#submit').attr('disabled',false);

          

          if(parseInt(e.value)>parseInt(lot_qty))
          {
            $(e).after('<span class="error error-keyup-560 text-danger">Sorry! Available LOT Qty is '+lot_qty+'</span>');
            $('#submit').attr('disabled',true);
            
          }

        }





        function purLotQtyChange(e)
        {
            //get arra index no from data attribute of lot_no dropdown
            var pur_conf_no=$(e).parent().parent().find('.pconf_no').val();
            var arrIndex=$(e).parent().parent().find('.PurArrIndex').val();




            $('span.error-keyup-111').hide();
            $('#submit').attr('disabled',false);

           
              var data  = purchaseDataArr[pur_conf_no][arrIndex];

              if(parseInt(e.value)>parseInt(data.lot_bales))
              {
                 $(e).after('<span class="error error-keyup-111 text-danger">Sorry! Available Bales is '+data.lot_bales+'</span>');
                  $('#submit').attr('disabled',true);
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



   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

      <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


  </body>
</html>

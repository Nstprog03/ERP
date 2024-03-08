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
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>URD Kapas Purchase & Payment Report Create</title>

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


     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 


       $(".datepicker").datepicker({
            
              dateFormat: "dd/mm/yy",
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Create New URD Kapas Purchase & Payment Report</span></a>
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

              <div class="card-header">URD Kapas Purchase & Payment Report Create
                 <div style="float: right;">
                     <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                     Calculator
                    </button>
                 </div>
              </div>

              <div class="container-fluid demo">
    <div class="modal left fade" id="exampleModal" tabindex="" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              
                <div class="modal-body">
                    <div class="nav flex-sm-column flex-row">
                      <label for="amt"><b>Calculation</b></label>
                      <div class="row">
                        <div class="form-group col-md-6">
                          <label for="amt">Amount</label>
                          <input type="text" name="amt" class="form-control" placeholder="Amount" id="amt">
                        </div>

                        <div class="form-group col-md-6">
                          <label for="rate">Rate</label>
                          <input type="text" name="rate" class="form-control" placeholder="Rate" id="rate">
                        </div>

                        <div class="form-group col-md-12">
                          <label for="man">Man Amount</label>
                          <input type="text" name="man" class="form-control" placeholder="Man Amount" id="man" readonly="">
                        </div>
                      </div>
                      <div class="row">

                        <div class="form-group col-md-6">
                          <label for="man1">Man Amount</label>
                          <input type="text" name="man1" class="form-control" placeholder="Man" id="man1" onkeypress="return NumericValidate(event,this)" >
                        </div>

                        <div class="form-group col-md-6">
                          <label for="rate1">Rate</label>
                          <input type="text" name="rate1" class="form-control" placeholder="Rate" id="rate1" onkeypress="return NumericValidate(event,this)" >
                        </div>

                        <div class="form-group col-md-12">
                          <label for="total">Total Amount</label>
                          <input type="text" name="total" class="form-control" placeholder="Total Amount" id="total" readonly="">
                        </div>

                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>







              <div class="card-body">

                <form class="" action="add.php" method="post"
                   enctype="multipart/form-data">
                   
                            <?php
                              $sql = "select * from farmer";
                              $result = mysqli_query($conn, $sql);
                            ?>
                        <div class="row">
                            <div class="form-group col-md-4">
                              <label for="farmer">Select Farmer</label>
                               <a class="btn btn-primary btn-sm" target="_blank" href="/farmer/create.php"><i class="fa fa-user-plus"></i></a>
                                  <select name="farmer" data-live-search="true" class="form-control searchDropdown" onchange="changeParty()">
                                     <option value="" disabled selected>Choose option</option>
                                      <?php                   
                                        foreach ($conn->query($sql) as $result) 
                                        {
                                              echo "<option data-value='".str_replace(' ','',$result['farmer_name'])."' value='" .$result['id']. "'>" .$result['farmer_name']. "</option>";
                                        }
                                      ?>                              
                                  </select>
                            </div>

                            <div class="form-group col-md-4">
                              <label for="village">Select Village </label>
                              <select name="village" class="form-control">
                               <option value="" disabled selected>Choose option</option>
                                <?php                   
                                  foreach ($conn->query($sql) as $result) 
                                  {
                                        echo "<option data-party='".str_replace(' ','',$result['farmer_name'])."' value='" .$result['vlg_name']. "'>" .$result['vlg_name']. "</option>";
                                  }
                                ?>                              
                              </select>
                            </div>

                            <div class="form-group col-md-4">
                              <label for="district">Select District </label>
                              <select name="district" class="form-control">
                                <option value="" disabled selected>Choose option</option>
                                <?php                   
                                  foreach ($conn->query($sql) as $result) 
                                  {
                                        echo "<option data-party='".str_replace(' ','',$result['farmer_name'])."' value='" .$result['dist_name']. "'>" .$result['dist_name']. "</option>";
                                  }
                                ?>                              
                              </select>
                            </div>
                        </div>
                        
                        <div class="row">
                          <div class="form-group col-md-4">
                            <label for="weight">Man Weight</label>
                            <input type="text" value="" class="form-control weight" name="weight" placeholder="Enter Weight">
                          </div>

                          <div class="form-group col-md-4">
                              <label for="rate">Man Rate</label>
                            <input type="text" value="" class="form-control rate" name="rate" placeholder="Enter Rate">
                          </div>

                          <div class="form-group col-md-4">
                              <label for="amount">Amount</label>
                            <input type="text" value="" class="form-control bold" id="amount" name="amount" readonly>
                          </div>
                        </div> 





                        <div class="row">


                          
                  <div class="form-group col-md-4">
                      <label for="date">Select Date</label>
                      <input type="text" class="form-control datepicker" name="date"  placeholder="Select Date" value="" autocomplete="off">
                  </div>



                          <div class="form-group col-md-4">
                           <label for="firm">Firm</label>
                          <input type="text" class="form-control" value="<?php echo $_SESSION['pur_firm']; ?>" readonly="">
                          <input type="hidden" value="<?php echo $_SESSION['pur_firm_id']; ?>" name="firm">

                          <input type="hidden" value="<?php echo $_SESSION['pur_financial_year_id']; ?>" name="pur_financial_year">
                          </div>
                          <div class="form-group col-md-4">
                            <label for="broker">Select Broker</label>
                            <a class="btn btn-primary btn-sm" target="_blank" href="/broker/create.php"><i class="fa fa-user-plus"></i></a>
                            <?php
                              $sql = "select * from broker";
                              $result = mysqli_query($conn, $sql);
                            ?>                    
                            <select name="broker" data-live-search="true" class="form-control searchDropdown">
                              <?php                   
                                foreach ($conn->query($sql) as $result) {
                                  echo "<option  value='".$result['id']."'>" .$result['name']. "</option>";  
                                }
                              ?>
                            </select>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="payment">Payment Status</label>
                             <select name="payment" class="form-control">
                              <option value="" selected="selected" disabled="">Select Payment Status</option>
                              <option  value="complete">Complete</option>
                              <option  value="pending">Pending</option>          
                            </select>
                          </div>
                        </div>
                  

                    <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>
              </div>
            </div>
          
        </div>
      </div>
</div>
</div>
  
  <style type="text/css">

    .modal.left .modal-dialog {
  position:fixed;
  right: 0;
  margin: auto;
  width: 320px;
  height: 100%;
  -webkit-transform: translate3d(0%, 0, 0);
  -ms-transform: translate3d(0%, 0, 0);
  -o-transform: translate3d(0%, 0, 0);
  transform: translate3d(0%, 0, 0);
}

.modal.left .modal-content {
  height: 75%;
  overflow-y: auto;
}

.modal.right .modal-body {
  padding: 15px 15px 80px;
}

.modal.right.fade .modal-dialog {
  left: -320px;
  -webkit-transition: opacity 0.3s linear, left 0.3s ease-out;
  -moz-transition: opacity 0.3s linear, left 0.3s ease-out;
  -o-transition: opacity 0.3s linear, left 0.3s ease-out;
  transition: opacity 0.3s linear, left 0.3s ease-out;
}

.modal.right.fade.show .modal-dialog {
  right: 0;
}

/* ----- MODAL STYLE ----- */
.modal-content {
  border-radius: 0;
  border: none;
}

.modal-header {
  border-bottom-color: #eeeeee;
  background-color: #fafafa;
}

/* ----- v CAN BE DELETED v ----- */




.btn-demo {
  margin: 15px;
  padding: 10px 15px;
  border-radius: 0;
  font-size: 16px;
  background-color: #ffffff;
}

.btn-demo:focus {
  outline: 0;
}

  </style>  
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <script type="text/javascript">
    function changeParty() {
      $('select[name=village] option').hide();
      $('select[name=village] option[data-party='+$("select[name=farmer] option:selected").attr("data-value")+']').show();
      $('select[name=district] option').hide();
      $('select[name=district] option[data-party='+$("select[name=farmer] option:selected").attr("data-value")+']').show();

    }

        $(document).ready(function(){
          $('input[type="text"]').keyup(function () {
            var val1 = parseFloat($('.weight').val());
            var val2 = parseFloat($('.rate').val());
            if (isNaN(val1)) {
              val1 = 0;
            }
            if (isNaN(val2)) {
              val2 = 0;
            }
            var amount = val1*val2;
            $("input#amount").val(amount);       
          });

          // amount validation
          $('#amt').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
              event.preventDefault(); //stop character from entering input
            }

          });

          // amount validation
          $('#rate').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
              event.preventDefault(); //stop character from entering input
            }

          });

          // rate cal
          $('#rate').keyup(function() 
          {

            var amount = $('#amt').val();
            var rate = $('#rate').val();
            $('#rate1').val(rate);
            if (isNaN(amount)) {
              amount = 0;
            }
            if (isNaN(rate)) {
              rate = 0;
            }
            var man = parseFloat(amount)/parseFloat(rate);

            if (isNaN(man)) {
              man = 0;
            }

            $('#man').val(man.toFixed(2));
            $('#man1').val(man.toFixed(2)); 

            // second cal
            var man1 = $('#man1').val();
            var rate1 = $('#rate1').val();
            if (isNaN(man1)) {
              man1 = 0;
            }
            if (isNaN(rate1)) {
              rate1 = 0;
            }
            var total = parseFloat(man1)*parseFloat(rate1);
            if (isNaN(total)) {
              total = 0;
            }
            $('#total').val(total.toFixed(2));         
          });


          // amt cal
          $('#amt').keyup(function() 
          {
            var amount = $('#amt').val();
            var rate = $('#rate').val();
            $('#rate1').val(rate);
            if (isNaN(amount)) {
              amount = 0;
            }
            if (isNaN(rate)) {
              rate = 0;
            }

            var man = parseFloat(amount)/parseFloat(rate);
            if (isNaN(man)) {
              man = 0;
            }

            $('#man').val(man.toFixed(2));
            $('#man1').val(man.toFixed(2));

            // second cal
            var man1 = $('#man1').val();
            var rate1 = $('#rate1').val();
            if (isNaN(man1)) {
              man1 = 0;
            }
            if (isNaN(rate1)) {
              rate1 = 0;
            }
            var total = parseFloat(man1)*parseFloat(rate1);

            if (isNaN(total)) {
              total = 0;
            }


            $('#total').val(total.toFixed(2));     
          });


          // man amount 
          $('#man1').keyup(function() {
            var man1 = $('#man1').val();
            var rate1 = $('#rate1').val();
            if (isNaN(man1)) {
              man1 = 0;
            }
            if (isNaN(rate1)) {
              rate1 = 0;
            }
            var total = parseFloat(man1)*parseFloat(rate1);

            if (isNaN(total)) {
              total = 0;
            }

            $('#total').val(total.toFixed(2));  
          }); 

          // man amount 
          $('#rate1').keyup(function() {
            var man1 = $('#man1').val();
            var rate1 = $('#rate1').val();
            if (isNaN(man1)) {
              man1 = 0;
            }
            if (isNaN(rate1)) {
              rate1 = 0;
            }
            var total = parseFloat(man1)*parseFloat(rate1);
            if (isNaN(total)) {
              total = 0;
            }
            $('#total').val(total.toFixed(2));  
          }); 



        });


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
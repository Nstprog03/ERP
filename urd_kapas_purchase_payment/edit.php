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

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from urd_purchase_payment where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      // print_r($row);
      // exit();
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
    $id = $_GET['id'];
		$farmer = $_POST['farmer'];
   
    $weight = $_POST['weight'];
    $rate = $_POST['rate'];
    $amount = $_POST['amount'];
    $broker = $_POST['broker'];
    $payment_status = $_POST['payment'];
    $village = $_POST['village'];
    $district = $_POST['district'];

     $date = '';
    if($_POST['date']!='')
    {
      $date = str_replace('/', '-', $_POST['date']);
      $date = date('Y-m-d', strtotime($date));
    }

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    
    
		if(!isset($errorMsg)){
			$sql = "update urd_purchase_payment
									set farmer = '".$farmer."',
                    weight = '".$weight."',
                    rate = '".$rate."',
                    amount = '".$amount."',
                    broker = '".$broker."',
                    payment_status = '".$payment_status."',
                    district = '".$district."',
                    date= '".$date."',
                    username= '".$username."',
                    updated_at= '".$timestamp."'

					where id=".$id;
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
                echo $errorMsg;
			}
		}

	}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Edit URD Kapas Report</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit URD Kapas Report</span></a>
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
           $sqlLastChange="select username,updated_at from urd_purchase_payment where id='".$row['id']."'";

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
              <div class="card-header">
                Update RD Kapas Report
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
                <form class="" action="" method="post"
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
                                          $isFirmSelected ="";
                                          if ($result['id'] == $row['farmer']) {
                                            $isFirmSelected = "selected";
                                          }
                                              echo "<option data-value='".str_replace(' ','',$result['farmer_name'])."' value='" .$result['id']."'".$isFirmSelected.">" .$result['farmer_name']. "</option>";
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
                                    $isFirmSelected ="";
                                    if ($result['vlg_name'] == $row['village']) {
                                      $isFirmSelected = "selected";
                                    }
                                        echo "<option data-party='".str_replace(' ','',$result['farmer_name'])."' value='" .$result['vlg_name']."'".$isFirmSelected.">" .$result['vlg_name']. "</option>";
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
                                    $isFirmSelected ="";
                                    if ($result['dist_name'] == $row['district']) {
                                      $isFirmSelected = "selected";
                                    }
                                        echo "<option data-party='".str_replace(' ','',$result['farmer_name'])."' value='" .$result['dist_name']."'".$isFirmSelected.">" .$result['dist_name']. "</option>";
                                  }
                                ?>                              
                              </select>
                            </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-4">
                            <label for="weight">Man Weight</label>
                            <input type="text"  class="form-control weight" name="weight" placeholder="Enter Weight" value="<?php echo $row['weight']; ?>">
                          </div>

                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                          <div class="form-group col-md-4">
                              <label for="rate">Man Rate</label>
                            <input type="text"  class="form-control rate" name="rate" placeholder="Enter Rate" value="<?php echo $row['rate']; ?>" >
                          </div>

                          <div class="form-group col-md-4">
                              <label for="amount">Amount</label>
                            <input type="text"  class="form-control bold" id="amount" name="amount" readonly  value="<?php echo $row['amount']; ?>">
                          </div>
                        </div>                  


                        <div class="row">



                            <?php
                     
                        $date='';
                        if($row['date']!='' && $row['date']!='0000-00-00')
                          {
                            $date = str_replace('-', '/', $row['date']);
                            $date = date('d/m/Y', strtotime($date));
                          }

                      ?>

                  <div class="form-group col-md-4">
                      <label for="date">Select Date</label>
                      <input type="text" class="form-control datepicker" name="date"  placeholder="Select Date" value="<?php echo $date ?>" autocomplete='off'>
                  </div>







                          <div class="form-group col-md-4">
                            <label for="firm">Firm</label>
                                <?php
                                  $sql4 = "select * from party where id='".$row['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }
                                        
                                ?>          
                               
                          <input type="text" class="form-control" value="<?php echo $pname; ?>" readonly="">
                         
                        
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
                                  $isFirmSelected ="";
                                    if ($result['id'] == $row['broker']) {
                                      $isFirmSelected = "selected";
                                    }
                                  echo "<option  value='" .$result['id']."'".$isFirmSelected.">" .$result['name']. "</option>";  
                                }
                              ?>
                            </select>
                          </div>
                          <div class="form-group col-md-4">
                            <label for="payment">Payment Status</label>
                             <select name="payment" class="form-control">
                              <option value="" selected="selected" disabled="">Select Payment Status</option>
                              <option  value="complete"<?php if($row['payment_status'] == 'complete'){echo 'selected';} ?>>Complete</option>
                              <option  value="pending" <?php if($row['payment_status'] == 'pending'){echo 'selected';} ?>>Pending</option>          
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
  
    <!-- Popper.JS -->

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
<script type="text/javascript">


        $(document).ready(function(){
          $('input[type="text"]').keyup(function () {
            var val1 = parseFloat($('.basic').val());
            var val2 = parseFloat($('.tax').val());
            var val3 = parseFloat($('.tcs').val());
            var val4 = parseFloat($('.tcs_amt').val());
            var val5 = parseFloat($('.dbt_amt').val());
            var val6 = parseFloat($('.gd_value').val());

                    var sum = (val1+val2) * val3/100;
                    var mnt = val1+val2+(sum);
                    var net = (mnt)-val5;
                    $("input#result").val(sum);
                    $("input#gd_amt").val(mnt);
                    $("input#net_amt").val(net);       
          });

});

</script>

<script type="text/javascript">
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
          $('#amt').keyup(function() {
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
          $('#rate1').keyup(function() 
          {
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

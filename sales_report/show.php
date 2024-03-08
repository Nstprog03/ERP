<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['sales_conf_firm_id']) && !isset($_SESSION['sales_financial_year_id']))
{
  header('Location: ../sales_conf_index.php');
}

$dir = "/file_storage/"; 

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

  $getYear=$_SESSION['sales_conf_financial_year'];
  $year_array=explode("/",$getYear);

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from sales_report where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sale Report</title>
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

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date('<?php echo($year_array[1]) ?>'),
        minDate: new Date('<?php echo($year_array[0]) ?>')
    });


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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Sales Report Show</span></a>
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
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["sales_conf_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["sales_conf_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
          $sqlLastChange="select username,updated_at from sales_report where id='".$row['id']."'";

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
              <div class="card-header">Sales Report Show</div>
              <div class="card-body">
             
                <form   method="post"
                   enctype="multipart/form-data">
                   <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="row">
                       <?php
                            $sql = "select * from seller_conf where firm='".$_SESSION['sales_conf_firm_id']."'";
                            $result = mysqli_query($conn, $sql);

                             $sql3 = "select * from sales_conf_split where firm='".$_SESSION['sales_conf_firm_id']."'";
                            $result3 = mysqli_query($conn, $sql3);

                            
                          ?>   
               
                        <div class="form-group col-sm-6">
                          <label for="party_data">Select Party</label>
                          <select name="party_data" id="conf_no" class="form-control" disabled="" >
                            <option value="" disabled selected>Select</option>
                            <?php 

                             $fyear=explode("/",$_SESSION["sales_conf_financial_year"]);
                               $startDate=$fyear[0];
                               $endDate=$fyear[1]; 
                              
                              foreach ($conn->query($sql) as $result) 
                              {

                                //External Party
                                $Ex_party = "select * from external_party where id='".$result['external_party']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);

                                  //bales count
                                    $sql2="SELECT SUM(no_of_bales) as used_bales FROM sales_conf_split WHERE conf_no='".$result['sales_conf']."'";
                                    $result2 = mysqli_query($conn, $sql2);
                                    $row2 = mysqli_fetch_assoc($result2);
                                    $isSelected =""; 
                                    if($row['conf_no']==$result['sales_conf'])
                                    {
                                       $isSelected = "selected";
                                    }


                                    if($result['sales_date']>=$startDate && $result['sales_date']<=$endDate)
                                    {
                                        if($result['cont_quantity']-$row2['used_bales']!=0)
                                          {

                                             echo "<option  value='" .$result['external_party'].'/'.$result['sales_conf'].'/conf'."'".$isSelected.">" .$Ex_partyrow['partyname'].' ('.$result['sales_conf'].')'."</option>";
                                          }
                                    }
                              }

                              foreach ($conn->query($sql3) as $result3) 
                              {
                                //External Party
                                $Ex_party = "select * from external_party where id='".$result3['split_party_name']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);

                                $isSelected2 =""; 
                                if($row['conf_no']==$result3['conf_split_no'])
                                {
                                   $isSelected2 = "selected"; 
                                }
                                  if($result3['conf_split_date']>=$startDate && $result3['conf_split_date']<=$endDate)
                                  {
                                       echo "<option  value='" .$result3['split_party_name'].'/'.$result3['conf_split_no'].'/confsplit'."'".$isSelected2.">" .$Ex_partyrow['partyname'].' ('.$result3['conf_split_no'].')'."</option>";
                                  }
                               
                              }

                            ?>                              
                          </select>     
                        </div>

                        <div class="form-group col-sm-6">
                            <label for="party">Firm</label>
                            <input type="text" class="form-control" value="<?php echo $_SESSION['sales_conf_firm'] ?>" readonly>
                            <input type="hidden" name="firm" value="<?php echo $_SESSION['sales_conf_firm_id'] ?>">
                               
                        </div>
                    </div>

                     
                    <div class="row">
                    
                      <div class="form-group col-sm-4">
                        <label for="delivery_city">Delivery City</label>
                        <input type="text" class="form-control" name="delivery_city"  placeholder="Enter Delivery City" value="<?php echo $row['delivery_city'] ?>">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="truck">Truck</label>
                        <?php
                              $sql = "select t.*,p.trans_name from truck_master t, transport p where t.transport=p.id";
                              $result = mysqli_query($conn, $sql);
                              
                            ?>                      
                              <select name="truck" class="form-control" disabled="">
                              <?php                   
                                foreach ($conn->query($sql) as $result) 
                                {
                                  $isSelected =""; 
                                  if($row['truck']==$result['id'])
                                  {
                                     echo $isSelected = "selected";
                                  }

                                   echo "<option  value='" .$result['id']."'".$isSelected.">" .$result['truck_no']. ' (' .$result['trans_name'].')'."</option>";
                                  
                                  //echo "<option  value='" .$result['id']."'".$isSelected.">" .$result['truck_no']. '-' .$result['transport']. "</option>";
                                }
                              ?>                              
                              </select>
                      </div>
                      <?php
                        $invoice_date='';
                        if($row['invoice_date']!='')
                        {
                          $invoice_date=date('d/m/Y', strtotime($row['invoice_date']));
                        }
                      ?>
                      <div class="form-group col-sm-4">
                        <label for="report_date"> Invoice Date :</label>
                          <input type="text" class="form-control " name="invoice_date"  placeholder=" Invoice Date" value=" <?php echo $invoice_date ?>" autocomplete="off">
                      </div>


                      <?php
                        $parakh_date='';
                        if($row['parakh_date']!='')
                        {
                          $parakh_date=date('d/m/Y', strtotime($row['parakh_date']));
                        }
                      ?>

                      <div class="form-group col-sm-4">
                        <label for="parakh_date"> Parakh Date :</label>
                          <input type="text" class="form-control " name="parakh_date" value="<?php echo $parakh_date ?>" placeholder=" Parakh Date" value="" autocomplete="off" required="">
                      </div>


                      <div class="form-group col-sm-4">
                        <label for="invice_no">Invoice No</label>
                        <input type="text" class="form-control" name="invice_no"  placeholder="Enter Invoice No" value="<?php echo $row['invice_no'] ?>" onkeypress="return NumericValidate(event)" readonly>
                      </div>
                      <div class="form-group col-sm-4">
                        <label for="avl_bales">No Of Bales (Available)</label>
                        <input type="text" class="form-control" id="avl_bales" name="avl_bales" value="<?php echo $row['avl_bales'] ?>" readonly>
                      </div>
                      <div class="form-group col-sm-4">
                        <label for="noOFBales">No Of Bales </label>
                        <input type="text"  class="form-control numericValidation" id="noOFBales" name="noOFBales" value="<?php echo $row['noOFBales'] ?>">
                      </div>
                      
                    </div>

                    <div class=" field_wrapper_dyamic">
                      
                      <?php 
                    $balesArr=json_decode($row['lot_no']);
                    $lot_bales=json_decode($row['lot_bales']);
                    foreach ($balesArr as $key=> $value) 
                    {

                      if($key==0)
                      {
                      ?>
                        <span class="row removefiled">
                          <div class="form-group col-md-4">
                            <label>Lot No.</label>
                            <input type="text" class="form-control" readonly="" name="lot_no[]" value="<?php echo $value ?>">
                          </div>
                          <div class="form-group col-md-4">
                           <label>Lot Qty</label>
                            <input type="text" class="form-control countbales" name="lot_bales[]" placeholder=" Bales" value="<?php echo $lot_bales[$key] ?>" onkeyup="checkBales()">
                          </div>                       
                        </span>
                      <?php
                      }
                      else
                      {
                      ?>
                        <span class="row removefiled">
                          <div class="form-group col-md-4">
                            <input type="text" class="form-control" readonly="" name="lot_no[]" value="<?php echo $value ?>">
                          </div>
                          <div class="form-group col-md-4">
                            <input type="text" class="form-control countbales" name="lot_bales[]" placeholder=" Bales" value="<?php echo $lot_bales[$key] ?>" onkeyup="checkBales()">
                          </div>                       
                        </span>

                      <?php
                      }
                     ?>
                      
                    <?php } 
                          ?> 
                    </div>
                    

                    <div class="row">
                      <div class="form-group col-sm-4">
                       <h5>PR No :</h5>
                      </div>
                    </div>
                    <div class="row">

                      <div class="form-group col-sm-6">
                        <label for="start_pr">Start PR No.</label>
                        <input type="text" class="form-control " id="start_pr" name="start_pr"  placeholder="Enter Start PR No." value="<?php echo $row['start_pr']; ?>">
                      </div>

                      <div class="form-group col-sm-6">
                        <label for="end_pr">End PR No.</label>
                        <input type="text" class="form-control " id="end_pr" name="end_pr"  placeholder="Enter End PR No." value="<?php echo $row['end_pr']; ?>">
                      </div>

                    </div>

                    <div class="row">
                      <div class="form-group col-sm-4">
                       <h5>Rate :</h5>
                      </div>
                    </div>                    
                  <div class="row">
                      <div class="form-group col-sm-4">
                        <label for="net_weight">Net Weight</label>
                        <input type="text" class="form-control numericValidation" id="net_weight" name="net_weight"  placeholder="Enter Net Weight" value="<?php echo $row['net_weight'] ?>">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="candy_rate">Candy Rate</label>
                        <input type="text" class="form-control" id="candy_rate" name="candy_rate" placeholder="Candy Rate"  readonly="" value="<?php echo $row['candy_rate'] ?>">
                      </div>

                      

                      <div class="form-group col-sm-4">
                        <label for="grs_amt">Gross AMT</label>
                        <input type="text" class="form-control numericValidation" name="grs_amt" value="<?php echo $row['grs_amt'] ?>" placeholder="Enter Gross AMT" id="gross_amt">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="txn">Tax(%)</label>
                        <input type="text" class="form-control" name="txn"  placeholder="Enter Tax" value="<?php echo $row['txn'] ?>" id="tax" onkeypress="return decimalValidate(event,this)">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="txn_amt">Tax Amount</label>
                        <input type="text" class="form-control numericValidation" name="txn_amt"  placeholder="Enter Tax Amount" value="<?php echo $row['txn_amt'] ?>" id="txn_amt" readonly="">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="Other">Other(TCS %)</label>
                        <input type="text" class="form-control" name="Other"  placeholder="Enter Other(TCS)" value="<?php echo $row['Other'] ?>" id="other_tcs" onkeypress="return decimalValidate(event,this)">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="other_amt_tcs">Other Amount(TCS)</label>
                        <input type="text" class="form-control" name="other_amt_tcs"  placeholder="Other Amount(TCS)" id="other_amt_tcs" readonly="" value="<?php echo $row['other_amt_tcs'] ?>">
                      </div>

                      <div class="form-group col-sm-4">
                        <label for="total_value">Total Amount</label>
                        <input type="text" class="form-control bold" name="total_value"  placeholder="Total Amount" readonly="" id="total_value" value="<?php echo $row['total_value'] ?>">
                      </div>
                  <div>
                  </div></div>



      <div class="row">

<?php
  if($row['doc_file'] != ''){
   $prev = explode(',',$row['doc_file']);
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
                    <div class="form-group col-md-4 field-show-image">
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
                        <h6 class="title">Document File <?= $key+1 ?></h6>
                      
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



                   <input type="hidden" id="shipping_ext_party_id" name="shipping_ext_party_id" value="<?php echo $row['shipping_ext_party_id'] ?>">
                    <input type="hidden" id="variety" name="variety" value="<?php echo $row['variety'] ?>">
                    <input type="hidden" id="sub_variety" name="sub_variety" value="<?php echo $row['sub_variety'] ?>">
                    <input type="hidden" id="length" name="length" value="<?php echo $row['length'] ?>">
                    <input type="hidden" id="strength" name="strength" value="<?php echo $row['strength'] ?>">
                    <input type="hidden" id="mic" name="mic" value="<?php echo $row['mic'] ?>">
                    <input type="hidden" id="rd" name="rd" value="<?php echo $row['rd'] ?>">
                    <input type="hidden" id="trash" name="trash" value="<?php echo $row['trash'] ?>">
                    <input type="hidden" id="moi" name="moi" value="<?php echo $row['moi'] ?>">

                      <input type="hidden" id="credit_days" name="credit_days" value="<?php echo $row['credit_days'] ?>">


                   
               
                </form>

                 </div>
      
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
    <script type="text/javascript">


      var lotGloble = '';
      var GlobleBales = ''
      var countselectedBales = 0;
      var GlobleNoOfBales = 0;
      LoadFunction();
        $(document).ready(function () {

          $('input[type="text"]').prop('readonly', true);
          $('.numericValidation').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
              event.preventDefault();
            }
          });

          // $('#noOFBales').keyup(function() {
          //   GlobleNoOfBales = this.value;

          // });

          $('#conf_no').on('change', function() {

            // reset 
            $('.removefiled').remove();
            $('#noOFBales').val('');
            lotGloble = '';
            GlobleBales = new Array();
            countSelectedBales = 0;
            GlobleNoOfBales = 0;

            var value=this.value;
            var data=value.split("/");



            $.ajax({
                type: "POST",
                url: 'getData.php',
                data: {conf_no:data[1],table:data[2]},
                success: function(response)
                {
                    var jsonData = JSON.parse(response);
                    console.log(jsonData);
                    if (data[2] === 'conf') {
                      $('#candy_rate').val(jsonData['candy_rate']);

                      var remindBales = parseInt(jsonData['cont_quantity'])-parseInt(jsonData['used_bales']) 
                      $('#avl_bales').val(remindBales); 
                    }
                    else if(data[2] === 'confsplit')
                    {
                      var remindBales2 = parseInt(jsonData['no_of_bales'])-parseInt(jsonData['used_bales']) 
                      $('#avl_bales').val(remindBales); 
                        $('#candy_rate').val(jsonData['price']);
                        $('#avl_bales').val(remindBales2);
                    } 

                     //set dropdown
                    var lotdata=JSON.parse(jsonData['lot_no']);
                    lotGloble = lotdata;
                      $('#lot_dropdown').find('option').not(':first').remove();
                        for (var i=0;i<lotdata.length;i++)
                        {
                         $('<option/>').val(lotdata[i]).html(lotdata[i]).appendTo('#lot_dropdown');
                        }

                      // set bales Glo
                      balesArr=JSON.parse(jsonData['lot_bales']);

                      for(i=0; i<=lotdata.length; i++)
                      {
                        GlobleBales[lotdata[i]]=balesArr[i];
                       // console.log('lot '+lotdata[i]+' bales '+balesArr[i]);
                      }
                     console.log(GlobleBales);


                     // add value in hidden field
                      $('#shipping_ext_party_id').val(jsonData['shipping_ext_party_id']);
                      $('#variety').val(jsonData['variety']);
                      $('#sub_variety').val(jsonData['sub_variety']);
                      $('#length').val(jsonData['length']);
                      $('#strength').val(jsonData['strength']);
                      $('#mic').val(jsonData['mic']);
                      $('#rd').val(jsonData['rd']);
                      $('#trash').val(jsonData['trash']);
                      $('#moi').val(jsonData['moi']);
                       $('#credit_days').val(jsonData['credit_days']);






                }
            });
          });

          // show button

          $('#lot_dropdown').on('change', function() {
            var checkbales = $('#noOFBales').val();
            if (checkbales!= '') {
              $('.showButton').attr('disabled',false);
            }else{

              $('#lot_dropdown').prop('selectedIndex',0);
              $("#submit").attr("disabled", true);
              alert('Please Select No Of Bales');

            }
            
          });
          $('#noOFBales').keyup(function() {


           

            GlobleNoOfBales = this.value;


            // reset 
            // $('.removefiled').remove();

            // reloadLot();

            var noOfBales=parseInt($("#noOFBales").val());
            var avlBales=parseInt($('#avl_bales').val());
            // alert(avlBales);
            $('span.error-keyup-1').hide();
            $("#lot_dropdown").attr("disabled", false);
            $("#submit").attr("disabled", false);
            
            if(noOfBales=='0')
            {
              $("#noOFBales").val('');
            }
            if(noOfBales>avlBales)
            {
                $('#noOFBales').after('<span class="error error-keyup-1 text-danger">No Of Bales Should Not be greater than Available Bales...</span>'); 
                $("#lot_dropdown").attr("disabled", true);
                $("#submit").attr("disabled", true); 
            }

             checkBales();

          });

          // submit 

          // $('#submit').on('click',function() {
          
          // });    




          // cal
          $('#tax').keyup(function() 
          {
            var tax=parseFloat($("#tax").val());
            var gross_amt=parseFloat($("#gross_amt").val());
            var txn_amt=parseFloat($("#txn_amt").val());
            if (isNaN(tax)) {
              tax = 0;
            }
            if(isNaN(gross_amt)){
              gross_amt = 0;
            }
            var total = (gross_amt)*(tax)/100;
            $("#txn_amt").val(total.toFixed(2));

            var finaltotal = (gross_amt)+(total)+(txn_amt);
            $("#total_value").val(finaltotal.toFixed(2));
          });

          $('#gross_amt').keyup(function() {
            var tax=parseFloat($("#tax").val());
            var other_tcs=parseFloat($("#other_tcs").val());
            var gross_amt=parseFloat($("#gross_amt").val());
            var txn_amt=parseFloat($("#txn_amt").val());
            if (isNaN(tax)) {
              tax = 0;
            }
            if(isNaN(gross_amt)){
              gross_amt = 0;
            }
            var total = (gross_amt)*(tax)/100;
            $("#txn_amt").val(total.toFixed(2));

            if(isNaN(other_tcs)){
              other_tcs = 0;
            }
            var other_amount_value = (gross_amt + total)*(other_tcs/100);
            // alert(other_amount_value);
            $("#other_amt_tcs").val(other_amount_value.toFixed(2));

            var finaltotal = (gross_amt)+(other_amount_value)+(txn_amt);
            $("#total_value").val(finaltotal.toFixed(2));
          });

          $('#other_tcs').keyup(function() {
            var other_tcs=parseFloat($("#other_tcs").val());
            var gross_amt=parseFloat($("#gross_amt").val());
            var txn_amt=parseFloat($("#txn_amt").val());
            // alert(gross_amt)
            if (isNaN(txn_amt)) {
              txn_amt = 0;
            }
            if(isNaN(gross_amt)){
              gross_amt = 0;
            }
            if(isNaN(other_tcs)){
              other_tcs = 0;
            }
            var other_amount_value = (gross_amt + txn_amt)*(other_tcs/100);
            // alert(other_amount_value);
            $("#other_amt_tcs").val(other_amount_value.toFixed(2));

            var finaltotal = (gross_amt)+(other_amount_value)+(txn_amt);
            $("#total_value").val(finaltotal.toFixed(2));
           });

          $("form").submit(function (e) {
             var c=0;
             var noOFBales = $('#noOFBales').val();
            $( ".countbales" ).each(function(index) {
              c=parseInt(c)+parseInt(this.value);
            });
            if (parseInt(noOFBales)!= parseInt(c)) {

              e.preventDefault();
              alert('Selected Lot Bales And Entered No. Of Bales Not Match')
              return false;

            }
          }); 

          //Add Lot And Bales
          
          var field_wrapper_dyamic = $('.field_wrapper_dyamic'); 
           
          $('.add_button').click(function(){

            var noOFBales = $('#noOFBales').val();
                var c=0;

                $( ".countbales" ).each(function(index) {
                   c=parseInt(c)+parseInt(this.value);
                  });
                
                GlobleNoOfBales=parseInt(noOFBales)-parseInt(c);

            if (noOFBales!='' || noOFBales===0) {
              var dropdownLotNo = $('#lot_dropdown :selected').val();

              if(dropdownLotNo!='')
              {

                var dropdownLotNo = $('#lot_dropdown :selected').val();
                
                if(parseInt(countSelectedBales)>=parseInt(noOFBales))
                {
                  alert('Selected Bales Total Should Not Be Greater Than No. Of Bales');
                  $('#lot_dropdown').prop('selectedIndex',0);
                }else{

                  var value=$('#conf_no :selected').val();
                  var data=value.split("/");
                  var sel_lot_no = dropdownLotNo;
                 
                /*  if (parseInt(GlobleNoOfBales)<100) 
                  {
                      
                      var balesfieldHTML= '<span class="row removefiled"><div class="form-group col-md-4"><input type="text" class="form-control" readonly="" name="lot_no[]" value="'+dropdownLotNo+'"></div><div class="form-group col-md-4"><input type="text" class="form-control countbales" name="lot_bales[]" placeholder=" Bales" value="'+GlobleNoOfBales+'"></div><div class="col-md-4"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div></span>';

                        $(field_wrapper_dyamic).append(balesfieldHTML);

                        $('#lot_dropdown option[value="'+sel_lot_no+'"]').get(0).remove();
                        $('#lot_dropdown').prop('selectedIndex',0);
                        countSelectedBales+=parseInt(GlobleNoOfBales);
                        GlobleNoOfBales = parseInt(GlobleNoOfBales)-parseInt(GlobleNoOfBales);

                  }else{*/

                      var balesfieldHTML= '<span class="row removefiled"><div class="form-group col-md-4"><input type="text" class="form-control" readonly="" name="lot_no[]" value="'+dropdownLotNo+'"></div><div class="form-group col-md-4"><input type="text" class="form-control countbales" name="lot_bales[]" placeholder=" Bales" value="'+GlobleBales[dropdownLotNo]+'" onkeyup="checkBales()"></div><div class="col-md-4"><a href="javascript:void(0);" class="btn btn-danger remove_btn">-</a></div></span>';

                        $(field_wrapper_dyamic).append(balesfieldHTML);

                        $('#lot_dropdown option[value="'+sel_lot_no+'"]').get(0).remove();
                        $('#lot_dropdown').prop('selectedIndex',0);
                        countSelectedBales+=parseInt(GlobleBales[dropdownLotNo]);

                        GlobleNoOfBales = parseInt(GlobleNoOfBales)-parseInt(GlobleBales[dropdownLotNo]);


                  /*}*/

                   

                }
                $('span.error-keyup-10').hide();
                 $(':input[type="submit"]').prop('disabled', false);

                

                 var noOFBales=$('#noOFBales').val();
                  var countbales=0;

                  $( ".countbales" ).each(function(index) {
                   countbales=parseInt(countbales)+parseInt(this.value);
                  });


                  console.log(noOFBales);


                 if(parseInt(noOFBales)!=parseInt(countbales))
                 {
                  $('.countbales').last().after('<span class="error error-keyup-10 text-danger">No Of Bales Is not eual to selected bales.</span>'); 
                  $(':input[type="submit"]').prop('disabled', true);
                 }




                
              }
              else
              {
                alert('Please Select Lot No.');
              }

            }else{

              alert('Please Enter Bales.');

            }
          });

          $("form").submit(function (e) {
            var c=0;
            var noOFBales = $('#noOFBales').val();
            $( ".countbales" ).each(function(index) {
              c=parseInt(c)+parseInt(this.value);
            });
            if (parseInt(noOFBales)!= parseInt(c)) {

              e.preventDefault();
              alert('Selected Lot Bales And Entered No. Of Bales Not Match')
              return false;

            }
          });
          
          $(field_wrapper_dyamic).on('click', '.remove_btn', function(e){
              e.preventDefault();

             var lotno = $(this).parent().parent().find('input[name="lot_no[]"]').val();
             var bales = $(this).parent().parent().find('input[name="lot_bales[]"]').val();
             // alert(countSelectedBales)
             // alert(bales)
             // alert(GlobleNoOfBales)

             countSelectedBales=parseInt(countSelectedBales)-parseInt(GlobleBales[lotno]);
              GlobleNoOfBales = parseInt(GlobleNoOfBales)+parseInt(GlobleBales[lotno]);
              
              //GlobleBales[lotno]=bales;


             $('#lot_dropdown').append( '<option value="'+lotno+'">'+lotno+'</option>' );

             $("#lot_dropdown option").sort(function(a, b) {
                  a = a.value;
                  b = b.value;
                  return a-b;
              }).appendTo('#lot_dropdown');

              $('#lot_dropdown').prop('selectedIndex',0);
              $(this).parent('div').parent('span').remove(); 

              checkBales();

              
          });

           $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });







        });
      
        function LoadFunction() {
          // reset 
          lotGloble = '';
          GlobleBales = new Array();
          countSelectedBales = parseInt("<?php echo $row['noOFBales'] ?>");

          GlobleNoOfBales = parseInt("<?php echo $row['noOFBales'] ?>");
          var value=$('#conf_no :selected').val();;
          var data=value.split("/");
          $.ajax({
              type: "POST",
              url: 'getData.php',
              data: {conf_no:data[1],table:data[2]},
              success: function(response)
              {
                  var jsonData = JSON.parse(response);
                  console.log(jsonData);
                   //set dropdown
                  var lotdata=JSON.parse(jsonData['lot_no']);
                  lotGloble = lotdata;
                    $('#lot_dropdown').find('option').not(':first').remove();
                      for (var i=0;i<lotdata.length;i++)
                      {
                       $('<option/>').val(lotdata[i]).html(lotdata[i]).appendTo('#lot_dropdown');
                      }

                    // set bales Glo
                    balesArr=JSON.parse(jsonData['lot_bales']);

                    for(i=0; i<=lotdata.length; i++)
                    {
                      GlobleBales[lotdata[i]]=balesArr[i];
                     // console.log('lot '+lotdata[i]+' bales '+balesArr[i]);
                    }
                   console.log(GlobleBales);



                   var getAvlBales = GlobleBales.reduce((a, b) => parseInt(a) + parseInt(b), 0);
                   
                   var curBales=$('#noOFBales').val();
                  
                   $('#avl_bales').val(parseInt(getAvlBales)+parseInt(curBales))






              }
          });
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
  function decimalValidate(evt, element) {

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

  function checkBales() {
      $('span.error-keyup-10').hide();

      var noBales = $('#noOFBales').val();
      var countBales = 0;
      $( ".countbales" ).each(function( index ) {
        countBales = parseInt(countBales) + parseInt(this.value);

      });
      if (parseInt(noBales) != parseInt(countBales)) {


        $('.countbales').last().after('<span class="error error-keyup-10 text-danger">No Of Bales Is not eual to selected bales.</span>'); 
        $("#submit").attr("disabled", true);
      }else{

        $('span.error-keyup-10').hide();
        $("#submit").attr("disabled", false);
      }
}

        
      


    </script>
    
    
  </body>
</html>

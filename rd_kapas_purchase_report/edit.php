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
  $getFirm=$_SESSION["pur_financial_year"];
  $year_array=explode("/",$getFirm);

  $dir = "/file_storage/"; // file storage in root folder of site
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from rd_kapas_report where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){

    $report_date='';
    if($_POST['report_date']!='')
    {
        $report_date = DateTime::createFromFormat('d/m/Y', $_POST['report_date']);
        $report_date=$report_date->format('Y-m-d');  
    }

		

    $invoice_no = $_POST['invoice_no'];
    $external_party = $_POST['external_party'];
    
		$product = $_POST['product'];
    $broker = $_POST['broker'];
    $basic_amt = $_POST['basic_amt'];
    $tax = $_POST['tax'];
    $tax_amt = $_POST['tax_amt'];
    $tcs = $_POST['tcs'];
    $tcs_amt = $_POST['tcs_amt'];
    $gd_value = $_POST['gd_value'];
    
    $net_amt = $_POST['net_amt'];

     $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


    include('../global_function.php'); 
    $data=getFileStoragePath("rd_kapas_purchase_report",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path

    $imgArr=array();
    $filecount = count($_FILES['docimg']['tmp_name']);  
    foreach ($_FILES['docimg']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['docimg']['name'][$key];

      $imgTmp = $_FILES['docimg']['tmp_name'][$key];
      $imgSize = $_FILES['docimg']['size'][$key];

  
      if(!empty($img)){
        
        $imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        $allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $img = time().'_'.rand(1000,9999).'.'.$imgExt;
        // array_push($imgArr,$img);
        $imgArr[$key] = $img;
        if(in_array($imgExt, $allowExt)){

          if($imgSize < 5000000){
            move_uploaded_file($imgTmp ,$root_path.$img);
          }else{
            $errorMsg = 'Image too large';
            echo $errorMsg;
          }
        }else{
          $errorMsg = 'Please select a valid image';
          echo $errorMsg;
        }

      }else{
        $imgArr[$key] = '';
      }
    }
    
    $finalimg = array();
    if(count($imgArr) > 0){
      foreach($imgArr as $k => $v){
        if($v == "" && isset($_POST['oldfile'][$k])){
          $finalimg[] = $_POST['oldfile'][$k];
        }else{
          if($v!='' && $v!=null)
          {
            $finalimg[] = $store_path.$v;
          }
        }
      }
    }


    $img_title = $_POST['img_title'];
    $imgTitle = implode(',', $img_title);
    $imgStore = implode(',', $finalimg);
    
    $OldDBImg = explode(',', $row['docimg']); 
    $result1=array_diff($OldDBImg,$finalimg);
    foreach ($result1 as  $item) 
    {
          if($item!='')
          {
            $item=trim($item);             
            unlink($unlink_path.$item); 
          }  
    }
    
    
		if(!isset($errorMsg))
    {
			$sql = "update rd_kapas_report
									set report_date = '".$report_date."',
										invoice_no = '".$invoice_no."',
                    external_party = '".$external_party."',
                    
                    product = '".$product."',
                    broker = '".$broker."',
                    basic_amt = '".$basic_amt."',
                    tax = '".$tax."',
                    tax_amt = '".$tax_amt."',
                    tcs = '".$tcs."',
                    tcs_amt = '".$tcs_amt."',
                    gd_value = '".$gd_value."',
                    
                    net_amt = '".$net_amt."',
                    docimg = '".$imgStore."',
                    img_title = '".$imgTitle."',
                    username = '".$username."',
                    updated_at = '".$timestamp."'
                          

					where id=".$id;
			$result = mysqli_query($conn, $sql);
			if($result)
      {
				$successMsg = 'New record updated successfully';


          //update RD Kapas Purchase Payment Record (If Exits)
        $sql_get="select * from rd_kapas_payment where rd_kapas_report_id='".$id."'";
        $result_get=mysqli_query($conn, $sql_get);
        if(mysqli_num_rows($result_get)>0)
        {
            foreach ($result_get as $row) 
            {

              $dbt_amt=0;
              if($row['debit_amt']!='')
              {
                $dbt_amt=$row['debit_amt'];
              }

              $party_tds_amt=0;
              if($row['party_tds_amt']!='')
              {
                $party_tds_amt=$row['party_tds_amt'];
              }



              $net_amt=$gd_value-$dbt_amt-$party_tds_amt;
              $net_amt=round($net_amt);


              $dynamic_field1 = json_decode($row['dynamic_field']);
              $dynamic_AmtCount = 0;
              foreach ($dynamic_field1 as $key => $value) {
                $dynamic_AmtCount += (int)$value->amt;
              }


              $bill2billDynamicAmount=0;
              if(isset($row['bill2bill_dynamic_data']) && $row['bill2bill_dynamic_data']!='')
              {
                $b2bArr = json_decode($row['bill2bill_dynamic_data'],true);                      
                foreach ($b2bArr as $key => $value) {
                  if($value['b2b_amount']!='')
                  {
                    $bill2billDynamicAmount += $value['b2b_amount'];
                  }
                  
                }
              }

              $newPayAmt=$net_amt-$dynamic_AmtCount-$bill2billDynamicAmount;

             $sql_rd_pay = "update rd_kapas_payment set
                    invoice_no = '".$invoice_no."',
                    party = '".$external_party."',
                    goods_value = '".$gd_value."',
                   
                    net_amt = '".$net_amt."',
                    pay_amt= '".$newPayAmt."',
                    tax_amt = '".$tax_amt."',
                    tcs_amt = '".$tcs_amt."',
                    report_date = '".$report_date."'
                      
                    where id=".$row['id'];

                
                
                $result_rd_pay = mysqli_query($conn, $sql_rd_pay);
                if($result)
                {
                  $successMsg = 'New record updated successfully';
                }
            }
            
        }

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
    <title>Edit RD Kapas Report</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit RD Kapas Report</span></a>
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
         $sqlLastChange="select username,updated_at from rd_kapas_report where id='".$row['id']."'";

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
          
            <div class="card edit_RD_kapas_pur_report">
              <div class="card-header">
                Update RD Kapas Report
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  <div class="row">  
                    <div class="form-group col-md-2">
                      <label for="report_date">Report Date</label>
                      <input type="text" class="form-control datepicker" name="report_date" autocomplete="off"  value="<?php echo date("d/m/Y", strtotime($row['report_date'])); ?>">
                    </div>

                    <div class="form-group col-md-2">
                      <label for="invoice_no">Invoice No</label>
                      <input type="text" class="form-control" name="invoice_no" id="invoice_no"  value="<?php echo $row['invoice_no']; ?>" >
                    </div>
                    <div class="form-group col-md-4">
                      <label for="external_party">Select  Party</label>
                      <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="external_party" data-live-search="true" class="form-control searchDropdown" id="ext_party" onchange="get_GSTNO(this.value)">
                                <?php                   
                                    foreach ($conn->query($sql) as $result) {
                                      $isFirmSelected ="";
                                      if ($result['id'] == $row['external_party']) {
                                          $isFirmSelected = "selected";
                                        }
                                        echo "<option  value='" .$result['id']."'".$isFirmSelected.">" .$result['partyname']. "</option>";  

                                    }
                                ?>
                            </select>
                    </div>
                    <div class="form-group col-md-4">
                      <label for="party">GST No.:</label>
                      <input type="text" class="form-control set-gst-no" placeholder="GST No" readonly="readonly">
                  </div>
                  </div>

                  <div class="row">
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
                            }?>
                      <input type="text" class="form-control" readonly value="<?php echo $pname; ?>">
                     
                    </div>
                    <div class="form-group col-md-4">
                      <label for="product">Select Product</label>
                        <?php
                            $sql = "select * from products";
                            $result = mysqli_query($conn, $sql);
                            
                        ?>
                      
                        <select name="product" data-live-search="true" class="form-control searchDropdown">
                            <?php                   
                              foreach ($conn->query($sql) as $result) {
                                $isProdSelected ="";
                                  if ($result['id'] == $row['product']) {
                                      $isProdSelected = "selected";
                                    }
                                echo "<option  value='" .$result['id']."'".$isProdSelected.">" .$result['prod_name']. "</option>";  

                              }
                              ?>
                        </select>
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
                                  $isBrokerSelected ="";
                                      if ($result['id'] == $row['broker']) {
                                          $isBrokerSelected = "selected";
                                        }
                                  echo "<option  value='" .$result['id']."'".$isBrokerSelected.">" .$result['name']. "</option>";  

                                }
                                ?>
                            </select>
                    </div>
                  </div>

                  <div class="row">

                    <div class="form-group col-md-4">
                      <label for="basic_amt">Basic Amt</label>
                      <input type="text" class="form-control basic" name="basic_amt" placeholder="Enter Email" onkeypress="return NumericValidate(event,this)" value="<?php echo $row['basic_amt']; ?>" pattern="[0-9]+">
                    </div>

                     <div class="form-group col-md-4">
                      <label for="tax">Tax (In Percentage)</label>
                      <input type="text" class="form-control tax" name="tax" placeholder="Enter Tax" onkeypress="return NumericValidate(event,this)" pattern="[0-9]+" value="<?php echo $row['tax']; ?>">
                  </div>

                   <div class="form-group col-md-4">
                      <label for="tax_amt">Tax Amount</label>
                      <input type="text" class="form-control tax_amt" name="tax_amt" id="tax_amt" value="<?php echo $row['tax_amt']; ?>" readonly>
                  </div>

                  

                    <div class="form-group col-md-4">
                      <label for="tcs">TCS</label>
                      <input type="text" class="form-control tcs" name="tcs" placeholder="Enter Email" onkeypress="return NumericValidate(event,this)" value="<?php echo $row['tcs']; ?>" pattern="[0-9]+">
                    </div>
                
                        <div class="form-group col-md-4">
                      <label for="tcs_amt">TCS Amount</label>
                      <input type="text" id="result" class="form-control tcs_amt" name="tcs_amt" placeholder="Enter Email" value="<?php echo $row['tcs_amt']; ?>" readonly>
                    </div>



                        <div class="form-group col-md-4">
                      <label for="gd_value">Goods Value</label>
                      <input type="text" id="gd_amt" class="form-control gd_value" name="gd_value" placeholder="Enter Email" value="<?php echo $row['gd_value']; ?>" readonly>
                    </div>

                   
                 
                            <div class="form-group col-md-4">
                      <label for="net_amt">Net Amount</label>
                      <input type="text" id="net_amt" class="form-control net_amt bold" name="net_amt" placeholder="Enter Email" value="<?php echo $row['net_amt']; ?>" readonly>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script type="text/javascript">

      var delCount=0;

        $(document).ready(function () {

          
          //get selected dropdown value id
        if($('.edit_RD_kapas_pur_report').is(':visible')){
          var selected_party_id= $('#ext_party :selected').val();
          getSelectedPartyId_GSTNo(selected_party_id);
        }


           $(".datepicker").datepicker({dateFormat:'dd/mm/yy',
              changeMonth: true,
              changeYear: true,
              maxDate: new Date('<?php echo($year_array[1]) ?>'),
              minDate: new Date('<?php echo($year_array[0]) ?>')
            });
           $(".datepicker").keydown(false);

            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;

              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docimg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docimg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

            

            $('.imgcount').last().after(varietyfieldHTML);

            });



            $('#invoice_no').on('input', function() {
             
                      $('span.error-keyup-1').hide();

                        checkInvoiceNo();
                    
                });

             $('#ext_party').on('change', function() {
             
                      $('span.error-keyup-1').hide();

                        checkInvoiceNo();
                    
             });

        function checkInvoiceNo()
        {

          var getInvoice="<?php echo $row['invoice_no'] ?>";
          var getExtParty="<?php echo $row['external_party'] ?>";
          
          var invoice_no=$('#invoice_no').val();
          var ext_party=$('#ext_party :selected').val();


          if(getInvoice!=invoice_no || getExtParty!=ext_party)
          {
               $.ajax({
                type: "POST",
                url: 'check_invoiceAJAX.php',
                data: {
                  invoice_no:invoice_no,
                  ext_party:ext_party,
                },
                success: function(response)
                {
                    var jsonData = JSON.parse(response);
                    console.log(jsonData.name_exist);

                   if(jsonData.name_exist)
                   {
                     $('#invoice_no').after('<span class="error error-keyup-1 text-danger">Already Exist.</span>');
                     $(':input[type="submit"]').prop('disabled', true);
                   }
                   else
                   {
                    $('span.error-keyup-1').hide();
                    $(':input[type="submit"]').prop('disabled', false);
                   }
                    
               }
              });
          }
               
        }

        });
      function removeImg(e,index) {
        $(e).parent('div').parent('div').remove(); 
        delCount=delCount+1;
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


</script>
<script type="text/javascript">


        $(document).ready(function(){
 $('input[type="text"]').keyup(function () 
  {
  var basic_amt = parseFloat($('.basic').val());
  var tax_pr = parseFloat($('.tax').val());
  var tcs_pr = parseFloat($('.tcs').val());
  var tcs_amt = parseFloat($('.tcs_amt').val());
  var debit_amt = parseFloat($('.dbt_amt').val());
  var val6 = parseFloat($('.gd_value').val());
  var tax_amt = parseFloat($('.tax_amt').val());

  if (isNaN(basic_amt)) {
    basic_amt = 0;
  }
  if (isNaN(tax_amt)) {
    tax_amt = 0;
  }
  if (isNaN(tcs_pr)) {
    tcs_pr = 0;
  }
  if (isNaN(tcs_amt)) {
    tcs_amt = 0;
  }
  if (isNaN(debit_amt)) {
    debit_amt = 0;
  }
  if (isNaN(val6)) {
    val6 = 0;
  }
 

          var tax_amt = (basic_amt) * tax_pr/100;
          var tcs_amt = (basic_amt+tax_amt) * tcs_pr/100;
          var mnt = (basic_amt)+(tax_amt)+(tcs_amt);
          var net = (mnt)-(debit_amt);

           if (isNaN(tax_amt)) {
              tax_amt = 0;
            }
             if (isNaN(tcs_amt)) {
              tcs_amt = 0;
            }
             if (isNaN(mnt)) {
              mnt = 0;
            }
             if (isNaN(net)) {
              net = 0;
            }



          $("input#result").val(tcs_amt.toFixed(2));
          $("input#tax_amt").val(tax_amt.toFixed(2));
          $("input#gd_amt").val(mnt.toFixed(2));
          $("input#net_amt").val(net.toFixed(2)); 







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

<script>

  function getSelectedPartyId_GSTNo(party_id){
      $.ajax({
                type: "POST",
                url: 'get_GSTNO.php',
                data: {party_id:party_id},
                success: function(response)
                {
                    var jsonData = JSON.parse(response);
                //   console.log(jsonData);

                  if(jsonData.status==true){

                      if(jsonData.gstin_data!=''){
                          $('.set-gst-no').val(jsonData.gstin_data);
                    }else{
                      $('.set-gst-no').val(''); 
                    }
                  }else{
                    $('.set-gst-no').val(''); 
                  }
              }
          });
  }


  function get_GSTNO(party_id){
    
        $.ajax({
            type: "POST",
            url: 'get_GSTNO.php',
            data: {party_id:party_id},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
             //   console.log(jsonData);

              if(jsonData.status==true){

                  if(jsonData.gstin_data!=''){
                      $('.set-gst-no').val(jsonData.gstin_data);
                }else{
                  $('.set-gst-no').val(''); 
                }
              }else{
                $('.set-gst-no').val(''); 
              }
           }
       });
  }
</script>
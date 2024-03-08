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
$getFirm=$_SESSION["sales_conf_firm"];
$getFirmID=$_SESSION["sales_conf_firm_id"];

$getYear=$_SESSION['sales_conf_financial_year'];

$year_array=explode("/",$getYear);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from sales_conf_split where id=".$id;
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
    <title>View Sales Confirmation Split</title>
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> View Sales Confirmation Split</span></a>

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
           $sqlLastChange="select username,updated_at from sales_conf_split where id='".$row['id']."'";

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
              <div class="card-header">Sales Confirmation Split Confirmation Report</div>
              <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                        <div class="form-group">
                        <label for="conf_no">Sales Confirmation No</label>
                          
                        <input type="text" class="form-control" value="<?php echo $row['conf_no'] ?>" name="" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group">
                        <label for="conf_split_no">Sales Split Confirmation No</label>

                        <input type="text" class="form-control" name="conf_split_no" id="conf_split_no"   value="<?php echo $row['conf_split_no'] ?>" readonly="" >
                        </div>
                    </div>

                    <div class="form-group col-md-4">
                      <label for="conf_type">Sales Confirmation  Type</label>
                      <select name="conf_type" class="form-control" disabled="">
                        <option value="" disabled selected>Select</option>
                        <option value="0"<?php if ($row['conf_type'] == 0) {
                          echo "selected";
                        } ?>>Original</option>
                        <option value="1" <?php if ($row['conf_type'] == 1) {
                          echo "selected";
                        } ?>>Revised</option>
                        <option value="2" <?php if ($row['conf_type'] == 2) {
                          echo "selected";
                        } ?>>Cancel</option>
                      </select>
                    </div>
                    
                    
                    
                  </div>


                  <div class="row">

                    <div class="form-group col-md-4">


                      <label for="split_party_name">Split Party Name</label>
                          <?php 

                            $Split_party = "select * from external_party where id='".$row['split_party_name']."'";
                          $Split_party_result = mysqli_query($conn, $Split_party);
                            $Split_party_row = mysqli_fetch_assoc($Split_party_result);

                          ?>
                        <input type="text" class="form-control" name="split_party_name"
                        placeholder="Split Party Name" value="<?php echo $Split_party_row['partyname'] ?>"readonly>

                    </div>

                      <div class="form-group col-md-4">
                      <label for="shipping_ext_party_id">Shipping To</label>
                     
                      <?php
                            $shipping_party='';
                            $sql_ship = "select * from external_party where id='".$row['shipping_ext_party_id']."'";
                            $result_ship = mysqli_query($conn, $sql_ship);
                            $rowcount_ship=mysqli_num_rows($result_ship);

                          if($rowcount_ship>0)
                          {
                            $row_ship=mysqli_fetch_assoc($result_ship);
                            $shipping_party=$row_ship['partyname'];
                          }


                          ?>  


                           <input type="text" class="form-control" value="<?php echo $shipping_party ?>"   readonly>                    
                           
                    </div>



                     <div class="form-group col-md-4">
                        <label for="conf_split_date">Sales Confirmation Split Date :</label>
                        <input type="text" class="form-control datepicker" placeholder="Sales Confirmation Split Date" name="conf_split_date" autocomplete="off" value="<?php echo date('d/m/Y', strtotime($row['conf_split_date'])) ?>" readonly>
                        </div>

                        <div class="form-group col-md-4">
                        <label for="firm">Firm</label>
                        <input type="text" class="form-control" placeholder="Firm" value="<?php echo $_SESSION['sales_conf_firm'] ?>"  readonly>
                        <input type="hidden" name="firm" value="<?php echo $_SESSION['sales_conf_firm_id'] ?>">
                        </div>

    
                 
                    <?php 

                        $party = "select * from external_party where id='".$row['external_party']."'";
                        $partyresult = mysqli_query($conn, $party);
                        $partyrow = mysqli_fetch_assoc($partyresult);

                      ?>
                      <div class="form-group col-md-4">
                                            <label for="external_party">External Party</label>
                      <input type="text" name="" class="form-control" value="<?php echo $partyrow['partyname'] ?>" readonly>
                    </div>

                  <div class="form-group col-md-4">
                    <?php 

                        $broker = "select * from broker where id='".$row['broker']."'";
                        $broker_result = mysqli_query($conn, $broker);
                        $broker_row = mysqli_fetch_assoc($broker_result);
                    ?>
                    <label for="broker">Broker</label>
                    <input type="text" name="" class="form-control" value="<?php echo $broker_row['name'] ?>" readonly>
                    
                    </div>

                  <div class="form-group col-md-4">
                      <label for="trans_ins">Transit Insurance</label>
                        
                    <input type="text" name="" class="form-control" value="<?php echo $row['trans_ins'] ?>" readonly>                
                        
                    </div>


                 
                    <div class="form-group col-md-4">
                      <?php

                          $products = "select * from products where id='".$row['product']."'";
                          $products_result = mysqli_query($conn, $products);
                            $products_row = mysqli_fetch_assoc($products_result);

                       ?>
                      <label for="product"> Product</label>
                      <input type="text" name="" class="form-control" value="<?php echo $products_row['prod_name']; ?>" readonly>
                    </div>
                    </div>

                    
            
             

                    <h4>Quality Specification</h4>

                    <div class="row">                    

                  <div class="form-group col-md-4">
                      <label for="length">Product Length</label>
                      <input type="text" class="form-control" name="length"  placeholder="Product Length" value="<?php echo $row['length'] ?>" readonly onkeypress="return NumericValidate(event)">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="strength">Product Strength</label>
                      <input type="text" class="form-control" name="strength" value="<?php echo $row['strength'] ?>" placeholder="Product Strength" onkeypress="return NumericValidate(event)" readonly>
                  </div>

                   <div class="form-group col-md-4">
                      <label for="mic">Product MIC</label>
                      <input type="text" class="form-control" name="mic"  placeholder="Product Strength" value="<?php echo $row['mic'] ?>" onkeypress="return NumericValidate(event)" readonly>
                  </div>

                    <div class="form-group col-md-4">
                      <label for="rd">Product RD</label>
                      <input type="text" class="form-control" name="rd"  placeholder="Product Strength" value="<?php echo $row['rd'] ?>" onkeypress="return NumericValidate(event)" readonly>
                  </div>


                  <div class="form-group col-md-4">
                      <label for="trash">Product Trash</label>
                      <input type="text" class="form-control" name="trash"  placeholder="Product Strength" value="<?php echo $row['trash'] ?>" onkeypress="return NumericValidate(event)" readonly>
                  </div>

                  <div class="form-group col-md-4">
                      <label for="moi">Product Moisture</label>
                      <input type="text" class="form-control" name="moi" readonly=""  placeholder="Product moi" value="<?php echo $row['moi'] ?>" id="moi" onkeypress="return NumericValidate(event)">
                  </div>

                    </div>


                  <p>Select Tax Type:</p>


                  <div class="row">
                  <div class="form-group col-md-4">
                          <input type="radio" name="taxtype" id="taxtype1" value="sgst" <?php if($row['tax_type']=='sgst'){echo 'checked';} ?> disabled>
                      <label for="taxtype1">SGST</label>
                      <br>
                      <input type="radio" name="taxtype" id="taxtype2" value="igst" <?php if($row['tax_type']=='igst'){echo 'checked';} ?> disabled>
                      <label for="taxtype2">IGST</label>
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="sgst">SGST</label>
                      <input type="text" class="form-control" name="sgst"  placeholder="Product sgst" value="<?php echo $row['sgst'] ?>" readonly>
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="cgst">CGST</label>
                      <input type="text" class="form-control" name="cgst"  placeholder="Product cgst" value="<?php echo $row['cgst'] ?>" readonly>
                  </div>
                  <div class="form-group type_igst d-none col-md-6">
                      <label for="igst">IGST</label>
                      <input type="text" class="form-control" name="igst"  placeholder="Product igst" value="" readonly="">
                  </div>

                  </div>

                  <div class="row">
                  

                  <div class="form-group col-md-4">
                      <label for="cont_quantity">No Of Balse</label>
                      <input type="text" class="form-control" name="no_of_bales" id="noOFBales" onkeyup="$('input[name=no_lot]').val(value/100);" placeholder="Contracted Quantity" value="<?php echo $row['no_of_bales'] ?>" readonly>
                      
                  </div>

                 

                 
                 </div>
                  



                 <div class="row">

                   
                    
                    <div class="form-group col-md-4">
                      <label for="lot_no">Lot No</label>
                      <div id="lot_section">
                      <?php foreach (json_decode($row['lot_no']) as $value) 
                      {
                       ?>
                        <input type="text" placeholder="Lot No" class="form-control mb-2 lot_no" name="lot_no[]" value="<?php echo $value; ?>" readonly>
                       <?php
                      } ?>
                      </div>
                     
                    </div>

                    <?php
                    if($row['lot_bales']!='')
                      {
                    ?>
                    <div class="form-group col-md-4">
                      <label for="lot_bales">Bales</label>
                      <div id="bales">
                      <?php 
                      $balesArr=json_decode($row['lot_bales']);
                      foreach ($balesArr as $value) 
                      {
                       ?>
                        <input type="text" placeholder="lot_bales" class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $value; ?>" readonly>
                       <?php
                      } 
                      ?>
                      </div>
                     
                    </div>

                  <?php } ?>




                    </div>  

                    <div class="row">
                        
                      <div class="form-group col-md-6">
                        <label for="press_no">Press no</label>
                        <input type="text" placeholder="Press No." class="form-control" name="press_no" value="<?php echo $row['press_no'] ?>" readonly>
                      </div>


                        <?php 

                        $sql_q = "select * from product_sub_items where id='".$row['variety']."'";
                          $result_q = mysqli_query($conn, $sql_q);

                          $row_q = mysqli_fetch_assoc($result_q);

                          $pvariety='';
                          if(isset($row_q))
                          {
                            $pvariety=$row_q['value'];
                          }
                          ?> 


                      <div class="form-group col-md-6">
                        <label for="variety">Product Variety</label>
                          
                          <input type="text" name="" class="form-control" value="<?php echo $pvariety; ?>" readonly>

                        </div> 

                          </div>   


                            <?php 

                        $sql_q = "select * from product_sub_items where id='".$row['sub_variety']."'";
                          $result_q = mysqli_query($conn, $sql_q);

                          $row_q = mysqli_fetch_assoc($result_q);

                          $sub_variety='';
                          if(isset($row_q))
                          {
                            $sub_variety=$row_q['value'];
                          }
                          ?>  



                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="sub_variety">Sub Type Of Variety</label>
                                   
                           <input type="text" name="" class="form-control" value="<?php echo $sub_variety; ?>" readonly>

                        </div> 

                            <div class="form-group col-md-6">
                          <label for="price">Price</label>
                          <input type="text" name="price" class="form-control" value="<?php echo $row['price'] ?>" placeholder="enter price" readonly> 
                        </div>

                         <div class="form-group col-md-5">
                      <label for="spl_rmrk">Special Remark</label>
                      <textarea class="form-control" readonly="" name="spl_rmrk" id="w3review" rows="4" cols="60"><?php echo $row['spl_rmrk'] ?></textarea>
                  </div>

                      </div>
                    


                  <div class="form-group col-md-12">
                      <label for="bill_inst">Bill Instruction</label>
                     <?php echo $row['bill_inst'] ?>
                  </div>


                 

                  </div>

              </div>
            
          </div>
        </div>
      </div>

</div>
</div>
   
  

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
         
  </body>
</html>

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
    <title>Edit Sales Confirmation Split</title>
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



        <link rel="stylesheet" href="../PHPLibraries/richtexteditor/rte_theme_default.css" />
    <script type="text/javascript" src="../PHPLibraries/richtexteditor/rte.js"></script>
    <script type="text/javascript" src='../PHPLibraries/richtexteditor/plugins/all_plugins.js'></script>




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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Sales Confirmation Split</span></a>
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
              <div class="card-header">Edit Sales Confirmation Split Report</div>
              <div class="card-body">
                <form id="main_form"  class="" action="update.php" method="post" enctype="multipart/form-data">

             
                <input type="hidden" name="page_no" value="<?php echo $page ?>">




                  <div class="row">
                    <div class="col-md-4">
                      <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                        <div class="form-group">
                        <label for="conf_no">Sales Confirmation No</label>
                          <?php
                            $getDates=explode('/', $_SESSION["sales_conf_financial_year"]);
                          $start_date=$getDates[0];
                          $end_date=$getDates[1];
                          $firm_id=$_SESSION["sales_conf_firm_id"];
                          $fyear_id=$_SESSION["sales_financial_year_id"];

                            $sql = "select s.*,p.party_name from seller_conf s, party p where s.firm=p.id AND s.financial_year_id='".$fyear_id."' AND s.firm='".$firm_id."' AND s.conf_type!='2'";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="conf_no" id="conf_no" class="form-control">
                            <option value="" disabled selected>Select</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {

                                 //External Party
                                $ext_name='';
                                $Ex_party = "select * from external_party where id='".$result['external_party']."'";
                                $Ex_partyresult = mysqli_query($conn, $Ex_party);
                                if(mysqli_num_rows($Ex_partyresult)>0)
                                {
                                  $Ex_partyrow = mysqli_fetch_assoc($Ex_partyresult);
                                  $ext_name=$Ex_partyrow['partyname'];
                                }

                                 $used_bales=0;
                                //check in current table (sales conf split) if any record created with this..
                                $sql2="SELECT IFNULL(SUM(no_of_bales), 0) as used_bales FROM sales_conf_split WHERE conf_no='".$result['sales_conf']."'";
                                $result2 = mysqli_query($conn, $sql2);
                                $rowScs2=$result2->fetch_assoc();
                                $used_bales+=(int)$rowScs2['used_bales'];

                                //check in sales report
                                $sqlSR="select IFNULL(SUM(noOFBales), 0) as used_bales from sales_report where conf_no='".$result['sales_conf']."'";
                                $resultSR = mysqli_query($conn, $sqlSR);
                                $rowSR=$resultSR->fetch_assoc();
                                $used_bales+=(int)$rowSR['used_bales'];


                                
                                if($result['sales_conf']==$row['conf_no'])
                                {
                                  echo "<option  value='".$result['id'].'/'.$result['sales_conf']."' selected>".$result['sales_conf'].' ('.$ext_name.')'."</option>";
                                }
                                else if($result['cont_quantity']!=$used_bales)
                                {
                                  echo "<option  value='".$result['id'].'/'.$result['sales_conf']."'>".$result['sales_conf'].' ('.$ext_name.')'."</option>";
                                }




                             
                              }
                            ?>                              
                            </select>
                        
                        </div>
                    </div>
                    <div class="col-md-4">

                        <div class="form-group">
                        <label for="conf_split_no">Sales Split Confirmation No</label>

                        <input type="text" class="form-control" name="conf_split_no" id="conf_split_no"   value="<?php echo $row['conf_split_no'] ?>" readonly="" >

                        <input type="hidden" name="old_conf_split_no" value="<?php echo $row['conf_split_no'] ?>">

                        </div>


                    </div>

                    <div class="form-group col-md-4">
                      <label for="conf_type">Sales Confirmation  Type</label>
                      <select name="conf_type" class="form-control">
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
                      <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>
                        
                        <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="split_party_name" data-live-search="true" class="form-control searchDropdown" required>
                            <option value="" disabled selected>Select</option>
                            <?php 


                              foreach ($conn->query($sql) as $result) 
                              {
                                $isExtSelected='';
                                    if($result['id']==$row['split_party_name'])
                                    {
                                        $isExtSelected='selected';
                                    }
                                    echo "<option  value='".$result['id']."'".$isExtSelected.">".$result['partyname']. "</option>";
                              }
                            ?>                              
                            </select>

                    </div>




                    <div class="form-group col-md-4">
                      <label for="shipping_ext_party_id">Shipping To</label>
                     
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select id="shipping_ext_party_id" name="shipping_ext_party_id" data-live-search="true" class="form-control searchDropdown">
                            <option value="" disabled selected>Select</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {

                                if($row['shipping_ext_party_id']==$result['id'])
                                {
                                  echo "<option  value='".$result['id']."' selected>".$result['partyname']. "</option>";
                                }
                                else
                                {
                                   echo "<option  value='".$result['id']."'>".$result['partyname']. "</option>";
                                }

                                    
                              }
                            ?>                              
                            </select>
                    </div>



                     <div class="form-group col-md-4">
                        <label for="conf_split_date">Sales Confirmation Split Date :</label>
                        <input type="text" class="form-control datepicker" placeholder="Sales Confirmation Split Date" name="conf_split_date" autocomplete="off" value="<?php echo date('d/m/Y', strtotime($row['conf_split_date'])) ?>">
                        </div>

                        <div class="form-group col-md-4">
                        <label for="firm">Firm</label>
                        <input type="text" class="form-control" placeholder="Firm" value="<?php echo $_SESSION['sales_conf_firm'] ?>"  readonly>
                        <input type="hidden" name="firm" value="<?php echo $_SESSION['sales_conf_firm_id'] ?>">
                        </div>

    
               
                      <div class="form-group col-md-4">

                      <?php 

                        $party = "select * from external_party where id='".$row['external_party']."'";
                        $partyresult = mysqli_query($conn, $party);
                        $partyrow = mysqli_fetch_assoc($partyresult);

                      ?>
                        <label for="external_party">External Party</label>
                      <input type="text" class="form-control"  id="external_party" 
                        placeholder=" External Party Name" readonly="" readonly="" value="<?php echo $partyrow['partyname'] ?>">
                        <input type="hidden"  name="external_party" id="external_party_id" value="<?php echo $row['external_party'] ?>" placeholder="Broker">
                    </div>

                  <div class="form-group col-md-4">
                    <label for="broker">Broker</label>
                    <?php 

                        $broker = "select * from broker where id='".$row['broker']."'";
                        $broker_result = mysqli_query($conn, $broker);
                        $broker_row = mysqli_fetch_assoc($broker_result);
                    ?>
                      <input type="text" class="form-control"  id="broker" 
                        placeholder=" Broker" readonly="" readonly="" value="<?php echo $broker_row['name'] ?>">
                        <input type="hidden" name="broker"  class="form-control" id="broker_id" value="<?php echo $row['broker'] ?>" placeholder="Broker" >
                    </div>


                  <div class="form-group col-md-4">
                      <label for="trans_ins">Transit Insurance</label>
                          <input type="text" class="form-control" name="trans_ins" id="trans_ins" 
                        placeholder=" Transit Insurance" readonly="" value="<?php echo $row['trans_ins'] ?>">
                    </div>


                
                    <div class="form-group col-md-4">
                      <?php

                          $products = "select * from products where id='".$row['product']."'";
                          $products_result = mysqli_query($conn, $products);
                            $products_row = mysqli_fetch_assoc($products_result);

                       ?>
                      <label for="product"> Product</label>
                      <input type="text" class="form-control" name="product" id="product" 
                        placeholder=" Product" readonly="" value="<?php echo $products_row['prod_name'] ?>">

                      <input type="hidden" name="product" id="product_id" class="form-control" placeholder="Product" value="<?php echo $row['product'] ?>">


                    </div>
                    </div>

                    
            
             

                    <h4>Quality Specification</h4>

                    <div class="row">                    

                  <div class="form-group col-md-4">
                      <label for="length">Product Length</label>
                      <input type="text" class="form-control" name="length" id="product_length"  placeholder="Product Length" onkeypress="return NumericValidate(event)" value="<?php echo $row['length'] ?>">
                  </div>

                  <div class="form-group col-md-4">
                      <label for="strength">Product Strength</label>
                      <input type="text" class="form-control" name="strength" value="<?php echo $row['strength'] ?>" placeholder="Product Strength" onkeypress="return NumericValidate(event)" id="strength" >
                  </div>

                   <div class="form-group col-md-4">
                      <label for="mic">Product MIC</label>
                      <input type="text" class="form-control" name="mic"  placeholder="Product Strength" value="<?php echo $row['mic'] ?>" onkeypress="return NumericValidate(event)" id="mic" >
                  </div>

                    <div class="form-group col-md-4">
                      <label for="rd">Product RD</label>
                      <input type="text" class="form-control" name="rd"  placeholder="Product Strength" value="<?php echo $row['rd'] ?>" onkeypress="return NumericValidate(event)" id="rd" >
                  </div>


                  <div class="form-group col-md-4">
                      <label for="trash">Product Trash</label>
                      <input type="text" class="form-control" name="trash"  placeholder="Product Strength" value="<?php echo $row['trash'] ?>" onkeypress="return NumericValidate(event)" id="trash" >
                  </div>

                  <div class="form-group col-md-4">
                      <label for="moi">Product Moisture</label>
                      <input type="text" class="form-control" name="moi"  placeholder="Product moi" value="<?php echo $row['moi'] ?>" id="moi" onkeypress="return NumericValidate(event)">
                  </div>

                    </div>


                  <p>Select Tax Type:</p>


                  <div class="row">
                  <div class="form-group col-md-4">
                      <input type="radio" name="taxtype" id="taxtype1" value="sgst" <?php if($row['tax_type']=='sgst'){echo 'checked';} ?>>
                      <label for="taxtype1">SGST</label>
                      <br>
                      <input type="radio" name="taxtype" id="taxtype2" value="igst" <?php if($row['tax_type']=='igst'){echo 'checked';} ?>>
                      <label for="taxtype2">IGST</label>
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="sgst">SGST</label>
                      <input type="text" class="form-control" name="sgst"  placeholder="Product sgst" value="<?php echo $row['sgst'] ?>" id="sgst">
                  </div>

                  <div class="form-group type_sgst col-md-4">
                      <label for="cgst">CGST</label>
                      <input type="text" class="form-control" name="cgst"  placeholder="Product cgst" value="<?php echo $row['cgst'] ?>" id="cgst">
                  </div>
                  <div class="form-group type_igst d-none col-md-6">
                      <label for="igst">IGST</label>
                      <input type="text" class="form-control" name="igst"  placeholder="Product igst" value="<?php echo $row['igst'] ?>" id="igst">
                  </div>

                  </div>

                  <div class="row">

                    <div class="col-md-12">
                    <h4>Lot Details</h4>
                  </div>
                  

                  <div class="form-group col-md-4">
                      <label for="cont_quantity">No Of Balse</label>
                      <input type="text" class="form-control" name="no_of_bales" id="noOFBales" placeholder="Contracted Quantity" value="<?php echo $row['no_of_bales'] ?>">
                      
                  </div>

                  <div class="form-group col-md-4">
                      <label for="avl_bales">Available Balse</label>
                      <input type="text" class="form-control" name="avl_bales" id="avl_bales" value="<?php echo $row['avl_bales'] ?>" placeholder="Available Balse" readonly="">
                      
                  </div>

              
                   </div>  


                    <div class="row">

                   <div class="col-md-4">
                      <div class="form-group">
                      <label for="lot_select">Select LOT No.</label>                                   
                        <select id="lot_select" class="form-control">
                          <option value="" disabled="" selected="">Select Option</option>                            
                        </select>
                      </div>
                    </div>
                     <div class="col-md-2" >
                            <button type="button" style="margin-top: 32px;" class="btn btn-primary add_lot_button" disabled="">Add</button>
                      </div>

                        <div class="col-md-2" >
                            <button id="btnSaveChanges" type="button" style="margin-top: 32px;" class="btn btn-success" disabled="">Save Changes</button>
                      </div>

                      <div class="col-md-4">
                          <div style="margin-top: 35px;" class="btnSaveChangesMsg">
                            
                          </div> 
                      </div>

                  </div>

                   <div class="dynamicLotSection">

                  <?php
                    if($row['lot_no']!='')
                    {
                      $lotNoArr=json_decode($row['lot_no']);
                      $lotBalesArr=json_decode($row['lot_bales']);

                      foreach ($lotNoArr as $key => $value) 
                      {
                        if($key==0)
                        {
                      ?>

                      <div class="row">
                       
                        <div class="form-group col-md-3">
                          <label for="lot_no">Lot No</label>
                          <input type="text" placeholder="Lot No" class="form-control lot_no"  name="lot_no[]" value="<?php echo $value ?>" readonly>
                        </div>

                        <div class="form-group col-md-3">
                          <label for="lot_bales">Lot Bales</label>
                          <input type="text" placeholder="Lot Bales" class="form-control lot_bales"  name="lot_bales[]" value="<?php echo $lotBalesArr[$key] ?>" onkeyup="lotBalesChange(this)">
                        </div>


                        <div class="col-md-1"><a href="javascript:void(0);" style="margin-top:30px;" class="btn btn-danger remove_lot_btn" onclick='removeLot(this,"<?php echo $value  ?>")'>-</a></div>
                      </div>

                      <?php
                        }
                        else
                        {
                       ?>
                        
                        <div class="row">
                        <div class="form-group col-md-3">
                          <input type="text" placeholder="Lot No" class="form-control lot_no"  name="lot_no[]" value="<?php echo $value ?>" readonly>
                        </div>

                        <div class="form-group col-md-3">
                          <input type="text" placeholder="Lot Bales" class="form-control lot_bales"  name="lot_bales[]" value="<?php echo $lotBalesArr[$key] ?>" onkeyup="lotBalesChange(this)">
                        </div>


                        <div class="col-md-1"><a href="javascript:void(0);" class="btn btn-danger remove_lot_btn" onclick='removeLot(this,"<?php echo $value  ?>")'>-</a></div>
                      </div>
                       <?php
                        }                    
                      }

                    }

                  ?>   
                </div>

                  <br><br>
  



                   

                    <div class="row">
                        
                      <div class="form-group col-md-6">
                        <label for="press_no">Press no</label>
                        <input type="text" placeholder="Press No." class="form-control" name="press_no" value="<?php echo $row['press_no'] ?>">
                      </div> 


                      <div class="form-group col-md-6">
                        <label for="variety">Select Product Variety</label>
                                           
                           <select id="variety" name="variety" class="form-control">
                            <option value="" disabled selected>Select</option>

                                                    
                            </select>

                            </div> 

                          </div>   


                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="sub_variety">Sub Type Of Variety</label>
                                   
                           <select id="sub_variety" name="sub_variety" class="form-control">
                            <option value="" disabled selected>Select</option>
                                                
                            
                            </select>

                        </div> 

                            <div class="form-group col-md-6">
                          <label for="price">Price</label>
                          <input type="text" id="price" name="price" class="form-control" value="<?php echo $row['price'] ?>" placeholder="enter price"> 
                        </div>

                         <div class="form-group col-md-5">
                      <label for="spl_rmrk">Special Remark</label>
                      <textarea class="form-control" name="spl_rmrk" id="w3review" rows="4" cols="60"><?php echo $row['spl_rmrk'] ?></textarea>
                  </div>


                            </div>
                    


                  <div class="row">
                    <div class="col-md-12">
                     <label for="bill_inst">Bill Instruction</label>
                      <textarea class="form-control" name="bill_inst" id="div_editor1" rows="4" cols="60"><?php echo $row['bill_inst'] ?></textarea>
                    </div>



                  </div>


                 


        

                   <input type="hidden" id="credit_days" name="credit_days" value="<?php echo $row['credit_days'] ?>"/> 

                    <input type="hidden" id="station" name="station" value="<?php echo $row['station'] ?>"> 
                  <input type="hidden" id="dispatch_date" name="dispatch_date" value="<?php echo $row['dispatch_date'] ?>"> 
                  <input type="hidden" id="prod_quality" name="prod_quality" value="<?php echo $row['prod_quality'] ?>"> 



                 
                   
                  <div class="form-group mt-2">
                      <button type="submit" name="submit" id="submit" class="btn btn-primary">Submit</button>
                  </div>
                 

                </form>

              </div>
            </div>
         
        </div>
      </div>

</div>
</div>
   
  

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<script>

    var editor1 = new RichTextEditor("#div_editor1");

 
$(document).ready(function(){
  var check_tax = $('input[name=taxtype]:checked').val();
  if(check_tax=='sgst')
  {
      $('.type_sgst').removeClass('d-none');

  }
  else
  {
      $('.type_sgst').addClass('d-none');
      $('.type_igst').removeClass('d-none');
  }

  $('input[name=taxtype]').change(function(){
    var checked = $('input[name=taxtype]:checked').val();
    if(checked == 'sgst'){
      $('.type_sgst').each(function(){
        $(this).removeClass('d-none');
        $(this).find('input[type=text]').val('0');
      });
      $('.type_igst').addClass('d-none');
      $('.type_igst').find('input[type=text]').val('0');
    }else{
      $('.type_sgst').each(function(){
        $(this).addClass('d-none');
        $(this).find('input[type=text]').val('0');
      });
      $('.type_igst').removeClass('d-none');
      $('.type_igst').find('input[type=text]').val('0');
    }
  });
  $('input.lot_no').keyup(function(){
    var input_val = $(this).val();
    $(this).parent().find('input:not(.lot_no)').each(function() {
      input_val++;
      $(this).val(input_val);
    });
  });



  //-------------------------------------------------------

  //Confirmation No. Check
  $('#conf_no_avl_msg').hide();
  $('#conf_no_not_msg').hide();
   $('#conf_check').on('click', function() {

        var conf1=$('#conf_form').val();
        var conf_index=$('#conf_index').val();
        var conf_no=conf1+'-'+conf_index

        $.ajax({
            type: "POST",
            url: 'check_conf_no.php',
            data: {conf_no:conf_no},
            success: function(response)
            {

                var jsonData = JSON.parse(response);
                console.log(jsonData);

                if(jsonData.status==false)
                {
                  $('#conf_no_avl_msg').show();
                  $('#conf_no_not_msg').hide();
                }
                else
                {
                  $('#conf_no_avl_msg').hide();
                  $('#conf_no_not_msg').show();
                }

           }
       });
        
      });

    $('#conf_no').on('change', function() {


       

        var value=this.value;
        
        $.ajax({
            type: "POST",
            url: 'check.php',
            data: {conf_no:value},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                // console.log(jsonData);         
                $('#conf_split_no').val(jsonData['conf_split_no']);
           }
       });
        
      });


    $('#conf_no').on('change', function() {

       $('#variety').find('option').not(':first').remove();
      $('#sub_variety').find('option').not(':first').remove();


       getLotNoList();


        //remove dynamic lot & bales

        $('.dynamicLotSection').empty();
        




        var value=this.value;

      $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {conf_no:value},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                console.log(jsonData);
                // alert(jsonData)

                $('#avl_bales').val(jsonData['cont_quantity']);
                $('#external_party').val(jsonData['ext_name']);
                $('#external_party_id').val(jsonData['ext_id']);
                $('#product_length').val(jsonData['length']);
                $('#strength').val(jsonData['strength']);
                $('#mic').val(jsonData['mic']);
                $('#rd').val(jsonData['rd']);
                $('#trash').val(jsonData['trash']);
                $('#broker').val(jsonData['broker_name']);
                $('#broker_id').val(jsonData['broker_id']);
                $('#trans_ins').val(jsonData['trans_ins']);
                $('#product').val(jsonData['product_name']);
                $('#product_id').val(jsonData['product_id']);
                $('#avl_bales').val(jsonData['cont_quantity']-jsonData['used_bales']);

                $('#cgst').val(jsonData['cgst']);
                $('#sgst').val(jsonData['sgst']);
                $('#igst').val(jsonData['igst']);

                $('#price').val(jsonData['candy_rate']);

                $('#shipping_ext_party_id').val(jsonData['shipping_ext_party_id']);
                $('#moi').val(jsonData['moi']);

                 $('#credit_days').val(jsonData['credit_days']);

                   $('#station').val(jsonData['station']);
                $('#dispatch_date').val(jsonData['dispatch_date']);
                $('#prod_quality').val(jsonData['prod_quality']);




                var taxtype1 = jsonData['tax_type'];
                if(taxtype1 == 'sgst'){
                $("#taxtype1").prop("checked", true);
                $('.type_sgst').each(function(){
                  $(this).removeClass('d-none');
                  $(this).find('input[type=text]').val();
                });
                $('.type_igst').addClass('d-none');
                $('.type_igst').find('input[type=text]').val('0');
              }else{
                 $("#taxtype2").prop("checked", true);
                $('.type_sgst').each(function(){
                  $(this).addClass('d-none');
                  $(this).find('input[type=text]').val('0');
                });
                $('.type_igst').removeClass('d-none');
                $('#igst').find('input[type=text]').val(jsonData['igst']);
              }

              
              //set variety in dropdown

               var selected_variety=jsonData['variety'];

              var variety=jsonData['main_variety'];

              $.each(variety, function (key, val) 
               {
                    if(variety[key].id==selected_variety)
                    {
                        $('<option/>').val(variety[key].id).prop("selected", "selected").html(variety[key].value).appendTo('#variety');
                    } 
                    else
                    {
                        $('<option/>').val(variety[key].id).html(variety[key].value).appendTo('#variety');
                    }

                    
               });

               

                //set Sub variety in dropdown
                var sub_variety=jsonData['main_prod_sub_variety'];

                 var selected_sub_variety=jsonData['sub_variety'];


                $('#sub_variety').find('option').not(':first').remove();
                $.each(sub_variety, function (key, val) 
                 {
                      if(sub_variety[key].id==selected_sub_variety)
                      {
                         $('<option/>').val(sub_variety[key].id).prop("selected", "selected").html(sub_variety[key].value).appendTo('#sub_variety');
                      }
                      else
                      {
                        $('<option/>').val(sub_variety[key].id).html(sub_variety[key].value).appendTo('#sub_variety');
                      }
                     
                 });
                
           }
      });
        
    });



    $("form").submit(function (e) {
            var c=0;
            var noOFBales = $('#noOFBales').val();
            $( ".lot_bales" ).each(function(index) {
              c=parseInt(c)+parseInt(this.value);
            });
            if (parseInt(noOFBales)!= parseInt(c)) {

              e.preventDefault();
              alert('Selected Lot Bales And Entered No. Of Bales Not Match')
              return false;

            }
          });



    


    // balse check
    $('#noOFBales').keyup(function() {


    var noOfBales=parseInt($("#noOFBales").val());
    // alert(noOfBales);
    var avlBales=parseInt($('#avl_bales').val());
    // alert(avlBales);
    $('span.error-keyup-1').hide();
    $("#submit").attr("disabled", false);
    
    if(noOfBales=='0')
    {
      $("#noOFBales").val('');
    }
    if(noOfBales>avlBales)
    {
        $('#noOFBales').after('<span class="error error-keyup-1 text-danger">No Of Bales Should Not be greater than Available Bales...</span>'); 
        $("#submit").attr("disabled", true); 
    }

     checkBales();
    $("#btnSaveChanges").attr("disabled", false);

});




  //-------------------------------------------------------

  //lot_no no change
        $('#lot_select').on('change', function() {

          //enable add lot button
          $('.add_lot_button').attr("disabled",false);
        
        });


        var isFirstRow=false;

        //dynamic lot create
        $('.add_lot_button').click(function()
        {
              //check bales is entered or not
              var bales = $('#noOFBales').val();
              if(bales=='')
              {
                alert('Please Enter Bales...')
                return false
              }


              //check bales count if value is greter than or equal then dont allow to add new lot
              var lotBalesCount=0;
              var enterBales=$('#noOFBales').val();
              if(enterBales=='')
              {
                enterBales=0;
              }

             $(".lot_bales").each(function( index ) 
             {
                lotBalesCount+=parseInt(this.value);
             });


             if(parseInt(lotBalesCount)>=enterBales)
             {
               return false;
             }



             var curClass=$('.lot_no').length;
             if(curClass==0)
             {
                isFirstRow=true;
             }


            var selectedLotNo=$('#lot_select :selected').val();


          //find object of selected lot no
          var result=MainLotBalesArr[selectedLotNo];
         
            if(selectedLotNo!='')
            {
                  if(isFirstRow==true)
                  {
                  $('.dynamicLotSection').append('<div class="row"><div class="form-group col-md-3"><label for="lot_no">Lot No</label><input type="text" placeholder="Lot No" class="form-control mb-2 lot_no" name="lot_no[]" readonly value='+selectedLotNo+'></div><div class="form-group col-md-3"><label for="lot_bales">Lot Bales</label><input type="text" placeholder="Lot Bales" class="form-control lot_bales"  name="lot_bales[]" value="'+result+'" onkeyup="lotBalesChange(this)"></div><div class="col-md-1"><a href="javascript:void(0);" style="margin-top:30px;" class="btn btn-danger remove_lot_btn" onclick="removeLot(this,'+selectedLotNo+')">-</a></div></div>');
                }
                else
                {
                     $('.dynamicLotSection').append('<div class="row"><div class="form-group col-md-3"><input type="text" placeholder="Lot No" class="form-control mb-2 lot_no" name="lot_no[]" readonly value='+selectedLotNo+'></div><div class="form-group col-md-3"><input type="text" placeholder="Lot Bales" class="form-control lot_bales"  name="lot_bales[]" value="'+result+'" onkeyup="lotBalesChange(this)"></div><div class="col-md-1"><a href="javascript:void(0);" class="btn btn-danger remove_lot_btn" onclick="removeLot(this,'+selectedLotNo+')">-</a></div></div>');
                }

                $("#lot_select option[value="+selectedLotNo+"]").remove();
                $('#lot_select').prop('selectedIndex',0);
                isFirstRow=false;
               
                checkBales();
                updateLotData();
                
           
            }
 

        });



           $('#btnSaveChanges').on('click', function() {


              
              $('span.error-keyup-15').hide();

                var formdata = $('#main_form').serialize();
                    formdata += "&updateLotData=1";
                    formdata+="&record_id="+<?php echo $_GET['id'] ?>;

                  $.ajax({
                      type: "POST",
                      url: 'AjaxLotDataUpdate.php',
                      data : formdata,
                        method : 'post',
                       
                      success: function(response)
                      {
                        console.log(response)

                        var jsonData = JSON.parse(response);

                        if(jsonData.success)
                        {
                          $('.btnSaveChangesMsg').append('<span class="error error-keyup-15 text-success">Data Successfully Saved.</span>');
                          $("#btnSaveChanges").attr("disabled", true);

                        }

                      
                           
                     }
                  });
            

         });







});

checkBales();

function checkBales() {
      $('span.error-keyup-10').hide();

     

      var noBales = $('#noOFBales').val();
      var countBales = 0;
      $( ".lot_bales" ).each(function( index ) {
        countBales = parseInt(countBales) + parseInt(this.value);

      });
      if (parseInt(noBales) != parseInt(countBales)) {
        $('.lot_bales').last().after('<span class="error error-keyup-10 text-danger">No Of Bales Is not eual to selected bales.</span>'); 
        $("#submit").attr("disabled", true);
         $("#btnSaveChanges").attr("disabled", true);
      }else{

        $('span.error-keyup-10').hide();
        $("#submit").attr("disabled", false);
      }
}



//get Variety In dropdown When Page Load
getVariety();
function getVariety()
{
    var selected_variety="<?php echo $row['variety'] ?>";
    var selected_sub_variety="<?php echo $row['sub_variety'] ?>";
    var conf_no=$('#conf_no').val();

        //get Main Variety
        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {conf_no:conf_no},
            success: function(response)
            {

               var jsonData = JSON.parse(response);
                console.log(jsonData);



                  var getCurrentAddBales=<?php echo $row['no_of_bales'] ?>;
                  var get_avl_bales=parseInt(jsonData['cont_quantity'])-parseInt(jsonData['used_bales'])+parseInt(getCurrentAddBales);
                  $('#avl_bales').val(get_avl_bales);

                 

                //set variety in dropdown
                $('#variety').find('option').not(':first').remove();
                var main_var=jsonData['main_variety'];

                 $.each(main_var, function (key, val) 
                 {
                      if(main_var[key].id==selected_variety)
                      {
                          $('<option/>').val(main_var[key].id).prop("selected", "selected").html(main_var[key].value).appendTo('#variety');
                      } 
                      else
                      {
                          $('<option/>').val(main_var[key].id).html(main_var[key].value).appendTo('#variety');
                      }

                      
                 });

                
    
                //set Sub variety in dropdown
                var sub_variety=jsonData['main_prod_sub_variety'];
                $('#sub_variety').find('option').not(':first').remove();

                $.each(sub_variety, function (key, val) 
                 {
                      if(sub_variety[key].id==selected_sub_variety)
                      {
                         $('<option/>').val(sub_variety[key].id).prop("selected", "selected").html(sub_variety[key].value).appendTo('#sub_variety');
                      }
                      else
                      {
                        $('<option/>').val(sub_variety[key].id).html(sub_variety[key].value).appendTo('#sub_variety');
                      }
                     
                 });
             

            }
          });




}



  var MainLotBalesArr=new Array();

getLotNoList();

function getLotNoList() {

        var conf_no = $("#conf_no").val();
        var noBales = $('#noOFBales').val();

        $.ajax({
            type: "POST",
            url: 'lotGetedit.php',
            data: {
              conf_no:conf_no,
              record_id:"<?php echo $_GET['id'] ?>",
              curRecord_conf_no:"<?php echo $row['conf_no'] ?>"
            },
            success: function(response)
            {
                
                var jsonData = JSON.parse(response);
                console.log(jsonData);


                var Arr=new Array();

                $.each(jsonData.lot_no, function(i, item) {
                    
                    Arr[item]=jsonData.lot_bales[i];

                });

                MainLotBalesArr=Arr;



                //get current added lot list
                var AddedLotArr=new Array();

                $(".lot_no").each(function( index ) 
                 {
                    AddedLotArr[index]=this.value;
                 });

               

                $('#lot_select').find('option').not(':first').remove();
                $.each(jsonData.lot_no,function(index,obj)
                {

                  if(!AddedLotArr.includes(obj))
                  {
                        var option_data="<option value="+obj+">"+obj+"</option>";
                        $(option_data).appendTo('#lot_select'); 
                  }

                });

               
              
            }
        });

}

function removeLot(e,lot_no)
{


      var lot_no=lot_no


       var result=MainLotBalesArr[lot_no];

    

       $("#lot_select").append(new Option(lot_no, lot_no));
       $("#lot_select option").sort(function(a, b) {
                  a = a.value;
                  b = b.value;
                  return a-b;
              }).appendTo('#lot_select');

        $('#lot_select').prop('selectedIndex',0);
        $(e).parent('div').parent('div').remove(); 
       
        checkBales();
        updateLotData();
}




    function updateLotData()
    {
       var formdata = $('#main_form').serialize();
          formdata += "&updateLotData=1";
          formdata+="&record_id="+<?php echo $_GET['id'] ?>;

        $.ajax({
            type: "POST",
            url: 'AjaxLotDataUpdate.php',
            data : formdata,
              method : 'post',
            success: function(response)
            {
              console.log(response)

              var jsonData = JSON.parse(response);

              if(jsonData.success)
              {
                getLotNoList();
              }

              checkBales();

            
                 
           }
        });
      }


    function lotBalesChange(e,lotno)
    {

      $("#btnSaveChanges").attr("disabled", false);

       var lot_no = $(e).parent().parent().find('.lot_no').val();

       $('span.error-keyup-11').hide();
       $("#submit").attr("disabled", false);


         var result=MainLotBalesArr[lot_no];
           

       if(parseInt(e.value)>parseInt(result))
        {
             $(e).after('<span class="error error-keyup-11 text-danger">Sorry ! Available Bales is '+result+'.</span>');
            $("#submit").attr("disabled", true);
          
        }

        checkBales();

    }



/*getAvlLotAndBales();
function getAvlLotAndBales()
{
  var conf_no = $("#conf_no").val();
  var noBales = $('#noOFBales').val();

 $.ajax({
            type: "POST",
            url: 'lotGetedit.php',
            data: {
              conf_no:conf_no,
              noOfBales:noBales,
              record_id:"<?php echo $_GET['id'] ?>"
            },
            success: function(response)
            {
                console.log(response);
                var jsonData = JSON.parse(response);

                 MainLotBalesArr=jsonData['main_lot_and_bales'];
             
            }
        });
}*/

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
</script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

         
  </body>
</html>

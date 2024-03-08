<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
 if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from comparison_report where id=".$id;

    $result = mysqli_query($conn, $sql);
   

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      
    }else {
      $errorMsg = 'Could not Find Any Record';
    }

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
    <title>Comparison Report Show</title>
 
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Comparison Report Show</span></a>
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
         <?php
        $sqlLastChange="select username,updated_at from comparison_report where id='".$row['id']."'";

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

          <!-- last change on table END-->  

      <div class="container-fluid">
        <div class="row justify-content-center">
        
            <div class="card">
              <div class="card-header">Comparison Report Show</div>
              <div class="card-body">
               
                  <div class="row">
                    <div class="col-md-12">
                        <h6>Sales Report :</h6>
                        <hr/>
                    </div>


                     <div class="form-group col-md-5">
                              <label for="sales_conf_no">External Party with Confirmation</label>
                              <?php
                              $sql = "SELECT DISTINCT(conf_no),party_name FROM `sales_report`" ;
                              $result = mysqli_query($conn, $sql);
                                    
                                  ?>                      
                                   <select id="sales_conf_no" name="sales_conf_no" class="form-control" disabled="">
                                 
                                    <?php                   
                                      foreach ($conn->query($sql) as $result) 
                                      {

                                        $party_sql="SELECT * FROM external_party WHERE id='".$result['party_name']."'";
                                        $party_result = mysqli_query($conn, $party_sql);
                                        $party_row = $party_result->fetch_assoc();


                                          if($row['sales_conf_no']==$result['conf_no'])
                                          {
                                              echo "<option  value='" .$result['conf_no']."' selected>" .$party_row['partyname'].' ('.$result['conf_no'].')'."</option>";
                                          }
                                          
                                        }
                                    ?>                              
                                    </select>
                            </div>

                  </div>

                  <div class=" field_wrapper_dyamic">
                   

                       <div class="dynamicSalesLotSection">

                          <?php
                            $salesArr=json_decode($row['sales_data'],true);
                            foreach ($salesArr as $key => $item) 
                            {

                              //convert invoice date
                              $invoice_date='';
                              if($item['invoice_date']!='' && $item['invoice_date']!='0000-00-00')
                              {
                                $invoice_date = str_replace('-', '/', $item['invoice_date']);
                                $invoice_date = date('d/m/Y', strtotime($invoice_date));
                              }

                              //get veh no. from veh_id
                                $veh_no='';
                                $sqlTruck="select * from truck_master where id='".$item['veh_id']."'";
                                $resultTruck = mysqli_query($conn, $sqlTruck);
                                if(mysqli_num_rows($resultTruck)>0)
                                {
                                  $rowTruck=mysqli_fetch_assoc($resultTruck);
                                  $veh_no=$rowTruck['truck_no'];
                                }


                            ?>

                          <div class="row">
                            <div class="form-group col-md-2">
                              <label>LOT No.</label>
                              <input type="text" class="form-control lot_no" readonly="" name="sales_lot_no[]" value="<?php echo $item['lot_no'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                              <label>LOT Bales</label>
                              <input type="text" class="form-control lot_bales" name="sales_lot_bales[]" placeholder=" Bales" value="<?php echo $item['lot_bales'] ?>" readonly></div>
                              <div class="form-group col-md-2">
                                <label>Invoice No.</label>
                                <input type="text" class="form-control sales_invoice_no" name="sales_invoice_no[]" value="<?php echo $item['invoice_no'] ?>"readonly>
                              </div>
                              <div class="form-group col-md-2">
                                <label>Invoice Date</label>
                                <input type="text" class="form-control" value="<?php echo $invoice_date ?>" readonly>
                              </div>
                              <div class="form-group col-md-2">
                                <label>Vehicle No</label>
                                <input type="text" class="form-control" value="<?php echo $veh_no ?>" readonly>
                              </div>
                             
                              
                              
                            </div>

                             


                            <?php
                                                            
                            }
                          ?>



                       </div>

                    </div>

                    <div class="row">
                        <div class="form-group col-sm-4">
                          <label for="sales_bales">Bales</label>
                          <input id="sales_bales" type="text" class="form-control" name="sales_bales" value="<?php echo $row['sales_bales'] ?>" readonly>
                        </div>
                        <div class="form-group col-sm-4">
                          <label for="sales_total_bales">Total Bales</label>
                          <input id="sales_total_bales" type="text" class="form-control" name="sales_total_bales" readonly="" value="<?php echo $row['sales_total_bales'] ?>">
                        </div>
                          <div class="form-group col-sm-4">
                          <label for="delivery_at">Delivery At</label>
                          <input id="delivery_at" type="text" class="form-control" name="delivery_at" value="<?php echo $row['delivery_at'] ?>" readonly="">
                        </div>

                        <?php
                          //get invoice rais name (firm)
                          $firm_name1='';
                          $sqlFirm="select * from party where id='".$row['invoice_raise']."'";
                          $resultFirm = mysqli_query($conn, $sqlFirm);
                          if(mysqli_num_rows($resultFirm)>0)
                          {
                            $rowFirm=mysqli_fetch_assoc($resultFirm);
                            $firm_name1=$rowFirm['party_name'];
                          }
                        ?>


                        <div class="form-group col-sm-4">
                          <label for="invoice_raise_name">Invoice Raise in the Name</label>
                          <input id="invoice_raise_name" type="text" class="form-control"  readonly="" value="<?php echo $firm_name1 ?>">
                          <input type="hidden" name="invoice_raise" id="invoice_raise_id" value="<?php echo $row['invoice_raise'] ?>">
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


                


                        <div class=" field_wrapper_dyamic">
                             

                              <style type="text/css">
                              .purCard{
                                margin-top: 15px;
                              }
                            </style>


                       <div class="dynamicPurchaseLotSection">

                        <?php
                            $purchaseArr=json_decode($row['purchase_data'],true);
                            foreach ($purchaseArr as $key => $item) 
                            {                              
                              ?>

                              <div class="card purCard">
                            <div class="card-header">
                              Lot No. <?php echo $item['lot_no'] ?>
                           
                            </div>
                           <div class="card-body">

                             <div class="row">

                              <div class="form-group col-md-4">
                                <label>LOT No.</label>
                                <input type="text" class="form-control pur_lot_no" readonly="" name="pur_lot_no[]" value="<?php echo $item['lot_no'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-4">
                                <label>LOT Qty.</label>
                                <input type="text" class="form-control pur_lot_qty" name="pur_lot_qty[]" value="<?php echo $item['lot_bales'] ?>" readonly>
                              </div>
                              <div class="form-group col-md-4">
                                <label>Invoice No.</label>
                                <input type="text" class="form-control pur_invoice_no" name="pur_invoice_no[]" value="<?php echo $item['invoice_no'] ?>" readonly>
                              </div>

                                   


                                  <?php
                                  $used_firm_bales='';
                                  foreach ($firmArr as $key => $value) 
                                  {
                                    if($item['used_firm_bales']==$value['id'])
                                    {
                                       $used_firm_bales=$value['party_name'];
                                    }
                                                                           
                                  }
                                ?>

                                  <div class="form-group col-sm-4">
                                      <label for="used_firm_bales">Use of Firm Bales :</label>
                                      <input type="text" class="form-control" id="used_firm_bales" value="<?php echo $used_firm_bales ?>" readonly="">
                                  </div>

                                   <div class="form-group col-md-4">
                                      <label for="total_dispatch_bales">Total No Of Bales Dispatch</label>
                                      <input type="text" class="form-control" name="total_dispatch_bales[]" value="<?php echo $item['total_dispatch_bales'] ?>" readonly>
                                  </div>
                                

                                 

                                 <div class="form-group col-sm-4"><label for="party_with_conf">External Party & Conf No.</label><input type="text" name="ext_conf_no[]" class="form-control" value="<?php echo $item['ext_conf_no'] ?>" readonly=""></div>

                                 

                        </div>

                            </div>
                            
                       </div>

                          <?php
                            }
                          ?>

                       </div>

                    </div>
                        
  

                    
              </div>
            </div>
         
        </div>
      </div>

</div>
</div>

 <script type="text/javascript">


        
     

        $(document).ready(function () {






        });


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

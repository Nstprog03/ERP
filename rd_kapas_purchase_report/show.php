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
   $dir = "/file_storage/";
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
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RD Kapas Purchase Report Details</title>
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
    });
    </script>   

<style type="text/css">
.modal-dialog {width:800px;}
.thumbnail {margin-bottom:6px;}
.modal.fade .modal-dialog {
      -webkit-transform: translate(0, 0);
      -ms-transform: translate(0, 0); // IE9 only
          transform: translate(0, 0);

 }
</style>
    
    
    
  </head>
  <body>


    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

            
      <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> RD Kapas Purchase Report Details</span></a>
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
              <ul class="navbar-nav ml-auto"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
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
          <div class="card">
            <div class="card-header">
              RD Kapas Purchase Report Details 
            </div>
            <div class="card-body">
              <div class="row">  
                    <div class="form-group col-md-4">
                      <label for="report_date">Report Date</label>
                      <input type="text" class="form-control" name="report_date"  value="<?php echo date("d/m/Y", strtotime($row['report_date'])); ?>">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="invoice_no">Invoice No</label>
                      <input type="text" class="form-control" name="invoice_no" placeholder="Enter Mobile Number" value="<?php echo $row['invoice_no']; ?>">
                    </div>

                    <?php
                       $sql4 = "select * from external_party where id='".$row['external_party']."'";
                            $result4 = mysqli_query($conn, $sql4);
                            $row10 = mysqli_fetch_assoc($result4);
                            $ext_party='';
                            if(isset($row10))
                            {
                              $ext_party=$row10['partyname'];
                            }
                    ?>

                    <div class="form-group col-md-4">
                      <label for="external_party">External Party</label>
                      <input type="text" class="form-control" name="external_party" placeholder="External Party" value="<?php echo $ext_party; ?>">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-4">
                      <label for="firm">Firm</label>
                      <?php 

                            $sql4 = "select * from party where id='".$row['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);
                            $row10 = mysqli_fetch_assoc($result4);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }?>
                      <input type="text" class="form-control" name="firm" placeholder="Firm" value="<?php echo $pname; ?>">
                    </div>

                    <?php
                       $sql4 = "select * from products where id='".$row['product']."'";
                            $result4 = mysqli_query($conn, $sql4);
                            $row10 = mysqli_fetch_assoc($result4);
                            $prod_name='';
                            if(isset($row10))
                            {
                              $prod_name=$row10['prod_name'];
                            }
                    ?>

                    <div class="form-group col-md-4">
                      <label for="product">Product</label>
                      <input type="text" class="form-control" name="product" placeholder="Product" value="<?php echo $prod_name; ?>">
                    </div>

                    <?php
                       $sql4 = "select * from broker where id='".$row['broker']."'";
                            $result4 = mysqli_query($conn, $sql4);
                            $row10 = mysqli_fetch_assoc($result4);
                            $broker='';
                            if(isset($row10))
                            {
                              $broker=$row10['name'];
                            }
                    ?>

                    <div class="form-group col-md-4">
                      <label for="broker">Broker</label>
                      <input type="text" class="form-control" name="broker" placeholder="Broker" value="<?php echo $broker; ?>">
                    </div>
                  </div>

                  <div class="row">

                    <div class="form-group col-md-4">
                      <label for="basic_amt">Basic Amt</label>
                      <input type="text" class="form-control basic" name="basic_amt" placeholder="Enter Email" value="<?php echo $row['basic_amt']; ?>" pattern="[0-9]+">
                    </div>

                    <div class="form-group col-md-4">
                      <label for="tax">Tax (In Percentage)</label>
                      <input type="text" class="form-control tax" name="tax" placeholder="Enter Email" value="<?php echo $row['tax']; ?>" pattern="[0-9]+">
                    </div>



                   <div class="form-group col-md-4">
                      <label for="tax_amt">Tax Amount</label>
                      <input type="text" class="form-control tax_amt" name="tax_amt" id="tax_amt" value="<?php echo $row['tax_amt']; ?>" readonly>
                  </div>

                    <div class="form-group col-md-4">
                      <label for="tcs">TCS</label>
                      <input type="text" class="form-control tcs" name="tcs" placeholder="Enter Email" value="<?php echo $row['tcs']; ?>" pattern="[0-9]+">
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

                          <div class="row" style="margin: 0;">
                    
                                        <?php
                                        if($row['docimg'] != ''){
                                         $prev = explode(',',$row['docimg']);
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
                                                          <div class="form-group col-md-4 field-show-image pl-0">
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
                                                              <h6 class="title"> Document File <?= $key+1 ?></h6>
                                                            
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
                      $('input').attr('readonly', true);

        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
        $(document).ready(function () {
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
});
    </script>
    </body>
  </html>

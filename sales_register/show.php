<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}
 // include('add.php');
  // $getYear=$_SESSION['sales_conf_financial_year'];
  // $year_array=explode("/",$getYear);

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
      <div class="container">
        <a class="navbar-brand" href="index.php">Sales Register Database</a>
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

      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">Sales Register Database</div>
              <div class="card-body">
             
                <form class=""  method="POST"
                   enctype="multipart/form-data">
                   <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="row">
                       <?php
                            $sql4 = "select * from external_party where id='".$row['shipping_ext_party_id']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['partyname'];
                            }
                            
                          ?>
               
                        <div class="form-group col-sm-4">
                          <label for="party_data"> Shipping Name</label>
                          <input type="text" class="form-control" value="<?php  echo $pname; ?>">    
                        </div>

                        <div class="form-group col-sm-4">
                          <label for="party_data"> Delivery City</label>
                          <input type="text" class="form-control" value="<?php echo $row['delivery_city'] ?>">    
                        </div>


                         <?php 
                                $sql_var = "select * from product_sub_items where id='".$row['variety']."'";
                                $result_var = mysqli_query($conn, $sql_var);

                                $row_var = mysqli_fetch_assoc($result_var);
                                // print_r($row10);
                                $var_name='';
                                if(isset($row_var))
                                {
                                  $var_name=$row_var['value'];
                                }
                              
                            ?>







                        <div class="form-group col-sm-4">
                            <label for="party">Variety </label>
                            <input type="text" class="form-control" value="<?php  echo $var_name;?>" readonly>
                            
                               
                        </div>
                    </div>

                     
                    <div class="row">


                        <?php 
                                $sql_sub = "select * from product_sub_items where id='".$row['sub_variety']."'";
                                $result_sub = mysqli_query($conn, $sql_sub);

                                $row_sub = mysqli_fetch_assoc($result_sub);
                                // print_r($row10);
                                $subvar='';
                                if(isset($row_sub))
                                {
                                  $subvar=$row_sub['value'];
                                }
                              
                            ?>
                    
                      <div class="form-group col-sm-4">
                        <label for="delivery_city">Sub Variety</label>
                        <input type="text" class="form-control" name="delivery_city"  placeholder="Enter Sub Variety" value="<?php echo $subvar;   ?>">
                      </div>


                      <?php 
                       $sql_truck = "select t.*,p.trans_name from truck_master t, transport p where t.transport=p.id AND t.id='".$row['truck']."'";
                         $result_truck = mysqli_query($conn, $sql_truck);
                     

                      $row_truck = mysqli_fetch_assoc($result_truck);
                      // print_r($row10);
                      $truck_name='';
                      if(isset($row_truck))
                      {
                        $truck_name=$row_truck['truck_no'];
                      }
                            ?>
                      <div class="form-group col-sm-4">
                        <label for="truck">Truck /Vehicle No.</label>
                        <input type="text" class="form-control" value="<?php echo $truck_name ?>">
                      </div>
                      

                      <div class="form-group col-sm-4">
                        <label for="invice_no">Invoice No</label>
                        <input type="text" class="form-control" name="invice_no"  placeholder="Enter Invoice No" value="<?php echo $row['invice_no'] ?>">
                      </div>
                      <div class="form-group col-sm-4">
                        <label for="avl_bales">Total Amount</label>
                        <input type="text" class="form-control" id="avl_bales" name="avl_bales" value="<?php echo $row['total_value'] ?>" readonly>
                      </div>

                      <div class="form-group col-sm-4">
                          <label for="lot_bales">Candy Rate</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['candy_rate'] ?>" readonly>
                           
                          </div>
                         
                        </div>
                      
                      
                    </div>

                    <div class="row">

                      <?php
                      if($row['lot_no']!='')
                      {
                      ?>
                        <div class="form-group col-md-4">
                          <label for="lot_no">Lot No</label>
                          <div id="bales">
                          <?php 
                          $balesArr=json_decode($row['lot_no']);
                          foreach ($balesArr as $value) 
                          {
                           ?>
                            <input type="text" placeholder="lot_no" class="form-control mb-2 lot_no" name="lot_no[]" value="<?php echo $value; ?>" readonly>
                           <?php
                          } 
                          ?>
                          </div>
                         <?php } ?>


                       

                        </div>

                        <?php
                         $bales='';
                                 if($row['lot_bales']!='' || $row['lot_bales']!=null)
                                  {
                                       $bales=json_decode($row['lot_bales']);
                                       if($bales!=null)
                                       {
                                        $bales= array_sum($bales);
                                       }
                                       else
                                       {
                                        $bales='';
                                       } 
                                  }
                                  else
                                  {
                                    $bales='';
                                  }
                              ?>



                      <div class="form-group col-md-4">
                          <label for="lot_no">Lot Bales</label>
                          <input type="text" class="form-control mb-2 lot_no" name="lot_no[]" value="<?php echo  $bales ?>" readonly>
                        </div>



                        </div>

                        <div class="row">

                      
                        <div class="form-group col-md-4">
                          <label for="lot_bales"> Start PR NO.</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['start_pr'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                        <div class="form-group col-md-4">
                          <label for="lot_bales"> End PR NO.</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['end_pr'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                      </div>

                      
                    

                    <div class="row">

                      <div class="form-group col-sm-4">
                       <h5>Condition :</h5>
                      </div>

                        

                    </div>

                    <div class="row">

                      <div class="form-group col-md-4">
                          <label for="lot_bales"> Length</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['length'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                        <div class="form-group col-md-4">
                          <label for="lot_bales">Strength</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['strength'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                        <div class="form-group col-md-4">
                          <label for="lot_bales">Mic</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['mic'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                        <div class="form-group col-md-4">
                          <label for="lot_bales">Trash</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['end_pr'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                        <div class="form-group col-md-4">
                          <label for="lot_bales">Moisture</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="lot_bales[]" value="<?php echo $row['moi'] ?>" readonly>
                           
                          </div>
                         
                        </div>

                        <div class="form-group col-md-4">
                          <label for="lot_bales">RD</label>
                          <div id="bales">
                          
                            <input type="text"  class="form-control mb-2 lot_bales" name="rd" value="<?php echo $row['rd'] ?>" readonly>
                           
                          </div>
                         
                        </div>



                    </div>


                    
                    
                    

                                       
                  
                  </div></div>
                    
                </div>
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
        $(document).ready(function () {
          $('input[type="text"]').prop('readonly', true);
    });
    </script>
  </body>
</html>

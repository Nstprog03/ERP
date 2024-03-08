<?php
  include('add.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create New Product Consolidated Record</title>

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
        <a class="navbar-brand" href="index.php">Create New Product Consolidated Record</a>
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
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">Product Consolidated Report</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">

                   

                     <div class="form-group">
                      <label for="product">Select Product</label>
                      <?php
                            $sql = "select * from products";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="product" class="form-control">
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value=" .$result['prod_name']. ">" .$result['prod_name']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>
                     
                     <div class="form-group">
                      <label for="pur_conf">Purchase Confirmation No</label>
                       <input type="text" class="form-control" name="pur_conf"  placeholder="Enter Confirmation No" value="">
                    </div>

                    <div class="form-group">
                      <label for="conf_type">Confirmation Type</label>
                                         
                           <select name="conf_type" class="form-control">
                              <option value="0">Original</option>
                              <option value="1">Revised</option>
                              <option value="2">Cancel</option>
                            </select>
                    </div>


                <div class="form-group">
                    <label for="report_date">Select Report Date :</label>
                  <input type="date" value="<?php echo date('Y-m-d');?>" name="report_date">
                  </div>

                  

                     <div class="form-group">
                      <label for="party">Select External Party</label>
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="party" class="form-control">
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value=" .$result['partyname']. ">" .$result['partyname']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>


                     <div class="form-group">
                      <label for="firm">Select Firm</label>
                      <?php
                            $sql = "select * from party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="firm" class="form-control">
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value=" .$result['party_name']. ">" .$result['party_name']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>

                       <div class="form-group">
                      <label for="seller_ref">Seller Ref No</label>
                      <input type="text" class="form-control" name="seller_ref"  placeholder="Enter Refe No" value="">
                    </div>

                 
               

<?php
              date_default_timezone_set('Asia/Kolkata');

$date = date('Y/m/d H:i:s');
?>
  <div class="form-group">
                      <label for="bargain_date">Bargain Date & Time</label>
<input type="text" class="form-control" name="bargain_date"  placeholder="<?php echo $date;?>" value='<?php echo $date;?>'>
                    </div>

                                           <div class="form-group">
                      <label for="broker">Select Broker</label>
                      <?php
                            $sql = "select * from broker";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="broker" class="form-control">
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option  value=" .$result['name']. ">" .$result['name']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>

               

                 <div class="form-group">
                      <label for="transit_ins">Transit Insurance</label>
                      <select name="transit_ins">
                        <option value="0">
                        Transit Insurance By Us  
                        </option>

                        <option value="1">
                        Transit Insurance By Seller
                        </option>
                      </select>
                      
                  </div>

                  <div class="form-group">
                      <label for="pro_length">Product Length</label>
                      <input type="text" class="form-control" name="pro_length"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_meanlen">Product Mean length</label>
                      <input type="text" class="form-control" name="pro_meanlen"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_ui">Product UI</label>
                      <input type="text" class="form-control" name="pro_ui"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_str">Product Strength</label>
                      <input type="text" class="form-control" name="pro_str"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_sfi">Product SFI</label>
                      <input type="text" class="form-control" name="pro_sfi"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_mic">Product MIC</label>
                      <input type="text" class="form-control" name="pro_mic"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_rd">Product RD</label>
                      <input type="text" class="form-control" name="pro_rd"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_b">Product +B</label>
                      <input type="text" class="form-control" name="pro_b"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_cg">Product CG</label>
                      <input type="text" class="form-control" name="pro_cg"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_trash">Product Trash</label>
                      <input type="text" class="form-control" name="pro_trash"  placeholder="Enter Bales Quantity" value="">
                  </div>

                  <div class="form-group">
                      <label for="pro_mois">Product Moisture</label>
                      <input type="text" class="form-control" name="pro_mois"  placeholder="Enter Bales Quantity" value="">
                  </div>

                   <div class="form-group">
                      <label for="bales">No Of Bales</label>
                      <input type="text" class="form-control" name="bales"  placeholder="Enter Bales Quantity" value="">
                  </div>

                <div class="form-group">
                      <label for="pro_variety">Product Variety</label>
                      <?php
                            $sql = "select * from products";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="pro_variety" class="form-control">
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                              echo "<option  value=" .$result['prod_variety']. ">" .$result['prod_variety']. "</option>";
                              }
                            ?>                              
                            </select>
                    </div>

                    <div class="form-group">
                      <label for="price">Price</label>
                      <input type="text" class="form-control" name="price"  placeholder="Enter Price" value="">
                    </div>

                    <div class="form-group">
                      <label for="del_addr">Delivery Address</label>
                      <textarea class="form-control" name="del_addr" id="w3review" rows="4" cols="60"></textarea>
                    </div>

                    <div class="form-group">
                      <label for="bill_inst">Billing Instruction</label>
                      <textarea class="form-control" name="bill_inst" id="w3review" rows="4" cols="60"></textarea>
                    </div>
                    
                    <div class="form-group">
                      <label for="spl_rmrk">Special Remark</label>
                      <textarea class="form-control" name="spl_rmrk" id="w3review" rows="4" cols="60"></textarea>
                    </div>


                    <div class="form-group">
                      <label for="lab_name">Laboratory Condition</label>
                      <input type="text" class="form-control" name="lab_name"  placeholder="Enter Laboratory Name" value="">
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
</div>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    
         
  </body>
</html>

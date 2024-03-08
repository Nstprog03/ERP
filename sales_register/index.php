<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}
  

if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  if(isset($_POST['submit'])){

      $main_query="select * from sales_report where";


      $start_date='';
      $end_date='';
      $where_cond = array();
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $where_cond[] = " invoice_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " invoice_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " invoice_date>='".$start_date."' AND invoice_date<='".$end_date."'";

      }

      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);

        $where_cond[] = " firm in (".$firm.")";
        


      }

      if(isset($_POST['ext_party']))
      {
        $ext_party="'".implode("','",$_POST['ext_party'])."'";
        $where_cond[] = " party_name in (".$ext_party.")";

      }
      if(!empty($where_cond)){
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where.' order by invoice_date DESC';
      }else{

        $main_query="select * from sales_report order by invoice_date DESC";
      }
       
      $row_arr  = array();
       $result2 = mysqli_query($conn, $main_query);

       while($value = mysqli_fetch_assoc($result2)){

         $row_arr[] = $value;
       
       }





       //for excel export
        $xls_arr=array();
       foreach ($row_arr as $key => $value) {

        $xls_arr[$key]['id']=$key+1;

          //firm
        $sql_firm = "select * from party where id='".$value['firm']."'";
        $result_firm = mysqli_query($conn, $sql_firm);
        $row_firm=mysqli_fetch_array($result_firm);

        //set firm name in array
        $xls_arr[$key]['firm']=$row_firm['party_name'];


        $xls_arr[$key]['external_party']=$value['party_name'];


          //external party
        if($value['party_name']!='')
        {
            $sql_ext = "select * from external_party where id='".$value['party_name']."'";
            $result_ext = mysqli_query($conn, $sql_ext);
            $row_ext=mysqli_fetch_array($result_ext);

            $xls_arr[$key]['external_party']=$row_ext['partyname'];

        }
        else
        {
             $xls_arr[$key]['external_party']='';
        }





        //shipping party
        if($value['shipping_ext_party_id']!='')
        {
            $sql_ship = "select * from external_party where id='".$value['shipping_ext_party_id']."'";
            $result_ship = mysqli_query($conn, $sql_ship);
            $row_ship=mysqli_fetch_array($result_ship);

             //set firm name in array
            $xls_arr[$key]['shipping_party']=$row_ship['partyname'];

        }
        else
        {
             //set firm name in array
             $xls_arr[$key]['shipping_party']='';
        }


        $xls_arr[$key]['delivery_city']=$value['delivery_city'];




          //prodcut variety
        if($value['variety']!='')
        {
             $sql_var = "select * from product_sub_items where id='".$value['variety']."'";
            $result_var = mysqli_query($conn, $sql_var);

            $row_var = mysqli_fetch_assoc($result_var);

            $xls_arr[$key]['variety']=$row_var['value'];

        }
        else
        {
             $xls_arr[$key]['variety']='';
        }




          //sub prodcut variety
        if($value['sub_variety']!='')
        {
             $sql_var = "select * from product_sub_items where id='".$value['sub_variety']."'";
            $result_var = mysqli_query($conn, $sql_var);

            $row_var = mysqli_fetch_assoc($result_var);

            $xls_arr[$key]['sub_variety']=$row_var['value'];

        }
        else
        {
             $xls_arr[$key]['sub_variety']='';
        }



          //truck 
        if($value['truck']!='')
        {

           $sql_truck = "select * from truck_master where id='".$value['truck']."'";
            
            $result_truck = mysqli_query($conn, $sql_truck);
                    
            $row_truck = mysqli_fetch_assoc($result_truck);


            $xls_arr[$key]['truck_no']=$row_truck['truck_no'];

        }
        else
        {
             $xls_arr[$key]['truck_no']='';
        }






      
        

        if($value['lot_no']!='' || $value['lot_no']!=null)
        {
             $lot=json_decode($value['lot_no']);

             if($lot!=null)
             {
               $xls_arr[$key]['lot_no']=implode(",",$lot);
             }
             else
             {
              $xls_arr[$key]['lot_no']='';
             }
        }
        else
        {
          $xls_arr[$key]['lot_no']='';
        }
       

       

        if($value['lot_bales']!='' || $value['lot_bales']!=null)
        {
             $bales=json_decode($value['lot_bales']);
             if($bales!=null)
             {
              $xls_arr[$key]['lot_bales']=array_sum($bales);
             }
             else
             {
              $xls_arr[$key]['lot_bales']='';
             } 
        }
        else
        {
          $xls_arr[$key]['lot_bales']='';
        }


        $xls_arr[$key]['start_pr']=$value['start_pr'];
        $xls_arr[$key]['end_pr']=$value['end_pr'];
        $xls_arr[$key]['candy_rate']=$value['candy_rate'];
        $xls_arr[$key]['invoice_no']=$value['invice_no'];

        $inv_date='';
        if($value!='' && $value!='0000-00-00')
        {
          $inv_date=date("d/m/Y", strtotime($value['invoice_date']));
        }
        else
        {
          $inv_date='';
        }

        $xls_arr[$key]['invoice_date']=$inv_date;

        




        $xls_arr[$key]['total_amount']=$value['total_value'];
        $xls_arr[$key]['length']=$value['length'];
        $xls_arr[$key]['strength']=$value['strength'];
        $xls_arr[$key]['mic']=$value['mic'];
        $xls_arr[$key]['rd']=$value['rd'];
        $xls_arr[$key]['trash']=$value['trash'];
        $xls_arr[$key]['moi']=$value['moi'];


        $_SESSION['sales_register_export_data']=$xls_arr;
        $_SESSION['sales_register_column_data']=$_POST;
       

      
         
       }

  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales Register</title>
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

       
       <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
      <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 








     <script> 

    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

       $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
    });
       $(".datepicker").keydown(false);

       $(".mul-select").select2({
        placeholder: "Select Option",
      
         theme: "classic",
         width: 'element',
        

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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span>Sales Register</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
               <ul class="navbar-nav">

             

               <!--  <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li> -->

              </ul>
          </div>
        </div>
      </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">

          
                <div class="card">
                  <div class="card-header">Filter</div>
                      <div class="card-body">
                        <form class="" action="" method="post" enctype="multipart/form-data">
                    <div class="row justify-content-center">


                         
                                  <div class="form-group col-md-4">
                            <label for="firm">Select Firm</label>
                                <?php
                                    $sql = "select * from party";
                                    $result = mysqli_query($conn, $sql);
                                ?>                      
                        <select name="firm[]" class="form-control" multiple> 
                              
                            
                            <?php                   
                                foreach ($conn->query($sql) as $result) 
                                {                                  
                                   if(isset($_POST['firm']) && in_array($result['id'], $_POST['firm']))
                                   {

                                      echo "<option  value='".$result['id']."' selected>".$result['party_name']. "</option>";
                                   }
                                   else
                                   {
                                      echo "<option  value='".$result['id']."'>".$result['party_name']. "</option>";
                                   }
                                }
                            ?>                              
                            </select>
                        </div>
                         

                             <div class="form-group col-md-4">
                              <label for="party">Select External Party</label>
                              <?php
                                $sql = "select * from external_party";
                                $result = mysqli_query($conn, $sql);
                              ?>                      
                              <select name="ext_party[]" class="form-control" multiple>
                                
                                <?php                   
                                  foreach ($conn->query($sql) as $result) 
                                  {
                                   if(isset($_POST['ext_party']) && in_array($result['id'], $_POST['ext_party']))
                                   {

                                       echo "<option  value='".$result['id']."' selected>" .$result['partyname']. "</option>";
                                   }
                                   else
                                   {
                                       echo "<option  value='".$result['id']."'>" .$result['partyname']. "</option>";
                                   } 
                                  }
                                ?>                              
                              </select>
                            </div>

                             <div class="form-group col-md-4">
                              <div class="start-dt-col">
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} ?>" autocomplete="off">
                              </div>
                              <div class="end-dt-col">
                                <label for="end_date">End Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} ?>" autocomplete="off">
                              </div>
                            </div>

                            
                            
                            

                          </div>

                             <div class="row">

                            <div class="col-md-12">
                                 <h6>Column For Excel Export :</h6>
                            </div>
                          
                         
                           <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_invoice_date" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_invoice_date'])){echo 'checked';}}else{echo 'checked';}?>>Invoice Date
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_invoice_no" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_invoice_no'])){echo 'checked';}}else{echo 'checked';}?>>Invoice No
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_firm" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_firm'])){echo 'checked';}}else{echo 'checked';}?>>Firm
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_external_party" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_external_party'])){echo 'checked';}}else{echo 'checked';}?>>External Party
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_shipping_party" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_shipping_party'])){echo 'checked';}}else{echo 'checked';}?>>Shipping Party
                                </label>
                              </div>
                           </div>


                           <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_delivery_city" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_delivery_city'])){echo 'checked';}}else{echo 'checked';}?>>Delivery City
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_variety" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_variety'])){echo 'checked';}}else{echo 'checked';}?>>Variety
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_sub_variety" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_sub_variety'])){echo 'checked';}}else{echo 'checked';}?>>Sub Variety
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_truck_veh_no" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_truck_veh_no'])){echo 'checked';}}else{echo 'checked';}?>>Truck/Vehicle No.
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_lot_no" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_lot_no'])){echo 'checked';}}else{echo 'checked';}?>>LOT No.
                                </label>
                              </div>
                           </div>


                            <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_lot_bales" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_lot_bales'])){echo 'checked';}}else{echo 'checked';}?>>Lot Bales
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_pr_no_start" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_pr_no_start'])){echo 'checked';}}else{echo 'checked';}?>>PR. No. Start
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_pr_no_end" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_pr_no_end'])){echo 'checked';}}else{echo 'checked';}?>>PR. No. End
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_candy_rate" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_candy_rate'])){echo 'checked';}}else{echo 'checked';}?>>Candy Rate
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_total_amount" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_total_amount'])){echo 'checked';}}else{echo 'checked';}?>>Total Amount
                                </label>
                              </div>
                           </div>


                            <div class="col-md-3">
                             <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_length" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_length'])){echo 'checked';}}else{echo 'checked';}?>>Length
                                </label>
                              </div>
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_strength" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_strength'])){echo 'checked';}}else{echo 'checked';}?>>Strength
                                </label>
                              </div>
                                <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_mic" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_mic'])){echo 'checked';}}else{echo 'checked';}?>>Mic
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_trash" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_trash'])){echo 'checked';}}else{echo 'checked';}?>>Trash
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_mois" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_mois'])){echo 'checked';}}else{echo 'checked';}?>>Moisture
                                </label>
                              </div>
                               <div class="form-check">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="col_rd" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['col_rd'])){echo 'checked';}}else{echo 'checked';}?>>RD
                                </label>
                              </div>
                           </div>
           
                          </div>

                          
                          <div class="row mt-3">
                               <div class="form-group col-md-1">
                                <button type="submit" name="submit" class="btn btn-primary waves">Filter</button>
                              </div>
                              <div class="form-group col-md-1">
                                <button type="submit" name="clearFilter" class="btn btn-danger waves">Clear Filter</button>
                              </div>                         
                          </div>

                         

                     
                        </form>
                        
                     </div>
                </div>
          

            

                  <div class="card" style="margin-top: 20px;">
            <div style="margin-top: 20px;" class="col-md-12">
                <div class="card">
                   
                      <div class="card-body export-data expoert-register">

                        <?php
                          if(isset($_POST['submit']))
                          {
                            ?>
                            <div class="export-cta">
                             
                                
                              <a href="export.php" name="export" class="btn btn-info">Export To Excel</a>
                              
                         
                            </div>
                            <br>
                            <?php
                          }
                        ?>
                      <table id="example" class="registertable table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice Date</th>
                                <th>Invoice No</th>
                                <th>Firm</th>
                                <th>External Party</th>
                                <th>Shipping Party</th>
                                <th>Delivery City</th>
                                <th>Variety</th>
                                <th>Sub Variety</th>
                                <th>Truck/Vehicle No.</th>
                                <th>LOT No.</th>
                                <th>Lot Bales</th>
                                <th>PR. No. Start</th>
                                 <th>PR. No. End</th>
                                <th>Candy Rate</th>
                                <th>Total Amount</th>
                                <th>Length</th>
                                <th>Strength</th>
                                <th>Mic</th>
                                <th>Trash</th>
                                <th>Moisture</th>
                                <th>RD</th>
                                 <th>Show</th>
                               
                               
                                </tr>
                        </thead>
                        <tfoot>
                          
                            
                          
                          <tr>
                              <th>ID</th>
                                <th>Invoice Date</th>
                                <th>Invoice No</th>
                                 <th>Firm</th>
                                <th>External Party</th>
                                <th>Shipping Party</th>
                                <th>Delivery City</th>
                                <th>Variety</th>
                                <th>Sub Variety</th>
                                <th>Truck/Vehicle No.</th>
                                <th>LOT No.</th>
                                <th>Lot Bales</th>
                                <th>PR. No. Start</th>
                                 <th>PR. No. End</th>
                                <th>Candy Rate</th>
                                <th>Total Amount</th>
                                <th>Length</th>
                                <th>Strength</th>
                                <th>Mic</th>
                                <th>Trash</th>
                                <th>Moisture</th>
                                <th>RD</th>
                                 <th>Show</th>
                               
                               
                                
                                
                          </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($row_arr)>0) {

                            
                            
                            $i=0;
                            foreach ($row_arr as $key => $value) {
                               
                              
                            ?>
                          

                          <tr>
                            <td><?php echo $i = $i+1 ?></td>

                            <td><?php echo date("d/m/Y", strtotime($value['invoice_date'])) ?></td>
                            
                            <td><?php echo $value['invice_no']; ?></td>

                            <td>
                              <?php 
                               $sql2 = "select * from party where id='".$value['firm']."'";
                              $result2 = mysqli_query($conn, $sql2);
                              $row2=mysqli_fetch_array($result2);
                               echo $row2['party_name'] 
                            ?>
    
                            </td>

                            <td>
                            <?php 
                                $sql2 = "select * from external_party where id='".$value['party_name']."'";
                                $result2 = mysqli_query($conn, $sql2);
                                $row2=mysqli_fetch_array($result2);
                                 echo $row2['partyname'] ?>
                           </td>



                            <td><?php 

                            $sql4 = "select * from external_party where id='".$value['shipping_ext_party_id']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['partyname'];
                            }
                            echo  $pname; ?></td>


                            <td><?php echo $value['delivery_city']; ?></td>


                            <td>
                            <?php 
                                $sql_var = "select * from product_sub_items where id='".$value['variety']."'";
                                $result_var = mysqli_query($conn, $sql_var);

                                $row_var = mysqli_fetch_assoc($result_var);
                                // print_r($row10);
                                $var_name='';
                                if(isset($row_var))
                                {
                                  $var_name=$row_var['value'];
                                }
                                echo  $var_name; 
                            ?>

                            </td>

                            <td>
                               <?php 
                                $sql_sub = "select * from product_sub_items where id='".$value['sub_variety']."'";
                                $result_sub = mysqli_query($conn, $sql_sub);

                                $row_sub = mysqli_fetch_assoc($result_sub);
                                // print_r($row10);
                                $subvar='';
                                if(isset($row_sub))
                                {
                                  $subvar=$row_sub['value'];
                                }
                                echo  $subvar; 
                            ?>

                            </td>


                            <td>
                               <?php 
                                 $sql_truck = "select * from truck_master where id='".$value['truck']."'";
                                   $result_truck = mysqli_query($conn, $sql_truck);
                               

                                $row_truck = mysqli_fetch_assoc($result_truck);
                                // print_r($row10);
                                $truck_name='';
                                if(isset($row_truck))
                                {
                                  $truck_name=$row_truck['truck_no'];
                                }
                                echo  $truck_name; 
                            ?>
                              
                            </td>




                            <td>
                            <?php
                              if($value['lot_no']!='' || $value['lot_no']!=null)
                              {
                                   $lot=json_decode($value['lot_no']);

                                   if($lot!=null)
                                   {
                                     echo implode(",",$lot);
                                   }
                                   else
                                   {
                                    echo '';
                                   }
                              }
                              else
                              {
                                echo '';
                              }
                            ?>

                            </td>


                            <td>

                              <?php
                                 if($value['lot_bales']!='' || $value['lot_bales']!=null)
                                  {
                                       $bales=json_decode($value['lot_bales']);
                                       if($bales!=null)
                                       {
                                        echo array_sum($bales);
                                       }
                                       else
                                       {
                                        echo '';
                                       } 
                                  }
                                  else
                                  {
                                   echo '';
                                  }
                              ?>
                              

                            </td>

                            <td><?php echo $value['start_pr']; ?></td>
                            <td><?php echo $value['end_pr']; ?></td>
                            <td><?php echo $value['candy_rate']; ?></td>

                            

                            <td><?php echo $value['total_value']; ?></td>

                            <td><?php echo $value['length']; ?></td>
                            <td><?php echo $value['strength']; ?></td>
                            <td><?php echo $value['mic']; ?></td>
                            <td><?php echo $value['trash']; ?></td>
                            <td><?php echo $value['moi']; ?></td>
                            <td><?php echo $value['rd']; ?></td>

                            
                           



                           <td><a href="show.php?id=<?php echo $value['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a></td>
                          
                          
                           
                          </tr>

                          <?php }
                           }

                        }


                          ?>

                          
                          
                          
                        </tbody>
                      </table>
                    </div>
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
    <script type="text/javascript">

       

    $(document).ready(function() {

      
       




      } );
    </script>
  </body>
</html>

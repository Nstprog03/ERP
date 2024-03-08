<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}

function getExternalPartyDetails($id)
{
    
    include('../db.php');
    $party_name='';
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $party_name=$partyrow['partyname'];
    }

    return $party_name;
}

function getTruckDetails($id)
{
    
    include('../db.php');
    $truck_no='';
    $party = "select * from truck_master where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $truck_no=$partyrow['truck_no'];
    }

    return $truck_no;
}
function convertDate2($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }


    return $final_date;

}


if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}
  

  if(isset($_POST['submit'])){

      $main_query="select * from seller_conf where ";
      $main_query2="select * from sales_conf_split where ";


      $start_date='';
      $end_date='';
      $where_cond = array(); // sales confirmation filter array
      $where_cond2 = array(); // sales confirmation split array
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $where_cond[] = " sales_date>='".$start_date."'";
        $where_cond2[] = " conf_split_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " sales_date<='".$end_date."'";
        $where_cond2[] = " conf_split_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

      
        $where_cond[] = " sales_date>='".$start_date."' AND sales_date<='".$end_date."'";
        $where_cond2[] = " conf_split_date>='".$start_date."' AND conf_split_date<='".$end_date."'";

      }

      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);

        $where_cond[] = " firm in (".$firm.")";
        $where_cond2[] = " firm in (".$firm.")";
      
      }


      //sales confirmation--------------------------------------------------------------------------

      
      if(!empty($where_cond)){
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where.' and conf_type != 2';
      }else{

        $main_query="select * from seller_conf where conf_type != 2";
      }


       
      $row_arr  = array();
       $result2 = mysqli_query($conn, $main_query);


       while($value = mysqli_fetch_assoc($result2)){

         $row_arr[] = $value;
       
       }



       $dataArr=array();
       foreach ($row_arr as $key => $value) {


        $dataArr[$key]['sales_conf_id']=$value['id'];

        $dataArr[$key]['conf_no']=$value['sales_conf'];

        $dataArr[$key]['conf_ext_party']=getExternalPartyDetails($value['external_party']);

        $dataArr[$key]['conf_date']=convertDate2($value['sales_date']);

        //firm
        $firm_name='';
        $sql_firm = "select * from party where id='".$value['firm']."'";
        $result_firm = mysqli_query($conn, $sql_firm);
        $row_firm=mysqli_fetch_array($result_firm);

        if(mysqli_num_rows($result_firm)>0)
        {
           $dataArr[$key]['firm']=$row_firm['party_name'];
        }

        $total_bales=$value['cont_quantity'];

        
        //if conf split created based on main confirmation then deduct conf split bales from total bales
        $sqlSCS="SELECT IFNULL(SUM(no_of_bales), 0) as used_bales FROM sales_conf_split WHERE conf_no='".$value['sales_conf']."' AND sale_conf_id='".$value['id']."'";
        $resultSCS = mysqli_query($conn, $sqlSCS);
        $rowScs2=$resultSCS->fetch_assoc();
        $total_bales-=(int)$rowScs2['used_bales'];


        $dataArr[$key]['total_bales']=(int)$total_bales;



        //get used bales from sales report
        $sales_bales=0;
        $sql_report = "select SUM(noOFBales) as used_bales from sales_report where conf_no='".$value['sales_conf']."' AND sales_ids='".$value['id']."'";
        $result_report = mysqli_query($conn, $sql_report);
        if(mysqli_num_rows($result_report)>0)
        {
          $rowReport=mysqli_fetch_assoc($result_report);
          $sales_bales+=(int)$rowReport['used_bales'];
        }

        $dataArr[$key]['sales_bales']=$sales_bales;


        $dataArr[$key]['pending_bales']=$total_bales-$sales_bales;

        $dataArr[$key]['candy_rate']=$value['candy_rate'];



        //prodcut variety
        if($value['variety']!='')
        {
            $sql_var = "select * from product_sub_items where id='".$value['variety']."'";
            $result_var = mysqli_query($conn, $sql_var);

            $row_var = mysqli_fetch_assoc($result_var);

            $dataArr[$key]['variety']=$row_var['value'];

        }
        else
        {
             $dataArr[$key]['variety']='';
        }


        //prodcut sub variety
        if($value['sub_variety']!='')
        {
            $sql_var = "select * from product_sub_items where id='".$value['sub_variety']."'";
            $result_var = mysqli_query($conn, $sql_var);

            $row_var = mysqli_fetch_assoc($result_var);

            $dataArr[$key]['sub_variety']=$row_var['value'];

        }
        else
        {
             $dataArr[$key]['sub_variety']='';
        }



        //if total bales is 0. it means all bales of main confirmation are used in split so need to remove that record from array
        if($dataArr[$key]['total_bales']==0)
        {
          unset($dataArr[$key]);
        }

       


      }


      $dataArr=array_values($dataArr); //reset array index to start from 0



      // sales conf split------------------------------------------------------------------------

     


      if(!empty($where_cond2)){
        $where2 = implode('AND',$where_cond2);
        $main_query2 = $main_query2.$where2.' and conf_type != 2';
      }else{

        $main_query2="select * from sales_conf_split where conf_type != 2";
      }

       
      $row_arr2  = array();
      $result2 = mysqli_query($conn, $main_query2);


       while($value = mysqli_fetch_assoc($result2)){

         $row_arr2[] = $value;
       
       }

        $lastKey=array_key_last($dataArr);
        $key=$lastKey+1;
       foreach ($row_arr2 as $value) {


        $dataArr[$key]['sales_conf_id']=$value['id'];


        $dataArr[$key]['conf_no']=$value['conf_split_no'];

        $dataArr[$key]['conf_ext_party']=getExternalPartyDetails($value['split_party_name']);

        $dataArr[$key]['conf_date']=convertDate2($value['conf_split_date']);

        //firm
        $firm_name='';
        $sql_firm = "select * from party where id='".$value['firm']."'";
        $result_firm = mysqli_query($conn, $sql_firm);
        $row_firm=mysqli_fetch_array($result_firm);

        if(mysqli_num_rows($result_firm)>0)
        {
           $dataArr[$key]['firm']=$row_firm['party_name'];
        }



        $total_bales=(int)$value['no_of_bales'];

        $dataArr[$key]['total_bales']=$total_bales;

        //get used bales from sales report
        $sales_bales=0;
        $sql_report = "select SUM(noOFBales) as used_bales from sales_report where conf_no='".$value['conf_split_no']."' AND sales_ids='".$value['id']."'";
        $result_report = mysqli_query($conn, $sql_report);
        if(mysqli_num_rows($result_report)>0)
        {
          $rowReport=mysqli_fetch_assoc($result_report);
          $sales_bales+=(int)$rowReport['used_bales'];
        }

        $dataArr[$key]['sales_bales']=$sales_bales;


        $dataArr[$key]['pending_bales']=$total_bales-$sales_bales;

        $dataArr[$key]['candy_rate']=$value['price'];



        //prodcut variety
        if($value['variety']!='')
        {
            $sql_var = "select * from product_sub_items where id='".$value['variety']."'";
            $result_var = mysqli_query($conn, $sql_var);

            $row_var = mysqli_fetch_assoc($result_var);

            $dataArr[$key]['variety']=$row_var['value'];

        }
        else
        {
             $dataArr[$key]['variety']='';
        }


        //prodcut sub variety
        if($value['sub_variety']!='')
        {
            $sql_var = "select * from product_sub_items where id='".$value['sub_variety']."'";
            $result_var = mysqli_query($conn, $sql_var);

            $row_var = mysqli_fetch_assoc($result_var);

            $dataArr[$key]['sub_variety']=$row_var['value'];

        }
        else
        {
             $dataArr[$key]['sub_variety']='';
        }


        $key++;
      
      }

      $dataArr=array_values($dataArr); //reset array index to start from 0


      //---------------------------------------------



        //remove if pending bales is zero
       if(isset($_POST['pendingBales']))
       {
         foreach ($dataArr as $key => $item) 
         {
            if($item['pending_bales']==0)
            {
              unset($dataArr[$key]);
            }
         }
           //reset array index
           $dataArr=array_values($dataArr);

       }


       //add index no.
       if(count($dataArr)>0)
       {
          $index=1;
          foreach ($dataArr as $key => $item) 
          {
            $dataArr[$key]['index_no']=$index;
            $index++;
          }
       }





        $_SESSION['pending_bales_report_export_data']=$dataArr;


  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Pending Bales Report</title>
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

       $(".datepicker2").datepicker({
        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
      });

       $(".datepicker").keydown(false);
       $(".datepicker2").keydown(false);

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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span>Pending Bales Report</span></a>
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

      <div class="container">
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
                             <select name="firm[]" class="form-control searchDropdown" multiple title="Select Firm"> 
                              
                            
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

                            <?php
                            $min_date='';
                            $sqlDate="SELECT MIN(sales_date) as min_date FROM `seller_conf`";
                            $resultDate = mysqli_query($conn, $sqlDate);
                            if(mysqli_num_rows($resultDate)>0)
                            {
                              $rowDate=mysqli_fetch_assoc($resultDate);

                                if($rowDate['min_date']!='' && $rowDate['min_date']!='0000-00-00')
                                {
                                 $min_date = date("d/m/Y", strtotime($rowDate['min_date']));
                                }
                            }
                            ?>
                         


                             <div class="form-group col-md-4">
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];}else{echo $min_date; } ?>" autocomplete="off">
                            </div>

                             <div class="form-group col-md-4">
                            
                              <label for="end_date">End Date :</label>
                                <input type="text" class="form-control datepicker2" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];}else{echo date('d/m/Y'); } ?>" autocomplete="off">
                            </div>

                            </div>

                            <div class="row">
                              <div class="col-md-4">
                               <div class="form-check">
                                <input class="form-check-input" type="checkbox"name="pendingBales" value="1" <?php if(isset($_POST['pendingBales'])){echo 'checked';} ?>>
                                <label class="form-check-label">Show Only Pening Bales</label>
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
          

                <div class="card mt-4">
                   
                      <div class="card-body">

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
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>External Party</th>
                                <th width="15%">Sales Conf. No.</th>
                                <th>Sales Conf. Date</th>
                                <th>Firm</th>
                                <th>Candy Rate</th>
                                <th>Variety</th>
                                <th>Total Bales</th>
                                <th>Sales Bales</th>
                                <th>Pending Bales</th>                               
                                <th>Sub Variety</th>

                                </tr>
                        </thead>
                        <tfoot>
                          
                          <tr>
                                <th></th>
                                <th>ID</th>
                                <th>External Party</th>
                                <th>Sales Conf. No.</th>
                                <th>Sales Conf. Date</th>
                                <th>Firm</th>
                                <th>Candy Rate</th>
                                <th>Variety</th>
                                <th>Total Bales</th>
                                <th>Sales Bales</th>
                                <th>Pending Bales</th>                                
                                 <th>Sub Variety</th>

                          </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($dataArr)>0) {

                       
                            foreach ($dataArr as $key => $value) {
                               
                              
                            ?>
                          

                          <tr>
                            <td data-toggle="collapse" data-target="#row<?php echo $key ?>"><i class="fas fa-chevron-right"></i></td>

                            <td><?php echo $value['index_no'] ?></td>

                            <td><?php echo $value['conf_ext_party'] ?></td>

                            
                            <td><?php echo $value['conf_no']; ?></td>

                             <td><?php echo $value['conf_date']; ?></td>

                            <td><?php echo $value['firm']; ?></td>

                             <td><?php echo $value['candy_rate']; ?></td>

                            <td><?php echo $value['variety']; ?></td>

                            <td><?php echo $value['total_bales']; ?></td>

                            <td><?php echo $value['sales_bales']; ?></td>

                            <td><?php echo $value['pending_bales']; ?></td>

                           

                            <td><?php echo $value['sub_variety']; ?></td>

                          </tr>

                          <tr id="row<?php echo $key ?>" class="collapse">
                            <td colspan="11">

                                <table class="table">                                
                                    <thead>
                                      <tr>
                                        <th>Invoice Date</th>
                                        <th>Invoice No</th>
                                        <th>External Party</th>
                                        <th>Delivery City</th>
                                        <th>No Of Bale</th>
                                        <th>Truck No</th>
                                      </tr>
                                    </thead> 
                                    <tbody>
                                      <?php 
                                        $sqlReport="select * from sales_report where conf_no='".$value['conf_no']."' AND sales_ids='".$value['sales_conf_id']."'";
                                        $resultReport=mysqli_query($conn,$sqlReport);
                                        if(mysqli_num_rows($resultReport)>0)
                                        {
                                          while ($row2=mysqli_fetch_assoc($resultReport)) {

                                              //invoice Date
                                              $invoice_date='';
                                              if($row2['invoice_date']!='' && $row2['invoice_date']!='0000-00-00')
                                              {
                                                $invoice_date=date("d/m/Y", strtotime($row2['invoice_date']));
                                              }


                                             
                                        ?>
                                         <tr>
                                          <td><?php echo $invoice_date ?></td>
                                          <td><?php echo $row2['invice_no'] ?></td>
                                          <td><?php echo getExternalPartyDetails($row2['party_name']) ?></td>

                                          <td><?php echo $row2['delivery_city'] ?></td>

                                          <td><?php echo $row2['noOFBales'] ?></td>
                                          <td><?php echo getTruckDetails($row2['truck']) ?></td>
                                        </tr>

                                        <?php                                            
                                          }
                                        }

                                      ?>
                                     
                                    </tbody>                                  
                                </table>
                              
                            </td>
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
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>


          <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


    <script type="text/javascript">

    $(document).ready(function() {

      
       




      } );
    </script>
  </body>
</html>

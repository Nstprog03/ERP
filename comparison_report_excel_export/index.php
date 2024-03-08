<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}



function getExternalPartyDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

    $arr[0]=$partyrow['partyname'];

    $arr[1]=$partyrow['address'].', '.$partyrow['city'].', '.$partyrow['district'].', '.$partyrow['state'].' - '.$partyrow['pincode'];

    return $arr;

}



function getFirmDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

   $firm=$partyrow['party_name'];

    return $firm;

}


//dd/mm/yyy
function convertDate($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }


    return $final_date;

}

function firmFilter($mainArr,$firmArr){

  $firms=$firmArr;
  $arr=array();

  foreach ($mainArr as $key => $item) 
  {
    if(in_array($item['firm_id'],$firms))
    {
       $arr[]=$item;
    }
  }
  return $arr;

}

function dateFilter($start_date,$end_date,$arr)
{

    $newArr=array();

    foreach ($arr as $key => $item) 
    {
      if($start_date!='' && $end_date=='')
      {
          if($item['sales_invoice_date']>=$start_date)
          {
            $newArr[]=$item;
          }
      }
      else if($start_date=='' && $end_date!='')
      { 
          if($item['sales_invoice_date']<=$end_date)
          {
            $newArr[]=$item;
          }
      }
      else if($start_date!='' && $end_date!='')
      {
          if($item['sales_invoice_date']>=$start_date && $item['sales_invoice_date']<=$end_date)
          {
            $newArr[]=$item;
          }
      }
      
    }
    
    return $newArr;

}



if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  

  if(isset($_POST['submit']))
  {

      $start_date='';
      $end_date='';
      $dateFilterArr = array();
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));
        $dateFilterArr['start_date'] = $start_date;
        $dateFilterArr['end_date'] = '';
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));
         $dateFilterArr['start_date'] = '';
        $dateFilterArr['end_date'] = $end_date;
       
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $dateFilterArr['start_date'] = $start_date;
        $dateFilterArr['end_date'] = $end_date;

      }


      $firmFilter=array();
      if(isset($_POST['firm']))
      {
        $firmFilter=$_POST['firm'];
      }

     

      $main_query="select * from comparison_report order by id desc";
    

      $mainArr  = array();
      $i=0;
  
     $result = mysqli_query($conn, $main_query);
     while($row = mysqli_fetch_assoc($result))
     {

        $salesArr=json_decode($row['sales_data'],true);
        $sales_report_id=$salesArr[0]['sales_report_id'];


          //get sales external party & conf no.
          $sales_ext_party='';
          if($sales_report_id!='')
          {
             $sqlReport = "select conf_no,party_name from sales_report where id='".$sales_report_id."'";
             $resultReport=mysqli_query($conn,$sqlReport);
             if(mysqli_num_rows($resultReport)>0)
             {
                $rowReport=mysqli_fetch_assoc($resultReport);
                //get external party_details
                  $ext='';
                  $sqlEx = "select partyname from external_party where id='".$rowReport['party_name']."'";
                  $resultEx = mysqli_query($conn, $sqlEx);
                  if(mysqli_num_rows($resultEx)>0)
                  {
                    $rowEx=mysqli_fetch_assoc($resultEx);
                    $ext=$rowEx['partyname'];
                  }
                $sales_ext_party=$ext.' ('.$rowReport['conf_no'].')';
             }

          }
        $mainArr[$i]['sales_party']=$sales_ext_party;
        $mainArr[$i]['sales_invoice_no']=$salesArr[0]['invoice_no'];
        $mainArr[$i]['sales_invoice_date']=$salesArr[0]['invoice_date'];



        //get veh No. from id
        $veh_no='';
        $sqlTruck="select * from truck_master where id='".$salesArr[0]['veh_id']."'";
        $resultTruck = mysqli_query($conn, $sqlTruck);
        if(mysqli_num_rows($resultTruck)>0)
        {
          $rowTruck=mysqli_fetch_assoc($resultTruck);
          $veh_no=$rowTruck['truck_no'];
        }
        $mainArr[$i]['sales_veh_no']=$veh_no;

        $mainArr[$i]['sales_lot_no']=$salesArr[0]['lot_no'];

        $mainArr[$i]['sales_lot_bales']=$salesArr[0]['lot_bales'];

        $mainArr[$i]['own_bales']=$row['sales_bales'];

        $mainArr[$i]['delivery_at']=$row['delivery_at'];


        //invoice raise name (get firm name)
          $firm_name='';
          $sqlFirm="select * from party where id='".$row['invoice_raise']."'";
          $resultFirm = mysqli_query($conn, $sqlFirm);
          if(mysqli_num_rows($resultFirm)>0)
          {
            $rowFirm=mysqli_fetch_assoc($resultFirm);
            $firm_name=$rowFirm['party_name'];
            $firm_id=$rowFirm['id'];
          }

          $mainArr[$i]['invoice_raise_name']=$firm_name;
          $mainArr[$i]['firm_id']=$row['invoice_raise'];



        //purchase section
        $purchaseArr=json_decode($row['purchase_data'],true);
        foreach ($purchaseArr as $key => $item) 
        {
          if($key==0)
          {
            $mainArr[$i]['use_external_bales']=$item['total_dispatch_bales'];
            $mainArr[$i]['pur_ext_party']=$item['ext_conf_no'];
          }
          else
          {
              $mainArr[$i]=$mainArr[$i-1];
          

              $mainArr[$i]['use_external_bales']=$item['total_dispatch_bales'];
              $mainArr[$i]['pur_ext_party']=$item['ext_conf_no'];
          }
          

          $i++;
        }
         $i++;
    }


    //filter by firm
    if(count($firmFilter)>0)
    {
      $mainArr=firmFilter($mainArr,$firmFilter);
    }

    //datefilter
    if(count($dateFilterArr)>0)
    {
      $star_date=$dateFilterArr['start_date'];
      $end_date=$dateFilterArr['end_date'];
      $mainArr=dateFilter($star_date,$end_date,$mainArr);
    }


    /*echo '<pre>';
    print_r($mainArr);
    exit;*/


    //final filter remove sales data if invoice no. is same
    $arr1=array();
    $last_invoice='';
    foreach ($mainArr as $key => $item) 
    {
        if($last_invoice==$item['sales_invoice_no'])
        {
            foreach ($item as $k => $value) {

                if($k!='pur_ext_party' && $k!='use_external_bales')
                {
                  $item[$k]='';
                }
            }
            $arr1[]=$item;
        }
        else
        {
            $arr1[]=$item;
            $last_invoice=$item['sales_invoice_no'];
        }


    }

    $mainArr=$arr1;

    

    $_SESSION['comparison_report_export_data']=$mainArr;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Comparison Report Excel Export</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Comparison Report Excel Export</span></a>
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
                        <select name="firm[]" class="form-control searchDropdown" title="Select Option" multiple> 
                              
                            
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
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} ?>" autocomplete="off">
                            </div>

                            <div class="form-group col-md-4">
                              <label for="end_date">End Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} ?>" autocomplete="off">
                            </div>

                           


                      </div>
                      <div class="row">
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
                   
                      <div class="card-body export-data">

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

                              <th>ID</th>
                              <th>Sales Party</th>
                              <th>Sales Invoice No.</th>
                              <th>Sales Invoice Date</th>
                              <th>Sales Vehicle No</th>
                              <th>Sales LOT No.</th>
                              <th>Sales LOT Bales</th>
                              <th>Own Bales</th>
                              <th>Use of External Bales</th>
                              <th>External Party</th>
                              <th>Delivery At</th>
                              <th>Invoice Raise in the Name</th>
                           
                                
                           </tr>

                        </thead>
                        <tfoot>
                          
                           
                          <tr>

                              <th>ID</th>
                              <th>Sales Party</th>
                              <th>Sales Invoice No.</th>
                              <th>Sales Invoice Date</th>
                              <th>Sales Vehicle No</th>
                              <th>Sales LOT No.</th>
                              <th>Sales LOT Bales</th>
                              <th>Own Bales</th>
                              <th>Use of External Bales</th>
                              <th>External Party</th>
                              <th>Delivery At</th>
                              <th>Invoice Raise in the Name</th>
                                
                           </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($mainArr)>0) {
                            $i=0;
                            foreach ($mainArr as $key => $row) {  
                            ?>
                          

                          <tr>
                            <td>
                                <?php
                                if($row['sales_invoice_no']!='')
                                {
                                      echo $i = $i+1;
                                      
                                }
                                
                                ?>                                
                              </td>


                            <td><?php echo $row['sales_party'] ?></td>
                            <td><?php echo $row['sales_invoice_no'] ?></td>
                            <td><?php echo convertDate($row['sales_invoice_date']) ?>
                            <td><?php echo $row['sales_veh_no'] ?></td>
                            <td><?php echo $row['sales_lot_no'] ?></td>
                            <td><?php echo $row['sales_lot_bales'] ?></td>
                            <td><?php echo $row['own_bales'] ?></td>

                            <td><?php if(isset($row['use_external_bales'])){ echo $row['use_external_bales'];} ?></td>
                            <td><?php if(isset($row['pur_ext_party'])){ echo $row['pur_ext_party'];} ?></td>
                            <td><?php echo $row['delivery_at'] ?></td>
                            <td><?php echo $row['invoice_raise_name'] ?></td>
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

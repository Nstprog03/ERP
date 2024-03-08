<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}



function getExternalPartyDetails($id)
{
    $external_party='';
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);
    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $external_party=$partyrow['partyname'];
    }
    return $external_party;
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

function extPartFilter($mainArr,$extPartyArr){

  $extPartys=$extPartyArr;
  $arr=array();

  foreach ($mainArr as $key => $item) 
  {
    if(in_array($item['ext_party_id'],$extPartys))
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

function searchArr($mainArr,$id){

  foreach ($mainArr as $key => $item) 
  {
    if($item['id']==$id)
    {
       return $key;
    }
  }
}



if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  

  if(isset($_POST['submit']))
  {

     $main_query="select id,firm,party,invoice_no,lot_no,bales,conf_no from pur_report where";

     if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);
        $where_cond[] = " firm in (".$firm.")";
        
      }

      if(isset($_POST['ext_party_id']))
      {
        $ext_party="'".implode("','",$_POST['ext_party_id'])."'";
        $where_cond[] = " party in (".$ext_party.")";
      }

      if(!empty($where_cond))
      {
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where.' order by id DESC';
      }else
      {

        $main_query="select id,firm,party,invoice_no,lot_no,bales,conf_no from pur_report order by id desc";
      }


    $mainArr  = array();
    $i=0;
    $result = mysqli_query($conn, $main_query);
    while($row = mysqli_fetch_assoc($result))
    {
      $mainArr[$i]=$row;
      $mainArr[$i]['total_dispatch_bales']=0;
      $mainArr[$i]['firm_name']=getFirmDetails($row['firm']);
      $mainArr[$i]['ext_conf_no']=getExternalPartyDetails($row['party']).'('.$row['conf_no'].')';



      $i++;
    }

    


    //get data from comparision report
    $usedArr=array();
    $sqlCr="SELECT purchase_data FROM `comparison_report`";
    $result2 = mysqli_query($conn, $sqlCr);
    $i=0;
    while($row2 = mysqli_fetch_assoc($result2))
    {
      $dynamicData=json_decode($row2['purchase_data'],true);
      $usedArr[$i]=$dynamicData;
      $i++;
    }

   //remove empty array
   $usedArr=array_filter($usedArr);
   //merge array
   $usedArr=array_merge(...$usedArr);






   foreach ($usedArr as $key => $item) 
   {
      $getKey=searchArr($mainArr,$item['purchase_report_id']);
      if(isset($getKey))
      {
        $mainArr[$getKey]['total_dispatch_bales']+=(int)$item['total_dispatch_bales'];
      }
   }


   //if bales & dispatch bales matched it means fully used remove from array
   foreach ($mainArr as $key => $item) 
   {
     if($item['bales']==$item['total_dispatch_bales'])
     {
      unset($mainArr[$key]);
     }
   }
  $mainArr=array_values($mainArr);




    $_SESSION['comparison_pending_register_export_data']=$mainArr;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Comparison Pending Register</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Comparison Pending Register</span></a>
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
                    <div class="row">

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
                      <label for="ext_party_id">Select External Party</label>
                      <?php
                        $sql = "select * from external_party";
                        $result = mysqli_query($conn, $sql);
                      ?>                       
                      <select data-live-search="true" class="form-control searchDropdown" title="Select Option" name="ext_party_id[]" multiple="">
                        <?php                   
                          foreach ($conn->query($sql) as $result) 
                          {
                             if(isset($_POST['ext_party_id']) && in_array($result['id'], $_POST['ext_party_id']))
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
                              <th>Firm</th>
                              <th>External Party & Conf. No.</th>
                              <th>Invoice No.</th>
                              <th>Lot No.</th>
                              <th>Total Bales</th>
                              <th>Used Bales</th>
                              <th>Available Bales</th>
                            
                           
                                
                           </tr>

                        </thead>
                        <tfoot>
                          
                           
                          <tr>

                              <th>ID</th>
                              <th>Firm</th>
                              <th>External Party & Conf. No.</th>
                              <th>Invoice No.</th>
                              <th>Lot No.</th>
                              <th>Total Bales</th>
                              <th>Used Bales</th>
                              <th>Available Bales</th>
                                
                           </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($mainArr)>0) {
                            $i=0;
                            foreach ($mainArr as $key => $row) {  


                              $total_bales=(int)$row['bales'];
                              $used_bales=(int)$row['total_dispatch_bales'];
                              $avl_bales=$total_bales-$used_bales;

                            ?>
                          

                          <tr>
                            <td><?php echo $i = $i+1; ?> </td>
                            <td><?php echo $row['firm_name'] ?></td>
                            <td><?php echo $row['ext_conf_no'] ?></td>
                            <td><?php echo $row['invoice_no'] ?></td>
                            <td><?php echo $row['lot_no'] ?></td>
                            <td><?php echo $total_bales ?></td>
                            <td><?php echo $used_bales ?></td>
                            <td><?php echo $avl_bales ?></td>
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

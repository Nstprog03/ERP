<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}



function getExternalPartyDetails($id)
{
    $ext_party='';
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $ext_party=$partyrow['partyname'];
    }
    return $ext_party;
}

function getBrokerDetails($id)
{
    $broker;
    include('../db.php');
    $brokerSQL = "select * from broker where id='".$id."'";
    $brokerResult = mysqli_query($conn, $brokerSQL);

    if(mysqli_num_rows($brokerResult)>0)
    {
      $brokerRow = mysqli_fetch_array($brokerResult);
      $broker=$brokerRow['name'];
    }
    return $broker;
}

function getFirmDetails($id)
{
    $firm='';
    include('../db.php');
    $party = "select * from party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    if(mysqli_num_rows($partyresult)>0)
    {
      $partyrow = mysqli_fetch_array($partyresult);
      $firm=$partyrow['party_name'];
    }

    return $firm;

}

function array_sort_by_column(&$array, $column, $direction = SORT_DESC) {
    $reference_array = array();

    foreach($array as $key => $row) {
        $reference_array[$key] = $row[$column];
    }

    array_multisort($reference_array, $direction, $array);
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


function dateFilter($start_date,$end_date,$arr)
{

    $newArr=array();

    foreach ($arr as $key => $item) 
    {
      if($start_date!='' && $end_date=='')
      {
          if($item['sales_date']>=$start_date)
          {
            $newArr[]=$item;
          }
      }
      else if($start_date=='' && $end_date!='')
      { 
          if($item['sales_date']<=$end_date)
          {
            $newArr[]=$item;
          }
      }
      else if($start_date!='' && $end_date!='')
      {
          if($item['sales_date']>=$start_date && $item['sales_date']<=$end_date)
          {
            $newArr[]=$item;
          }
      }
      
    }
    
    return $newArr;

}

function paymentFilter($mainArr,$pay_status)
{
  $arr=array();
  foreach ($mainArr as $key => $item) 
  {
    if($item['pay_status']==$pay_status)
    {
       $arr[]=$item;
    }
  }
  return $arr;

}



if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  

  if(isset($_POST['submit']))
  {


      $main_query="select * from kapasiya where";

      $where_cond = array();


      //for json data filter
      $start_date='';
      $end_date='';
      $dateFilterArr=array();
      
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


      //filter in sql query
      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);
        $where_cond[] = " firm in (".$firm.")";
        
      }

      if(isset($_POST['ext_party']))
      {
        $ext_party="'".implode("','",$_POST['ext_party'])."'";
        $where_cond[] = " party in (".$ext_party.")";
      }

      if(isset($_POST['broker']))
      {
        $broker="'".implode("','",$_POST['broker'])."'";
        $where_cond[] = " broker in (".$broker.")";
      }

 
      if(!empty($where_cond))
      {
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where;
      }else
      {

        $main_query="select * from kapasiya order by id desc";
      }



      $mainArr  = array();
      $i=0;
  
      $result = mysqli_query($conn, $main_query);

         while($row = mysqli_fetch_assoc($result))
         {

            $mainArr[$i]['firm'] = getFirmDetails($row['firm']);
            $mainArr[$i]['ext_party'] = getExternalPartyDetails($row['party']);
            $mainArr[$i]['broker'] = getBrokerDetails($row['broker']);

            $truckArr=json_decode($row['truck'],true);
            foreach ($truckArr as $key => $item) 
            {
              if($key==0)
              {
                $mainArr[$i]['invoice_no']=$item['invoice_no'];
                $mainArr[$i]['sales_date']=$item['sales_date'];
                $mainArr[$i]['weight']=$item['weight'];
                $mainArr[$i]['final_amount']=$item['final_amt'];
                $mainArr[$i]['pay_status']=$item['payment_status'];
              }
              else
              {
                $mainArr[$i]=$mainArr[$i-1];
                $mainArr[$i]['invoice_no']=$item['invoice_no'];
                $mainArr[$i]['sales_date']=$item['sales_date'];
                $mainArr[$i]['weight']=$item['weight'];
                $mainArr[$i]['final_amount']=$item['final_amt'];
                $mainArr[$i]['pay_status']=$item['payment_status'];
                
              }
              $i++;

            }

           $i++;
         
      }


    //filter by payment status
    if(isset($_POST['pay_status']))
    {
      $mainArr=paymentFilter($mainArr,$_POST['pay_status']);
    }



      //datefilter
    if(count($dateFilterArr)>0)
    {
      $start_date=$dateFilterArr['start_date'];
      $end_date=$dateFilterArr['end_date'];
      $mainArr=dateFilter($start_date,$end_date,$mainArr);
    }


    //sort by date (latest)
    array_sort_by_column($mainArr, 'sales_date');



    $_SESSION['kapasiya_register_export_data']=$mainArr;


  }




?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Kapasiya Sales Register</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Kapasiya Sales Register</span></a>
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
                        <select name="firm[]" class="form-control searchDropdown" data-live-search="true" title="Select Option" multiple> 
                              
                            
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
                              <select name="ext_party[]" class="form-control searchDropdown" data-live-search="true" title="Select Option" multiple>
                                
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
                          <label for="broker">Select Broker</label>
                          <?php
                            $Broker_sql = "select * from broker";
                            $Broker_result = mysqli_query($conn, $Broker_sql);                            
                          ?>                      
                          <select name="broker[]" class="form-control searchDropdown" multiple data-live-search="true" title="Select Option">
                            <?php                   
                              foreach ($conn->query($Broker_sql) as $Broker_result) 
                              {
                                 if(isset($_POST['broker']) && in_array($Broker_result['id'], $_POST['broker']))
                                 {

                                    echo "<option  value='".$Broker_result['id']."' selected>" .$Broker_result['name']. "</option>";
                                 }
                                 else
                                 {
                                     echo "<option  value='".$Broker_result['id']."'>" .$Broker_result['name']. "</option>";
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

                            <div class="form-group col-md-4">
                              <label for="pay_status">Select Payment Status</label>
                              <?php
                                $sql = "select * from external_party";
                                $result = mysqli_query($conn, $sql);
                              ?>                      
                              <select name="pay_status" class="form-control searchDropdown">
                                  <?php 
                                $statusArr=["pending","complete"];
                                foreach ($statusArr as $key => $item) 
                                {
                                    if(isset($_POST['pay_status']) && $item==$_POST['pay_status'])
                                     {

                                        echo "<option  value='".$item."' selected>" .$item. "</option>";
                                     }
                                     else
                                     {
                                        echo "<option  value='" .$item. "'>" .$item. "</option>";
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
                   
                      <div class="card-body expoert-register">

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
                                <th>Firm Name</th>
                                <th>External Party</th>
                                <th>Broker</th>
                                <th>Sales Date</th>
                                <th>Invoice No</th>
                                <th>Weight</th>
                                <th>Final Amount</th>
                                <th>Payment Status</th>
                              </tr>
                        </thead>
                        <tfoot>
                          <tr>
                              <th>ID</th>
                              <th>Firm Name</th>
                              <th>External Party</th>
                              <th>Broker</th>
                              <th>Sales Date</th>
                              <th>Invoice No</th>
                              <th>Weight</th>
                              <th>Final Amount</th>
                              <th>Payment Status</th>
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
                            <td><?php echo $i = $i+1 ?></td>
                            <td><?php echo $row['firm'] ?></td>
                            <td><?php echo $row['ext_party'] ?></td>
                            <td><?php echo $row['broker'] ?></td>
                            <td><?php echo convertDate($row['sales_date']) ?></td>
                            <td><?php echo $row['invoice_no'] ?></td>
                            <td><?php echo $row['weight'] ?></td>
                            <td><?php echo $row['final_amount'] ?></td>
                            <td><?php echo $row['pay_status'] ?></td>

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

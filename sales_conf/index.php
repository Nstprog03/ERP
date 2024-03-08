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

$shortYear='';
$getFullYear='';

foreach ($year_array as $key => $value) {
  $shortYear=$shortYear.date("y", strtotime($value));

    if($key==0)
    {
       $getFullYear=$getFullYear.date("Y", strtotime($value));
    }
    else 
    {
       $getFullYear=$getFullYear.'-'.date("Y", strtotime($value));
    }
  
}



  if(isset($_GET['delete']))
  {
    $id = $_GET['delete'];
    $sql = "select * from seller_conf where id = ".$id;
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0)
    {
      $row = mysqli_fetch_assoc($result);

      $rowcount = 0;
      $msg = '';
      $sql_split = "select * from sales_conf_split where conf_no = '".$row['sales_conf']."'"; 
      $result_split = mysqli_query($conn, $sql_split);
      $splitRowCount = mysqli_num_rows($result_split);
      $rowcount +=  $splitRowCount;
      if ($splitRowCount!=0) {
        $msg = $msg.' Sales Confirmation Split';
      }
      

      $sql_report = "select * from sales_report where conf_no = '".$row['sales_conf']."'"; 
      $result_report = mysqli_query($conn, $sql_report);

      $reportrowcount =  mysqli_num_rows($result_report);
      $rowcount +=  $reportrowcount;
      if ($reportrowcount!=0) {
        $msg = $msg.' - Sales Report ';
      }


      //count sales report in baes of sales conf split
      $sql_spreport="select * from sales_report sr, sales_conf_split sp where sr.conf_no=sp.conf_split_no AND sp.conf_no='".$row['sales_conf']."'";
      $result_spreport = mysqli_query($conn, $sql_spreport);
      $spreport_rowcount =  mysqli_num_rows($result_spreport);
      if ($spreport_rowcount!=0) {
        $msg = $msg.' - Sales Report';
      }

  
      
      if ($rowcount>0) {


        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        

        if($spreport_rowcount>0 || $reportrowcount>0)
        {
             $alert= 'Sorry You Can Not Delete This Record Beacuse this record already used in'.$msg;



             echo "<script type='text/javascript'>alert('$alert');
             window.location.href='index.php?page=$page';
              </script>";
        }
        else
        {
           $sql = "update seller_conf set conf_type='2' where id=".$id;
           $sql2 = "update sales_conf_split set conf_type='2' where sale_conf_id=".$id;

           if(mysqli_query($conn, $sql) && mysqli_query($conn, $sql2)){
                header("Location: index.php?page=$page");
            }

        }

       
 
      }else{
       // $sql = "delete from seller_conf where id=".$id;
        $sql = "update seller_conf set conf_type='2' where id=".$id;

        if(mysqli_query($conn, $sql)){
          header('location:index.php');
        }
      }
    }
  }



if(isset($_POST['clearFilter']))
{
  unset ($_SESSION["sconf_filter_data"]);
  unset ($_SESSION["sconf_filter_selected"]);
  header("location:index.php");
}


  $isFilter=false;
  $selectedArr=array();
  if (isset($_POST['filter']) || isset($_SESSION['sconf_filter_data'])) 
  {

    $isFilter=true;

    $main_query="select * from seller_conf where";
    $where_cond = array();

    if(isset($_POST['filter']))
    {
    
          // ext_party
          if(isset($_POST['party']))
          {
            $ext_party=implode(",",$_POST['party']);
            $where_cond[] = " external_party in (".$ext_party.")";

          }

          //broker
          if(isset($_POST['broker']))
          {
            $broker=implode(",",$_POST['broker']);
            $where_cond[] = " broker in (".$broker.")";

          }

          // product
          if(isset($_POST['product_name']))
          {
            $product_name=implode(",",$_POST['product_name']);
            $where_cond[] = " product in (".$product_name.")";

          }

          // conf_type
          if(isset($_POST['conf_type']))
          {
            $conf_type=implode(',',$_POST['conf_type']);
            $where_cond[] = " conf_type in (".$conf_type.")";

          }

          // trans_ins
          if(isset($_POST['trans_ins']))
          {
            $trans_ins=implode("','",$_POST['trans_ins']);
            $where_cond[] = " trans_ins in ('".$trans_ins."')";

          }

          // shipping_ext_party_id
          if(isset($_POST['shipping_ext_party_id']))
          {
            $shipping_ext_party_id=implode(',',$_POST['shipping_ext_party_id']);
            $where_cond[] = " shipping_ext_party_id in (".$shipping_ext_party_id.")";

          }
          
          // print_r($conf_type);exit();
          $start_date='';
            $end_date='';
            if($_POST['start_date']!='' && $_POST['end_date']=='')
            {
              $start_date = str_replace('/', '-', $_POST['start_date']);
              $start_date = date('Y-m-d', strtotime($start_date));

              $where_cond[] = " sales_date>='".$start_date."'";
            }

            if($_POST['start_date']=='' && $_POST['end_date']!='')
            {
              $end_date = str_replace('/', '-', $_POST['end_date']);
              $end_date = date('Y-m-d', strtotime($end_date));

              $where_cond[] = " sales_date<='".$end_date."'";
            }

           
            if($_POST['start_date']!='' && $_POST['end_date']!='')
            {

              $start_date = str_replace('/', '-', $_POST['start_date']);
              $start_date = date('Y-m-d', strtotime($start_date));

              $end_date = str_replace('/', '-', $_POST['end_date']);
              $end_date = date('Y-m-d', strtotime($end_date));

              $where_cond[] = " sales_date>='".$start_date."' AND sales_date<='".$end_date."'";

            }
              $selectedArr=$_POST;
          $_SESSION['sconf_filter_selected']=$selectedArr;
          $_SESSION['sconf_filter_data']=$where_cond;
    }
    else
    {
      $where_cond=$_SESSION['sconf_filter_data'];
      $selectedArr=$_SESSION['sconf_filter_selected'];
    }

      $i=0;

    if(!empty($where_cond)){
     
      $where = implode('AND',$where_cond);
     $main_query = $main_query.$where." AND firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id ='".$_SESSION['sales_financial_year_id']."'";

       $result = mysqli_query($conn, $main_query);



    }else{
      $_POST = array();
       //pagination  ------------------
        $per_page_record = 10;         
        if (isset($_GET["page"])) 
        {    
            $page  = $_GET["page"];    
        }    
        else 
        {    
          $page=1;    
        }  

        //id auto increment
        $i=0;
        $i=($page*10)-10;  

        $start_from = ($page-1) * $per_page_record;  
        $sql = "select * from seller_conf where firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' ORDER BY id DESC LIMIT $start_from, $per_page_record";     
        $result = mysqli_query($conn, $sql);
        }
    
  }else{

    
    //pagination  ------------------
    $per_page_record = 10;         
    if (isset($_GET["page"])) 
    {    
        $page  = $_GET["page"];    
    }    
    else 
    {    
      $page=1;    
    }   

     //id auto increment
        $i=0;
        $i=($page*10)-10;   

    $start_from = ($page-1) * $per_page_record;  
     $sql = "select * from seller_conf where firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' ORDER BY id DESC LIMIT $start_from, $per_page_record";     
    $result = mysqli_query($conn, $sql);

  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales Confirmation Database</title>
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
     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html");

      $('.searchDropdown').selectpicker();

      $(".datepicker").keydown(false);



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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span>Sales Confirmation Database</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
               <ul class="navbar-nav">

                 <li class="nav-item mr-2">
                   <button type="button" name="viewfilter" id="viewfilter" class="btn btn-outline-primary"><i class="fa fa-filter"></i><span class="pl-1">View Filter</span></button>

                  <script type="text/javascript">

                    $(document).ready(function() 
                    {
                      $(".viewfilter").hide();
                      var check = false;
                      var checkPageLoad="<?php echo $isFilter ; ?>";

                      if(checkPageLoad==true)
                      {
                        $(".viewfilter").show();
                         check=true;
                      }                    
                      $("#viewfilter").click(function(){
                      }); 

                      $("#viewfilter").click(function(){
                            if(check==false)
                            {
                              $(".viewfilter").show();
                              check=true;
                            }
                            else
                            {
                                 $(".viewfilter").hide();
                                 check=false;
                            }
                        
                      }); 
                    });
                  </script>

              </li>

              <li class="nav-item mr-2">
                  <form action="../sales_conf_index.php" method="post">
                    <input type="hidden" name="financial_year" value="<?php echo $_SESSION['sales_conf_financial_year']; ?>">
                    <input type="hidden" name="firm" value="<?php echo $_SESSION['sales_conf_firm'].'/'.$_SESSION['sales_conf_firm_id']; ?>">

                 <button type="submit" name="submit" class="btn btn-outline-danger"><i class="fa fa-sign-out-alt"></i><span class="pl-1">Back</span></button>
                 </form>
              </li>

              <li class="nav-item mr-2"><a class="btn btn-outline-secondary" href="/sales_conf_index.php"><i class="fa fa-undo-alt"></i> Pre-Selection</a></li>


                <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>

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
          $sqlLastChange="select username,updated_at from seller_conf where
          financial_year_id='".$_SESSION['sales_financial_year_id']."' 
          AND firm='".$_SESSION['sales_conf_firm_id']."' order by updated_at DESC LIMIT 1";

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
          <div class="card viewfilter mt-2">
              <div class="card-header">Filter</div>
                <div class="card-body">

                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="row">
                        <div class="form-group col-md-4">
                    <label for="party[]">Select External Party</label>

                    <?php
                      $ext_Party = "select * from external_party";
                      $ext_Partyresult = mysqli_query($conn, $ext_Party);
                    ?>                      
                    <select name="party[]" data-live-search="true" class="form-control searchDropdown" title="Select" multiple="">
                      <?php                   
                        foreach ($conn->query($ext_Party) as $ext_Partyresult) 
                        {

                           if(isset($selectedArr['party']) && in_array($ext_Partyresult['id'], $selectedArr['party']))
                           {

                               echo "<option  value='".$ext_Partyresult['id']."' selected>" .$ext_Partyresult['partyname']. "</option>";
                           }
                           else
                           {
                               echo "<option  value='".$ext_Partyresult['id']."'>" .$ext_Partyresult['partyname']. "</option>";
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
                    <select name="broker[]" data-live-search="true" class="form-control searchDropdown" title="Select" multiple="">
                      <?php                   
                        foreach ($conn->query($Broker_sql) as $Broker_result) 
                        {
                           if(isset($selectedArr['broker']) && in_array($Broker_result['id'], $selectedArr['broker']))
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
                    <label for="product_name">Select Product</label>


                    <?php
                      $Productquery = "select * from products";
                      $productResult = mysqli_query($conn, $Productquery);
                    ?>                      
                    <select name="product_name[]" data-live-search="true" class="form-control searchDropdown" title="Select" multiple="">
                      <?php                   
                        foreach ($conn->query($Productquery) as $productResult) 
                        {

                          if(isset($selectedArr['product_name']) && in_array($productResult['id'], $selectedArr['product_name']))
                           {

                              echo "<option  value='".$productResult['id']."' selected>" .$productResult['prod_name']. "</option>";
                           }
                           else
                           {
                               echo "<option  value='".$productResult['id']."'>" .$productResult['prod_name']. "</option>";
                           }

                          
                          
                        }
                      ?>                              
                    </select>
                  </div>

                      </div>

                      <div class="row">
                        <div class="form-group col-md-4">
                          <label for="shipping_ext_party_id">Select Shipping To</label>
                          <?php
                            $external_party_sql = "select * from external_party";
                            $ex_result = mysqli_query($conn, $external_party_sql);
                            
                          ?>                      
                           <select name="shipping_ext_party_id[]" data-live-search="true" class="form-control searchDropdown" title="Select" multiple="">
                            
                            <?php                   
                              foreach ($conn->query($external_party_sql) as $ex_result) 
                              {
                                if(isset($selectedArr['shipping_ext_party_id']) && in_array($ex_result['id'], $selectedArr['shipping_ext_party_id']))
                                 {

                                     echo "<option  value='".$ex_result['id']."' selected>".$ex_result['partyname']. "</option>";
                                 }
                                 else
                                 {
                                     echo "<option  value='".$ex_result['id']."'>".$ex_result['partyname']. "</option>";
                                 }
                                    
                              }
                            ?>                              
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="conf_type">Sales Confirmation Type</label>
                            <select name="conf_type[]" data-live-search="true" class="form-control searchDropdown" title="Select" multiple="">

                              <?php 
                                $statusArr=["Original","Revised","Cancel"];
                                foreach ($statusArr as $key => $item) 
                                {
                                    if(isset($selectedArr['conf_type']) && in_array($key, $selectedArr['conf_type']))
                                     {

                                        echo "<option  value='".$key."' selected>" .$item. "</option>";
                                     }
                                     else
                                     {
                                        echo "<option  value='" .$key. "'>" .$item. "</option>";
                                     }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                          <label for="trans_ins">Transit Insurance</label>
                                        
                          <select name="trans_ins[]" multiple="" data-live-search="true" class="form-control searchDropdown" title="Select">

                            <?php 
                                $traninsArr=["Us"=>"Transit Insurance By Us","Buyer"=>"Transit Insurance By Buyer"];
                                foreach ($traninsArr as $key => $item) 
                                {
                                    if(isset($selectedArr['trans_ins']) && in_array($key, $selectedArr['trans_ins']))
                                     {

                                        echo "<option  value='".$key."' selected>" .$item. "</option>";
                                     }
                                     else
                                     {
                                        echo "<option  value='" .$key. "'>" .$item. "</option>";
                                     }
                                }
                                ?>         
                            </select>
                        </div>
                      </div>
                      <div class="row">
                          <div class="form-group col-md-6">
                            <label for="start_date"> Select Start Conformation Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Report Start Date" value="<?php if(isset($selectedArr['start_date'])){echo $selectedArr['start_date'];} ?>" autocomplete="off">
                          </div>
                          <div class="form-group col-md-6">
                               <label for="end_date">Select End Conformation Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select Report End Date " value="<?php if(isset($selectedArr['end_date'])){echo $selectedArr['end_date'];} ?>" autocomplete="off">
                          </div>
                      
                        
                   </div>
                   
                  <div class="row">

                     <div class="form-group col-md-1">
                      <button type="submit" name="filter" class="btn btn-primary waves">Filter</button>
                    </div>
                    <div class="form-group col-md-1">
                      <button type="submit" name="clearFilter" class="btn btn-danger waves">Clear Filter</button>
                    </div>
                     
                   </div>
                 </form>


                    </div>
                </div>  

                <div class="card">
                    <div class="card-header">Sales Confirmation List</div>
                      <div class="card-body">
                        <form action="#" method="POST">
                     
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                              
                                <th>ID</th>
                                <th>Confirmation No.</th>
                                <th>External Party</th>
                                <th>Firm Name</th>
                                <th>Date</th>
                                <th>Bales</th>
                                <th>Status</th>
                                
                                <th class="text-center">Action</th>
                                </tr>
                        </thead>
                        <tfoot>
                          <tr>
                             
                                <th>ID</th>
                                <th>Confirmation No.</th>
                                <th>External Party</th>
                                <th>Firm Name</th>
                                <th>Date</th>
                                <th>Bales</th>
                                <th>Status</th>
                                
                                <th class="text-center">Action</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php

                          // $getDates=explode('/', $_SESSION["sales_conf_financial_year"]);
                          // $start_date=$getDates[0];
                          // $end_date=$getDates[1];
                          // $firm_id=$_SESSION["sales_conf_firm_id"];

                          //   $sql = "select s.*,p.party_name from seller_conf s, party p where s.firm=p.id AND s.sales_date>='".$start_date."' AND s.sales_date<='".$end_date."' AND s.firm='".$firm_id."'";

                          //   $result = mysqli_query($conn, $sql);
                            
                            if(mysqli_num_rows($result)){
                              while($row = mysqli_fetch_assoc($result)){
                          ?>
                          <tr>
                             
                            <td><?php echo $i+1 ?></td>

                            <td><?php echo $row['sales_conf']; ?></td>
                            
                            <td>
                              <?php 
                                  $party = "select * from external_party where id='".$row['external_party']."'";
                                  $ex_party='';
                                  $partyresult = mysqli_query($conn, $party);
                                  if(mysqli_num_rows($partyresult)>0)
                                  {
                                    $partyrow = mysqli_fetch_assoc($partyresult);
                                    $ex_party=$partyrow['partyname'];

                                  }
                                  echo $ex_party; 
                              ?>
                          </td>
                            
                            <td>
                              <?php 
                                $sql4 = "select * from party where id='".$row['firm']."'";
                                $result4 = mysqli_query($conn, $sql4);
                                $pname='';
                                if(mysqli_num_rows($result4)>0)
                                {
                                  $row10 = mysqli_fetch_assoc($result4);
                                  $pname=$row10['party_name'];
                                }                        
                                echo $pname; 
                              ?>                              
                            </td>

                           <td><?php echo date("d/m/Y", strtotime($row['sales_date'])); ?></td>

                           <?php
                           $sql2="SELECT SUM(no_of_bales) as used_bales FROM sales_conf_split WHERE conf_no='".$row['sales_conf']."'";
                                  $result2 = mysqli_query($conn, $sql2);
                                  $row2 = mysqli_fetch_assoc($result2);


                            ?>
                           <td><?php echo $row['cont_quantity'] ;


                           if($row2['used_bales']!=null)
                            {
                              echo "<br>(Used : ".$row2['used_bales'].')';
                            }?>

                         </td>
                          
                             <?php 
                            
                              if($row['conf_type'] == 0) $status_string = '<i class="btn btn-success">Original</i>';
                              if($row['conf_type'] == 1) $status_string = '<i class="btn btn-warning">Revised</i>';
                              if($row['conf_type'] == 2) $status_string = '<i class="btn btn-danger">Cancelled</i>';
                              
                           
                            ?>
                            <td><?php echo $status_string; ?></td>


                            <?php
  if(!isset($page))
  {
  $page=1;
  }
?>




                           
                              <td class="text-center">
                              <a href="show.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                              <a href="edit.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a>
                              <a href="index.php?delete=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this record?')"><i class="fa fa-trash-alt"></i></a>

                            <!--   <a target="_blank"  href="generate_pdf.php?id=<?php echo $row['id'] ?>" class="btn btn-primary"><i class="fa fa-file-pdf"></i></a> -->

                            <!-- PDF Genetate Button & Modal -->
                              <a href="#" id="pdf<?php echo $row['id'] ?>" class="btn btn-primary" data-toggle="modal" data-target="#pdf_modal<?php echo $row['id'] ?>"><i class="fa fa-file-pdf"></i></a>

                              <div class="modal" id="pdf_modal<?php echo $row['id'] ?>">
                                  <div class="modal-dialog">
                                    <div class="modal-content">

                                      <!-- Modal Header -->
                                      <div class="modal-header">
                                        <h4 class="modal-title">Sales Confirmation PDF</h4>
                                        <button style="font-size: 40px" type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>


                                       <div class="modal-body">

                                           Confirmation No. : <b><?php echo $row['sales_conf'] ?></b>
                                           <br>

                                           External Party : <b><?php echo $ex_party ?></b>

                                           <br>

                                          Firm : <b><?php echo $pname; ?></b>
  
 
                                        </div>
                                     
                                     

                                      <!-- Modal footer -->
                                      <div class="modal-footer">

                                       

                                        <?php
                                          $checkCWSQL="select * from pdf where table_indicator='1' AND record_id='".$row['id']."'";
                                          $checkCWResult=mysqli_query($conn,$checkCWSQL);
                                          if(mysqli_num_rows($checkCWResult)>0)
                                          {
                                          ?>

                                           <a class="btn btn-primary" href="pdf_list.php?record_id=<?php echo $row['id'] ?>&page=<?php echo $page ?>">View Old PDFs</a>

                                          <span class="text text-danger">Already Generated.</span>

                                          <?php
                                          }
                                          else
                                          {
                                          ?>
                                        <a class="btn btn-success" target="_blank"  href="generate_pdf.php?id=<?php echo $row['id'] ?>">Generate New PDF</a>
                                            
                                          <?php
                                          }
                                        ?>

                                       

                                      </div>

                                    </div>
                                  </div>
                                </div>

                      <!-- PDF Genetate Button & Modal END -->




                            </td>
                          </tr>
                          <?php
                              $i++;}
                            }
                          ?>
                        </tbody>
                      </table>
                    </form>
                    </div>
                     <?php if (!isset($_SESSION['sconf_filter_data'])) {

                       $query = "SELECT COUNT(*) FROM seller_conf WHERE firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."'";   
                          $rs_result = mysqli_query($conn, $query);     
                          $row = mysqli_fetch_row($rs_result);     
                          $total_records = $row[0]; 
                      ?>

                      
                      <div class="pagination-parent">
                      <div class="total-pg">
                        <?php 
                         echo "Total Records : ".$total_records;
                         ?>
                     </div>
                    
                  
                    <ul class="pagination">

                      <?php

                          
                           // Number of pages required.   
                          $total_pages = ceil($total_records / $per_page_record);     
                          $pagLink = ""; 


                          $totalPages  = $total_pages;
                          $currentPage = $page;

                          if ($totalPages <= 10) {
                              $start = 1;
                              $end   = $totalPages;
                          } else {
                              $start = max(1, ($currentPage - 4));
                              $end   = min($totalPages, ($currentPage + 5));

                              if ($start === 1) {
                                  $end = 10;
                              } elseif ($end === $totalPages) {
                                  $start = ($totalPages - 9);
                              }
                          }

                          if($page>=2)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page-1 ?>">Previous</a></li>
                          <?php 
                          }

                          for ($i = $start; $i <= $end; $i++) 
                          { 
                                if ($i == $page) 
                                { 
                                ?>
                                  <li class="page-item active"><a class="page-link" href="index.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                                <?php   
                                }               
                                else  
                                {  
                                ?>
                                   <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                                <?php    
                                 
                                }  
                          } 

                          if($page<$total_pages)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page+1 ?>">Next</a></li>
                          <?php 
                          }

                      ?>
                    </ul>

                    <div class="total-pages">Total Pages : <?php echo $total_pages; ?></div></div>

                <?php } ?>
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
      $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date('<?php echo($year_array[1]) ?>'),
        minDate: new Date('<?php echo($year_array[0]) ?>')
    });
         
      } );
    </script>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


  </body>
</html>

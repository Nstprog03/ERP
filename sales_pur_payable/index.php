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
  include('../db.php');
  $attend_dir = 'files/attend/';
  $salary_dir = 'files/salary/';
  $pf_dir = 'files/pf/';
  $epf_dir = 'files/epf/';

  if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "select * from sales_rcvble where id = ".$id;
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_assoc($result);
     
      $sql = "delete from sales_rcvble where id=".$id;
      if(mysqli_query($conn, $sql)){
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");
      }
    }
  }






if(isset($_POST['clearFilter']))
{
  unset ($_SESSION["slrcv_filter_data"]);
  unset ($_SESSION["slrcv_filter_selected"]);
  header("location:index.php");
}


  $isFilter=false;
  $selectedArr=array();
  if (isset($_POST['filter']) || isset($_SESSION['slrcv_filter_data'])) 
  {

    $isFilter=true;

    $main_query="select * from sales_rcvble where";
    $where_cond = array();

    if(isset($_POST['filter']))
    {
        // party_name
        if(isset($_POST['party']))
        {
          $ext_party="'".implode("','",$_POST['party'])."'";
          $where_cond[] = " pur_party in (".$ext_party.")";
        }

        // print_r($conf_type);exit();
          $start_date='';
          $end_date='';
          if($_POST['start_date']!='' && $_POST['end_date']=='')
          {
            $start_date = str_replace('/', '-', $_POST['start_date']);
            $start_date = date('Y-m-d', strtotime($start_date));
            $where_cond[] = " due_date>='".$start_date."'";
          }

          if($_POST['start_date']=='' && $_POST['end_date']!='')
          {
            $end_date = str_replace('/', '-', $_POST['end_date']);
            $end_date = date('Y-m-d', strtotime($end_date));
            $where_cond[] = " due_date<='".$end_date."'";
          }

         
          if($_POST['start_date']!='' && $_POST['end_date']!='')
          {

            $start_date = str_replace('/', '-', $_POST['start_date']);
            $start_date = date('Y-m-d', strtotime($start_date));

            $end_date = str_replace('/', '-', $_POST['end_date']);
            $end_date = date('Y-m-d', strtotime($end_date));

            $where_cond[] = " due_date>='".$start_date."' AND due_date<='".$end_date."'";

          }

          $selectedArr=$_POST;
          $_SESSION['slrcv_filter_selected']=$selectedArr;
          $_SESSION['slrcv_filter_data']=$where_cond;
    }
    else
    {
      $where_cond=$_SESSION['slrcv_filter_data'];
      $selectedArr=$_SESSION['slrcv_filter_selected'];
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
        $sql = "select * from sales_rcvble where firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' ORDER BY id DESC LIMIT $start_from, $per_page_record";     
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
     $sql = "select * from sales_rcvble where firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' ORDER BY id DESC LIMIT $start_from, $per_page_record";     
    $result = mysqli_query($conn, $sql);

  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Sales Recievable Database</title>
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

      $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date('<?php echo($year_array[1]) ?>'),
        minDate: new Date('<?php echo($year_array[0]) ?>')
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Sales Recievable Database</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>

              <ul class="navbar-nav ml-auto">


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
         

           $sqlLastChange="select username,update_at from sales_rcvble where
          financial_year_id='".$_SESSION['sales_financial_year_id']."' 
          AND firm='".$_SESSION['sales_conf_firm_id']."' order by update_at DESC LIMIT 1";

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
                
                      <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['update_at']))."</span> </span>
                 
              ";
           }
          ?>

        </div>
          </div>

          <!-- last change on table END-->

      <div class="container-fluid">
        <div class="row justify-content-center">
           <div class="card viewfilter">
              <div class="card-header">Filter</div>
                <div class="card-body">

                    <form action="" method="post" enctype="multipart/form-data">
                      <div class="row">
                        <div class="form-group col-md-4">
                          <label for="party[]"> Party Name</label>
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
                            <label for="start_date">Start Due Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder=" Start Dut Date" value="<?php if(isset($selectedArr['start_date'])){echo $selectedArr['start_date'];} ?>" autocomplete="off">
                         
                          </div>

                           <div class="form-group col-md-4">
                           
                               <label for="end_date">End Due Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder=" End Dut Date" value="<?php if(isset($selectedArr['end_date'])){echo $selectedArr['end_date'];} ?>" autocomplete="off">
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

                <div class="card mt-3">
                    <div class="card-header">Sales Recievable List</div>
                      <div class="card-body">
                          <form action="#" method="POST">
                     
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                
                                <th>ID</th>
                                <th>Party Name</th>
                                <th>Sale Report Party Name</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                
                                <th class="text-center">Action</th>
                                </tr>
                        </thead>
                        <tfoot>
                          <tr>
                                
                                <th>ID</th>
                                <th>Party Name</th>
                                <th>Sale Report Party Name</th>
                                <th>Invoice No</th>
                                <th>Due Date</th>
                                
                                <th class="text-center">Action</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php
                            // $sql = "select * from sales_rcvble where firm = '".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION["sales_financial_year_id"]."'";
                            // $result = mysqli_query($conn, $sql);
                      
                            if(mysqli_num_rows($result)){
                              while($row = mysqli_fetch_assoc($result)){
                          ?>
                          <tr>
                              
                            <td><?php echo $i+1 ?></td>
                            <!--<td><img src="<?php echo $upload_dir.$row['image'] ?>" height="40"></td>-->
                            <td>
                              <?php

                                $firm = 'select * from party where id="'.$row['firm'].'"';
                                $Firmresult = mysqli_query($conn, $firm);
                                if (mysqli_num_rows($Firmresult) > 0) {
                                  $Firmrow = mysqli_fetch_assoc($Firmresult);
                                }


                             echo $Firmrow['party_name'] ?></td>
                           <td><?php $party = "select * from external_party where id='".$row['pur_party']."'";
                          $partyresult = mysqli_query($conn, $party);

                          $partyrow = mysqli_fetch_assoc($partyresult);

                          $ex_party='';
                          if(isset($partyrow))
                          {
                            $ex_party=$partyrow['partyname'];
                          }
                          echo $ex_party;
                          ?>

                        </td>

                        <?php
                          if(!isset($page))
                          {
                          $page=1;
                          }
                        ?>

                           <td><?php echo $row['pur_invoice_no'] ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['due_date'])) ?></td>
                                                     <td class="text-center">
                              <a href="show.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                              <a href="edit.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a>
                              <a href="index.php?delete=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this record?')"><i class="fa fa-trash-alt"></i></a>
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

                   <?php if (!isset($_SESSION['slrcv_filter_data'])) {

                     $query = "SELECT COUNT(*) FROM sales_rcvble WHERE firm='".$_SESSION['sales_conf_firm_id']."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."'";   
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
                    
                  
                    <ul class="pagination" >

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

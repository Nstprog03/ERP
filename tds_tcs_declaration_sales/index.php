<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

$module = "";
if(isset($_GET['module']))
{
  if($_GET['module'] == "sales"){
    $module='sales';
  }elseif($_GET['module'] == "purchase_cotton"){
    $module='purchase_cotton';
  }
}

if($module == '')
{
  header('Location: ../index.php');
}

  $dir=$_SERVER['DOCUMENT_ROOT'].'/file_storage/'; 

  if(isset($_GET['delete']))
  {
		$id = $_GET['delete'];
		$sql = "select * from tds_tcs_declaration where id = ".$id;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);

        $getFiles = explode(',', $row['doc_file']); 
        foreach ($getFiles as  $item) 
        {
            if($item!='')
            {
              $item=trim($item);             
              unlink($dir.$item);
            }
        }

          $sqlPDF = "select * from pdf where table_indicator='10' AND record_id=".$id;
          $resultPDF=mysqli_query($conn, $sqlPDF);
          if(mysqli_num_rows($resultPDF)>0)
          {
            while ($rowPDF=mysqli_fetch_assoc($resultPDF)) 
            {

              if($rowPDF['file_name']!='')
              {
                $item=trim($rowPDF['file_name']);             
                unlink($dir.$item);
              }
                

               
            }

             $sqlPDF2 = "delete from pdf where table_indicator='10' AND record_id=".$id;
            $resultPDF2=mysqli_query($conn, $sqlPDF2);

             


          }

			$sql = "delete from tds_tcs_declaration where id=".$id;
			if(mysqli_query($conn, $sql)){
				$page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page&module=$module");
			}
		}
	}


  if(isset($_POST['btn_multi_del']))
  {

      if(isset($_POST['del_id']))
      {

        $idArr=$_POST['del_id'];

        foreach ($idArr as $key => $id) 
        {

          $sql = "select * from tds_tcs_declaration where id = ".$id;
          $result = mysqli_query($conn, $sql);
          if(mysqli_num_rows($result) > 0)
          {
            $row = mysqli_fetch_assoc($result);


              $getFiles = explode(',', $row['doc_file']); 
              foreach ($getFiles as  $item) 
              {
                  if($item!='')
                  {
                    $item=trim($item);             
                    unlink($dir.$item);
                  }
                     
              }

              
                $sqlPDF = "select * from pdf where table_indicator='10' AND record_id=".$id;
                $resultPDF=mysqli_query($conn, $sqlPDF);
                if(mysqli_num_rows($resultPDF)>0)
                {
                  while ($rowPDF=mysqli_fetch_assoc($resultPDF)) 
                  {

                    if($rowPDF['file_name']!='')
                    {
                      $item=trim($rowPDF['file_name']);             
                      unlink($dir.$item);
                    }
                      
                     
                  }

                   $sqlPDF2 = "delete from pdf where table_indicator='10' AND record_id=".$id;
                  $resultPDF2=mysqli_query($conn, $sqlPDF2); 

                }


          
            $sql = "delete from tds_tcs_declaration where id=".$id;
            mysqli_query($conn, $sql);
          }

        }

        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page&module=$module");
      }
      else
      {
        echo '<script>alert("Please Select Records...")</script>';
      }
  }

    if(isset($_POST['clearFilter'])){
      if(isset($_SESSION['pur_ext_party'])){
        unset($_SESSION['pur_ext_party']);
      }
    }

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
    $sql = "SELECT * FROM tds_tcs_declaration where module_indicator IN ('".$module."')";
    if($module){ 
        if(isset($_POST['filter'])){
            if(isset($_POST['party']) && $_POST['party'] > 0){
                $sql .= " AND FIND_IN_SET(ext_party_id,'".implode(",",$_POST['party'])."')";
                $_SESSION['pur_ext_party'] = $_POST['party'];
                $_SESSION['module'] = $module;
                $isFilter = true;
            }else{
                $_SESSION['pur_ext_party'] = array();
                $_SESSION['module'] = $module;
            }
        }elseif(isset($_SESSION['pur_ext_party']) && count($_SESSION['pur_ext_party']) > 0){
          if($_SESSION['module'] == $module){
            $sql .= " AND FIND_IN_SET(ext_party_id,'".implode(",",$_SESSION['pur_ext_party'])."')";
            $isFilter = true;
          }else{
            $_SESSION['pur_ext_party'] = array();
            $isFilter = false;
          }
            
        }else{
             $_SESSION['pur_ext_party'] = array();
        }
    } 
    $sql .= " ORDER by id DESC LIMIT $start_from, $per_page_record";
    $result = mysqli_query($conn, $sql);

  
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>TDS/TCS Declaration</title>
 
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

        <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
   <link rel="stylesheet" href="../../style4.css">
    <link rel="stylesheet" href="../../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
     <script> 
    $(function(){
      $("#sidebarnav").load("../../nav.html"); 
      $("#topnav").load("../nav2.html"); 
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
          <a class="navbar-brand" href="index.php?module=<?= $module ?>"><span class="page-name-top"><span class="icon-report_dashboard"></span> TDS/TCS Declaration</span></a>
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
                      var checkPageLoad="<?php if(isset($isFilter)){ echo $isFilter ;} ?>";

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

                <li class="nav-item"><a class="btn btn-primary" href="create.php?module=<?= $module ?>"><i class="fa fa-user-plus"></i></a></li>
              </ul>
          </div>
        </div>
      </nav>

        <!-- last change on table START-->
          



      <div class="container-fluid">
        <div class="row justify-content-center">
            
            <div class="card viewfilter">
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
                                  if(isset($_SESSION['pur_ext_party']) && in_array($ext_Partyresult['id'], $_SESSION['pur_ext_party']))
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
                    <div class="card-header">TDS/TCS Declaration</div>
                      <div class="card-body">
                         <form action="#" method="POST">
                      <div class="del-multi">
                          <button type="submit" name="btn_multi_del" class="btn btn-danger btn-sm btn_multi_del" >Delete All Selected</button>
                        </div>
                        <br>
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th><input type='checkbox' name='check_all' class="check_all"></th>
                                <th>ID</th>
                                <th>Party Name</th>
                                <th>Date</th>
                                <th>Goods Exceeding</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 250px;">Action</th>
                                </tr>
                        </thead>
                        <tfoot>
                          <tr>
                                 <th></th>
                                <th>ID</th>
                                <th>Party Name</th>
                                <th>Date</th>
                                <th>Goods Exceeding</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php
                           
                            
                    				if(mysqli_num_rows($result)){
                    					while($row = mysqli_fetch_assoc($result)){
                          ?>
                          <tr>
                            <td><input type='checkbox' name='del_id[]' value="<?php echo $row['id'] ?>"></td>
                            <td><?php echo $i+1 ?></td>
                           
                            <td><?php 

                        $sql4 = "select * from external_party where id='".$row['ext_party_id']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['partyname'];
                            }
                      echo $pname; ?></td>

                           <?php
                        $date='';
                        if($row['date']!='' && $row['date']!='0000-00-00')
                        {
                         $date = date("d/m/Y", strtotime($row['date']));
                        }
                      ?>



                           <td><?php echo $date ?></td>
                        



                           
                           <td><?php echo $row['good_exceeding']  ?></td>

                           <td><?php 
                           if($row['status']=='1')
                           {
                              echo 'Complete';
                           }
                           else
                           {
                              echo 'Pending';
                           }  
                           ?></td>


                           <?php
                              if(!isset($page))
                              {
                              $page=1;
                              }
                            ?>


                            <td class="text-center">
                              <a href="show.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>&module=<?= $module ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                              <a href="edit.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>&module=<?= $module ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a>
                              

                                <!-- PDF Genetate Button & Modal -->
                              <a href="#" id="pdf<?php echo $row['id'] ?>" class="btn btn-primary" data-toggle="modal" data-target="#pdf_modal<?php echo $row['id'] ?>"><i class="fa fa-file-pdf"></i></a>

                              <div class="modal" id="pdf_modal<?php echo $row['id'] ?>">
                                  <div class="modal-dialog">
                                    <div class="modal-content">

                                      <!-- Modal Header -->
                                      <div class="modal-header">
                                        <h4 class="modal-title">Purchase Confirmation PDF</h4>
                                        <button style="font-size: 40px" type="button" class="close" data-dismiss="modal">&times;</button>
                                      </div>
                                       <div class="modal-body">
                                           Party : <b><?php echo $pname ?></b>
                                        </div>
                                     
                                      <!-- Modal footer -->
                                      <div class="modal-footer">

                                        <?php
                                          if($module == "sales"){
                                            $table_indicator = 10;
                                           }else{
                                          $table_indicator = 11;
                                           }
                                          $checkCWSQL="SELECT * FROM pdf where table_indicator='".$table_indicator."' AND record_id='".$row['id']."'";
                                          $checkCWResult=mysqli_query($conn,$checkCWSQL);
                                          if(mysqli_num_rows($checkCWResult)>0)
                                          {
                                          ?>

                                           <a class="btn btn-primary" href="pdf_list.php?record_id=<?php echo $row['id'] ?>&page=<?php echo $page ?>&module=<?= $module ?>">View Old PDFs</a>

                                          <span class="text text-danger">Already Generated.</span>

                                          <?php
                                          }
                                          else
                                          {
                                          ?>                                       
                                            <a class="btn btn-success" target="_blank"  href="generate_pdf.php?id=<?php echo $row['id'] ?>&module=<?= $module ?>">Generate New PDF</a>

                                          <?php
                                          }
                                        ?>

                                        
                                      </div>

                                    </div>
                                  </div>
                                </div>

                      <!-- PDF Genetate Button & Modal END -->


                      <a href="generate_docx.php?id=<?php echo $row['id'] ?>&module=<?= $module ?>" class="btn btn-primary"><i class="fa fa-file-word"></i></a>

                      <a href="index.php?delete=<?php echo $row['id'] ?>&page=<?php echo $page ?>&module=<?= $module ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this record?')"><i class="fa fa-trash-alt"></i></a>

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

                    <?php if (!isset($_POST['filter'])) {

                      $query = "SELECT COUNT(*) FROM tds_tcs_declaration where module_indicator IN ('".$module."')";     
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
                            <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page-1 ?>&module=<?= $module ?>">Previous</a></li>
                          <?php 
                          }

                          for ($i = $start; $i <= $end; $i++) 
                          { 
                                if ($i == $page) 
                                { 
                                ?>
                                  <li class="page-item active"><a class="page-link" href="index.php?page=<?php echo $i ?>&module=<?= $module ?>"><?php echo $i ?></a></li>
                                <?php   
                                }               
                                else  
                                {  
                                ?>
                                   <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $i ?>&module=<?= $module ?>"><?php echo $i ?></a></li>
                                <?php    
                                 
                                }  
                          } 

                          if($page<$total_pages)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page+1 ?>&module=<?= $module ?>">Next</a></li>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.check_all').change(function() 
       {
          if(this.checked) 
          {
            $('input:checkbox').prop('checked', this.checked);    
          }
          else
          {
            $('input:checkbox').prop('checked', this.checked);    
          }
           
      });
      } );
    </script>
  </body>
</html>

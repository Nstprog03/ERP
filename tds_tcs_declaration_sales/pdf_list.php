<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}
  $dir = "/file_storage/"; // file storage in root folder of site
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  $module = "";
  if (isset($_GET["module"])) {
      if ($_GET["module"] == "sales") {
          $module = "sales";
          $table_indicator = 10;
      } elseif ($_GET["module"] == "purchase_cotton") {
          $module = "purchase_cotton";
          $table_indicator = 11;
      }
  }

  if(isset($_GET['delete_id']))
  {

    if ($_GET["module"] == "sales") {
      $module = "sales";
        $table_indicator = 10;
    } elseif ($_GET["module"] == "purchase_cotton") {
        $module = "purchase_cotton";
        $table_indicator = 11;
    }

		$delete_id= $_GET['delete_id'];
    $record_id= $_GET['record_id'];
   
    

		$sql = "select * from pdf where table_indicator='".$table_indicator."' AND id = ".$delete_id;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0)
    {
			$row = mysqli_fetch_assoc($result);
			
    $del_file =$row['file_name']; 
    
     if($del_file!='')
      {
        unlink($unlink_path.$del_file);      
      } 
    

			$sql = "delete from pdf where id=".$delete_id;
			if(mysqli_query($conn, $sql)){
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
				header("location:pdf_list.php?record_id=".$record_id."&page=$page&module=$module");
			}
    }
  }
    $sql = "SELECT * FROM pdf where table_indicator='".$table_indicator."' AND record_id='".$_GET['record_id']."' ORDER BY id DESC";     
    $result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>TDS/TCS Declaration PDF List</title>


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
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php?module=<?= $module ?>"><span class="page-name-top"><span class="icon-report_dashboard"></span> TDS/TCS Declaration PDF List</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto">

                 <li class="nav-item mr-2">
                      <?php if(mysqli_num_rows($result)>0)
                      {
                      ?>                        
                        <Button class="btn btn-success" disabled>Already Generated...</Button>
                      <?php
                      }
                      else
                      {
                      ?>                   
                         <a class="btn btn-success" target="_blank"  href="generate_pdf.php?id=<?php echo $_GET['record_id'] ?>&module=<?= $module ?>">Generate New PDF</a>
                      <?php
                      }
                      ?>       
                  </li>

                  <?php
                  $page=1;
                  if(isset($_GET['page']))
                  {
                    $page=$_GET['page'];
                  }
                  ?>

                <li class="nav-item"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>&module=<?= $module ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
              </ul>
          </div>
        </div>
      </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">
            
                <div class="card">
                    <div class="card-header">TDS/TCS Declaration PDF List</div>
                      <div class="card-body">
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>File Name</th>
                                <th>Generate Date & Time</th>
                               
                                <th class="text-center">Action</th>
                                </tr>
                        </thead>
                        <tfoot>
                          <tr>                              
                               <th>ID</th>
                                <th>File Name</th>
                                <th>Generate Date & Time</th>
                                <th class="text-center">Action</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php
                            $i=0;
                    				if(mysqli_num_rows($result)){
                    					while($row = mysqli_fetch_assoc($result)){
                          ?>
                          <tr>
                            <td><?php echo $i+1 ?></td>
                           
                           <td><?php 
                            $file_name=explode("/", $row['file_name']);
                            echo $file_name[2];
                            ?></td>
                           <td>
                            <?php 
                            $generate_time=$row['created_at'];
                            echo date('d/m/Y h:i A', strtotime($generate_time));
                            ?>                            
                           </td>
                         
                                <td class="text-center">
                             

                              <?php
                              $page=1;
                              if(isset($_GET['page']))
                              {
                                $page=$_GET['page'];
                              }
                             
                              ?>

                              <a target="_blank"  href="<?php echo $dir.$row['file_name'] ?>" class="btn btn-primary"><i class="fa fa-file-pdf"></i></a>

                              <a href="pdf_list.php?record_id=<?php echo $_GET['record_id'] ?>&delete_id=<?php echo $row['id'] ?>&page=<?php echo $page ?>&module=<?= $module ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this PDF?')"><i class="fa fa-trash-alt"></i></a>
                            </td>
                          </tr>
                          <?php
                              $i++;}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
  </body>
</html>

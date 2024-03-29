<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
 
  $logo_dir = 'files/logo/';
  $stamp_dir = 'files/stamp/';

  $dir=$_SERVER['DOCUMENT_ROOT'].'/static_file_storage/'; 


  if(isset($_GET['delete'])){
		$id = $_GET['delete'];
		$sql = "select * from party where id = ".$id;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0)
    {
			$row = mysqli_fetch_assoc($result);

        if($row['logo_img']!='')
        {
          unlink($logo_dir.$row['logo_img']);
        }

        if($row['stamp_img']!='')
        {
          unlink($stamp_dir.$row['stamp_img']);
        }
       

        $fileArr=explode(',', $row['docImg']);
        foreach ($fileArr as $key => $item) {
            if($item!='')
            {
              $item=trim($item);             
              unlink($dir.$item);
            }
        }



			$sql = "delete from party where id=".$id;
			if(mysqli_query($conn, $sql)){
				header('location:index.php');
			}
		}
	}



?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Firm List</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Firm Database</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>
              </ul>
          </div>
        </div>
      </nav>

      <!-- last change on table START-->
          <?php
          $sqlLastChange="select username,updated_at from party order by updated_at DESC LIMIT 1";

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
                <div class='last-updates'>
                      <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>
                 </div>
              ";
           }
          ?>

          <!-- last change on table END-->

      <div class="container-fluid">
        <div class="row justify-content-center">
            
                <div class="card">
                    <div class="card-header">Firm List</div>
                      <div class="card-body">
                         <form action="#" method="POST">
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>Address</th>
                                <th class="text-center"  style="width: 170px;">Action</th>
                                </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>ID</th>
                                <th>Firm Name</th>
                                <th>Address:</th>
                                <th class="text-center">Action</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php
                            $sql = "select * from party order by party_name ASC";
                            $result = mysqli_query($conn, $sql);
                            $i=0;
                    				if(mysqli_num_rows($result)){
                    					while($row = mysqli_fetch_assoc($result)){
                          ?>
                          <tr>
                            <td><?php echo $i+1 ?></td>
                            <td><?php echo $row['party_name'] ?></td>
                           <td><?php echo $row['party_address'] ?></td>
                                                     <td class="text-center">
                              <a href="show.php?id=<?php echo $row['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                              <a href="edit.php?id=<?php echo $row['id'] ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a>

                               <?php if($_SESSION['user_type']=='master') { ?>                                
                              <a href="index.php?delete=<?php echo $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this record?')"><i class="fa fa-trash-alt"></i></a>
                               <?php } ?> 
                            </td>
                          </tr>
                          <?php
                              $i++;}
                            }
                          ?>
                        </tbody>
                      </table>
                      <div class="pagination-parent">
                      <div class="total-pg">
                        <?php 
                         echo "Total Records : ".mysqli_num_rows($result);
                         ?>
                       </div>
                     </div>
                    </form>
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
           
            
        });
    </script>
  </body>
</html>

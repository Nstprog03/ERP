<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
  
 $dir=$_SERVER['DOCUMENT_ROOT'].'/static_file_storage/'; 

  if(isset($_GET['delete'])){
		$id = $_GET['delete'];
		$sql = "select * from stock_audit where id = ".$id;
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result) > 0){
			$row = mysqli_fetch_assoc($result);

		
      $aud1Files = explode(',', $row['audit1_doc_file']); 
      foreach ($aud1Files as  $item) 
      {     
          if($item!='')
          {
            $item=trim($item);             
            unlink($dir.$item);
          }   
      }

      $aud2Files = explode(',', $row['audit2_doc_file']); 
      foreach ($aud2Files as  $item) 
      {     
          if($item!='')
          {
            $item=trim($item);             
            unlink($dir.$item);
          }  
      }
			$sql = "delete from stock_audit where id=".$id;
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


  if(isset($_POST['btn_multi_del']))
  {

      if(isset($_POST['del_id']))
      {

        $idArr=$_POST['del_id'];

        foreach ($idArr as $key => $id) 
        {
            $sql="select * from stock_audit where id='".$id."'";
            $result=mysqli_query($conn, $sql);

            $rowGetRecord=mysqli_fetch_assoc($result);

           $aud1Files = explode(',', $rowGetRecord['audit1_doc_file']); 
            foreach ($aud1Files as  $item) 
            {     
                if($item!='')
                {
                  $item=trim($item);             
                  unlink($dir.$item);
                }  
            }

            $aud2Files = explode(',', $rowGetRecord['audit2_doc_file']); 
            foreach ($aud2Files as  $item) 
            {     
                if($item!='')
                {
                  $item=trim($item);             
                  unlink($dir.$item);
                }        
            }

          $sql = "delete from stock_audit where id=".$id;
          $result=mysqli_query($conn, $sql);
        }

        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");
          
      }
      else
      {
        echo '<script>alert("Please Select Records...")</script>';
      }
  }







if(isset($_POST['clearFilter']))
{
  unset ($_SESSION["sad_filter_data"]);
  unset ($_SESSION["sad_filter_selected"]);
  header("location:index.php");
}


  $isFilter=false;
  $selectedArr=array();
  if (isset($_POST['filter']) || isset($_SESSION['sad_filter_data'])) 
  {

     $isFilter=true;

    $main_query="select * from stock_audit where";
    $where_cond = array();

    if(isset($_POST['filter']))
    {
    
        // firm
        if(isset($_POST['firm']))
        {
          $firm=implode(",",$_POST['firm']);
          $where_cond[] = " firm in (".$firm.")";
        }

        
        // start_yr
        if(isset($_POST['start_yr']))
        {
          $start_yr="'".implode("','",$_POST['start_yr'])."'";
          $where_cond[] = " start_yr in (".$start_yr.")";

        }

        // end_yr
        if(isset($_POST['end_yr']))
        {
          $end_yr="'".implode("','",$_POST['end_yr'])."'";
          $where_cond[] = " end_yr in (".$end_yr.")";

        }
          $selectedArr=$_POST;
          $_SESSION['sad_filter_selected']=$selectedArr;
          $_SESSION['sad_filter_data']=$where_cond;
    }
    else
    {
      $where_cond=$_SESSION['sad_filter_data'];
      $selectedArr=$_SESSION['sad_filter_selected'];
    }

    $i=0;

    if(!empty($where_cond)){
     
      $where = implode('AND',$where_cond);
      $main_query = $main_query.$where;

       $result = mysqli_query($conn, $main_query);


    }
    else
    {
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
        $sql = "SELECT * FROM stock_audit ORDER by id DESC LIMIT $start_from, $per_page_record";     
        $result = mysqli_query($conn, $sql);
        }
    
  }
  else
  {

    
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
      $sql = "SELECT * FROM stock_audit ORDER by id DESC LIMIT $start_from, $per_page_record"; 
    $result = mysqli_query($conn, $sql);

  }



?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Stock Audit Reports</title>


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
      $("#topnav").load("../../nav2.html"); 
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Stock Audit Report Database</span></a>
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

                <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>
              </ul>
          </div>
        </div>
      </nav>

      <!-- last change on table START-->
          <?php
          $sqlLastChange="select username,updated_at from stock_audit order by updated_at DESC LIMIT 1";

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

          <div class="card viewfilter">
                <div class="card-header">Filter</div>
                  <div class="card-body">

                    <form action="" method="post" enctype="multipart/form-data">

                      <div class="row">
                        <div class="form-group col-sm-4">
                          <div class="form-group">
                            <label for="firm">Select Firm</label>
                                <?php
                                      $sqlFirm = "select * from party";
                                      $resultFirm = mysqli_query($conn, $sqlFirm);
                                      
                                    ?>                      
                                 <select name="firm[]" class="form-control" multiple="">
                                  <?php                   
                                    foreach ($conn->query($sqlFirm) as $resultFirm) 
                                    {
                                      if(isset($selectedArr['firm']) && in_array($resultFirm['id'], $selectedArr['firm']))
                                       {

                                          echo "<option  value='".$resultFirm['id']."' selected>" .$resultFirm['party_name']. "</option>";
                                       }
                                       else
                                       {
                                          echo "<option  value='" .$resultFirm['id']. "'>" .$resultFirm['party_name']. "</option>";
                                       }
                                    }
                                  ?>                              
                                  </select>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="start_yr">Select Start Year</label>
                              <select name="start_yr[]" class="form-control" id="selectElementId" multiple="">
                                <?php
                                    $yearRange=range(1950,2050);
                                    foreach ($yearRange as $item) 
                                    {

                                      if(isset($selectedArr['start_yr']) && in_array($item, $selectedArr['start_yr']))
                                       {

                                          echo "<option value='".$item."' selected>".$item."</option>";
                                       }
                                       else
                                       {
                                          echo "<option value='".$item."'>".$item."</option>";
                                       }                           
                                    }
                                  ?>
                              </select>
                          </div>
                        </div>

                        <div class="form-group col-sm-4">
                          <div class="form-group">
                               <label for="end_yr">Select End Year</label>
                                <select name="end_yr[]" class="form-control" id="endYear" multiple="">
                                  <?php
                                    $yearRange=range(1950,2050);
                                    foreach ($yearRange as $item) 
                                    {

                                      if(isset($selectedArr['end_yr']) && in_array($item, $selectedArr['end_yr']))
                                       {

                                          echo "<option value='".$item."' selected>".$item."</option>";
                                       }
                                       else
                                       {
                                          echo "<option value='".$item."'>".$item."</option>";
                                       }                           
                                    }
                                  ?>
                                </select>
                          </div>
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
                    <div class="card-header">Stock Audit Report</div>
                      <div class="card-body">
                         <form action="#" method="POST">
                     <div class="del-multi">
                          <button type="submit" name="btn_multi_del" class="btn btn-danger btn-sm btn_multi_del" >Delete All Selected</button>
                        </div>
                        <br>
                      <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th><input type='checkbox' name='check_all' class="check_all"></th>
                                <th>ID</th>
                                <th>Party Name</th>
                                <th>Report Year</th>                                
                                <th class="text-center" style="width: 170px;">Action</th>
                                </tr>
                        </thead>
                        <tfoot>
                          <tr>
                             <th></th>
                            <th>ID</th>
                                <th>Party Name</th>
                                <th>Report Year</th>
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

                              <?php
                              $sql2 = "select * from party where id='".$row['firm']."'";
                              $result2 = mysqli_query($conn, $sql2);
                              $row2=mysqli_fetch_array($result2);
                              ?> 

                            <td><?php echo $row2['party_name'] ?></td>

                            <td><?php echo $row['start_yr'] ?> - <?php echo $row['end_yr'] ?></td>

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
                            </td>
                          </tr>
                          <?php
                              $i++;}
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>

                    <?php if (!isset($_SESSION['sad_filter_data'])) {
                      $query = "SELECT COUNT(*) FROM stock_audit";     
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });

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

        });




    </script>
  </body>
</html>
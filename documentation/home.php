<?php
session_start();
require_once('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

if(!isset($_SESSION['doc_firm_id']) || !isset($_SESSION['doc_seasonal_year_id']))
{
  header('Location: index.php');
}
$dir=$_SERVER['DOCUMENT_ROOT'].'/file_storage/'; 

  if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "select * from documentation where id = ".$id;
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){

        $row1=mysqli_fetch_assoc($result);
       $OldDBImg = explode(',', $row1['doc_file']); 
        foreach ($OldDBImg as  $item) {
            if($item!='')
            {
              $item=trim($item);             
              unlink($dir.$item);
            }    
        }



      
      $sql = "delete from documentation where id=".$id;
      if(mysqli_query($conn, $sql)){
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: home.php?page=$page");
      }
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

    $start_from = ($page-1) * $per_page_record;  
 $sql = "SELECT * FROM documentation where seasonal_year_id='".$_SESSION['doc_seasonal_year_id']."' AND firm_id='".$_SESSION["doc_firm_id"]."' ORDER by id DESC LIMIT $start_from, $per_page_record";     
    $result = mysqli_query($conn, $sql);

     //id auto increment
    $i=0;
    $i=($page*10)-10;
  
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Documentation</title>
  
  
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
          <a class="navbar-brand" href="home.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Documentation</span></a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto">
                 <li class="nav-item mr-2"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>

                <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>
              </ul>
          </div>
        </div>
      </nav>

       <!-- last change on table START-->
       <div class="last-updates">
                  <div class="firm-selectio">
             <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["doc_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Seasonal Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["doc_seasonal_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
           $sqlLastChange="select username,updated_at from documentation where firm_id='".$_SESSION['doc_firm_id']."' AND seasonal_year_id='".$_SESSION['doc_seasonal_year_id']."'  order by updated_at DESC LIMIT 1";

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
           
                <div class="card">
                    <div class="card-header">Documentation</div>
                      <div class="card-body">
                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                              <th>ID</th>
                           
                              <th>Firm</th>
                              <th>Seasonal Year</th>                                                                
                              <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                          <tr>
                               <th>ID</th>
                           
                              <th>Firm</th>
                              <th>Seasonal Year</th>                                                 
                              <th class="text-center">Action</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          <?php
                           
                          
                            if(mysqli_num_rows($result)){
                              while($row = mysqli_fetch_assoc($result)){
                          ?>
                          <tr>
                           <td><?php echo $i+1 ?></td>
                            <td><?php 

                        $sql4 = "select * from party where id='".$row['firm_id']."'";
                            $result4 = mysqli_query($conn, $sql4);
                             $pname='';
                            
                            
                            if(mysqli_num_rows($result4))
                            {
                              $row10 = mysqli_fetch_assoc($result4);
                               $pname=$row10['party_name'];
                            }
                              
                             echo $pname; ?>
                        
                      </td>
                          
                           <td><?php 



                                $seasonal_yearSQL = "select * from seasonal_year where id='".$row['seasonal_year_id']."'";
                                $seasonal_year_result = mysqli_query($conn, $seasonal_yearSQL);

                                 $start_yr='';
                                $end_yr='';
                                if(mysqli_num_rows($seasonal_year_result)>0)
                                {
                                      $seasonal_year_row = mysqli_fetch_assoc($seasonal_year_result);

                                 
                                  if(isset($seasonal_year_row))
                                  {
                                    $start_yr =  date("Y", strtotime($seasonal_year_row['startdate']));
                                    $end_yr =  date("Y", strtotime($seasonal_year_row['enddate']));
                                  }
                                }

                              
                                echo $start_yr.'-'.$end_yr;
                                ?>
                            </td>

                            <?php
                            if(!isset($page))
                            {
                            $page=1;
                            }
                            ?>
                            
 

                            <td class="text-center">
                              <a href="show.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                              <a href="edit.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a>
                              <a href="home.php?delete=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this record?')"><i class="fa fa-trash-alt"></i></a>
                            </td>
                          </tr>
                          <?php
                              $i++;}
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>

                    <?php
                     $query = "SELECT COUNT(*) FROM documentation where seasonal_year_id='".$_SESSION['doc_seasonal_year_id']."' AND firm_id='".$_SESSION["doc_firm_id"]."'";  

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
                            <li class="page-item"><a class="page-link" href="home.php?page=<?php echo $page-1 ?>">Previous</a></li>
                          <?php 
                          }

                          for ($i = $start; $i <= $end; $i++) 
                          { 
                                if ($i == $page) 
                                { 
                                ?>
                                  <li class="page-item active"><a class="page-link" href="home.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                                <?php   
                                }               
                                else  
                                {  
                                ?>
                                   <li class="page-item"><a class="page-link" href="home.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                                <?php    
                                 
                                }  
                          } 

                          if($page<$total_pages)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link" href="home.php?page=<?php echo $page+1 ?>">Next</a></li>
                          <?php 
                          }

                      ?>
                    </ul>
                    <div class="total-pages">Total Pages : <?php echo $total_pages; ?></div></div>
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
        $('#example').DataTable();
      } );
    </script>
  </body>
</html>

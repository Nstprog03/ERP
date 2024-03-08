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




if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  

  if(isset($_POST['submit']))
  {


      $main_query="select * from kapasiya where";

      $where_cond = array();


     

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

      if(isset($_POST['seasonal_year']))
      {
        $seasonal_year="'".implode("','",$_POST['seasonal_year'])."'";
        $where_cond[] = " seasonal_year in (".$seasonal_year.")";
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
            $total_amount=0;
            $truckArr=json_decode($row['truck'],true);
            foreach ($truckArr as $key => $item) 
            {
              $total_amount+=(float)$item['final_amt'];
            }

            $mainArr[$i]['firm'] = getFirmDetails($row['firm']);
            $mainArr[$i]['ext_party'] = getExternalPartyDetails($row['party']);
            $mainArr[$i]['no_of_truck'] = $row['no_of_truck'];
            $mainArr[$i]['conf_date'] = convertDate($row['conf_date']);
            $mainArr[$i]['total_amount'] =$total_amount;

         
            $i++;
          } 


        //show 50lakh ane above records only
        if(isset($_POST['above50lakh']))
        {

          foreach ($mainArr as $key => $item) 
          {
             if($item['total_amount']<5000000)
             {
                unset($mainArr[$key]);
             }
          }

          //reset array index
          $mainArr=array_values($mainArr);

        }



  }




?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Kapasiya Annual Sales</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Kapasiya Annual Sales</span></a>
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





                         <div class="col-md-4">
                              <div class="form-group">
                        <label for="financial_year">Select Sesonal Year</label>
                         <?php 
                                $seasonalYear = getSeasonalYear($conn);
                            ?> 
                        <select name="seasonal_year[]" class="form-control">
                          <option value="" selected="" disabled="">Select Option</option>
                        <?php                   
                            foreach ($seasonalYear as $result2) 
                            {
                                //get Start Year And End Year
                                $syear = date("Y", strtotime($result2['startdate']));
                                $eyear = date("Y", strtotime($result2['enddate']));
                                //current financial year selected
                                $curDate=date('Y-m-d');
                                $startdate=date('Y-m-d', strtotime($result2['startdate']));
                                $enddate=date('Y-m-d', strtotime($result2['enddate']));


                                if(isset($_POST['seasonal_year']) && in_array($result2['id'], $_POST['seasonal_year']))
                               {

                                echo "<option  value='".$result2['id']."' selected>" .$syear."-".$eyear."</option>";
                                    
                               }
                               else
                               {
                                  echo "<option  value='".$result2['id']."'>" .$syear."-".$eyear."</option>";
                               }
                                
                                
                                
                             }
                        ?>                              
                        </select>
                    </div>
                        </div>



                     <div class="col-md-4">
                       <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="above50lakh" name="above50lakh" value="1" <?php if(isset($_POST['above50lakh'])){echo 'checked';} ?>>
                        <label class="form-check-label" for="above50lakh">Show Only 50 Lakh & Above</label>
                      </div>
                      </div>
                   
                            


                      </div>
                      <div class="row mt-3">
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

                     
                      <table id="example" class="registertable table table-striped table-bordered" style="width:100%">
                        <thead>
                              <tr>
                               <th>ID</th>
                              <th>Firm Name</th>
                              <th>External Party</th>
                              <th>No. Of Truck</th>
                              <th>Confirmation Date</th>
                              <th>Total Amount</th>     
                              </tr>
                        </thead>
                       
                        <tbody>
                          <?php 

                          $final_total=0;
                          if (isset($_POST['submit'])) {

                            if (count($mainArr)>0) {

                            
                            
                            $i=0;
                            
                            foreach ($mainArr as $key => $row) {
                                 
                            $final_total+=(float)$row['total_amount'];
                              
                            ?>
                          

                          <tr>
                            <td><?php echo $i = $i+1 ?></td>
                            <td><?php echo $row['firm'] ?></td>
                            <td><?php echo $row['ext_party'] ?></td>
                            <td><?php echo $row['no_of_truck'] ?></td>
                            <td><?php echo $row['conf_date'] ?></td>
                            <td><?php echo $row['total_amount'] ?></td>

                          </tr>

                          <?php }
                           }

                        }


                          ?>

                          
                          
                          
                        </tbody>

                         <tfoot>
                          <tr>
                              <th colspan="4"></th>                              
                              <th>Total Amount</th> 
                              <th><?php echo $final_total; ?></th>                             
                           </tr>
                         </tfoot>


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

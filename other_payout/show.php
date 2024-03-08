<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

$dir = "/file_storage/"; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from other_payout where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Other Payout Show</title>

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



     <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">

      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

     <script> 
    $(function(){
      $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html");

      $(".datepicker").datepicker({
            
              dateFormat: "dd/mm/yy",
               changeMonth: true,
                changeYear: true,
             
          });
    


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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Other Payout Show</span></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>


            <?php
          $page=1;
          if(isset($_GET['page']))
          {
            $page=$_GET['page'];
          }
          ?>

          


            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>

      <!-- last change on table START-->
       <div class="last-updates">
         <?php
          $sqlLastChange="select username,updated_at from other_payout where id='".$row['id']."'";

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

          <!-- last change on table END-->       

      <div class="container-fluid">
        <div class="row justify-content-center">
         
            <div class="card">
              <div class="card-header">Other Payout Show</div>
              <div class="card-body">
              
                  <div class="row">

                     <div class="form-group col-md-4">
                        <label for="firm_id">Select Firm</label>
                            <?php
                                $sql = "select * from party";
                                $result = mysqli_query($conn, $sql);
                            ?>                      
                        <select name="firm_id" class="form-control" disabled="">
                        <?php                   
                            foreach ($conn->query($sql) as $result) 
                            {
                              if($row['firm_id']==$result['id'])
                              {
                                  echo "<option  value='".$result['id']."' selected>".$result['party_name']. "</option>";
                              }  
                            }
                        ?>                              
                        </select>
                    </div>

                    <?php
                      if($row['pay_to']=='e')
                      {
                    ?>


            
                    <div class="form-group col-md-4">
                      <label for="ext_party_id">External Party</label>
                      <?php
                            $sql = "select * from external_party";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="ext_party_id" class="form-control" disabled>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                if($row['ext_party_id']==$result['id'])
                                {
                                    echo "<option  value='" .$result['id']."' selected>" .$result['partyname']. "</option>";
                                }
                                
                              }
                            ?>                              
                            </select>
                    </div>


                  <?php } else if($row['pay_to']=='b') { ?>

                     <div class="form-group col-md-4">
                      <label for="broker_id">Broker</label>                     
                      <?php
                            $sql = "select * from broker";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>                      
                           <select name="broker_id" class="form-control" disabled="">
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    if($row['broker_id']==$result['id'])
                                    {
                                        echo "<option  value='" .$result['id']."' selected>" .$result['name']. "</option>";
                                    }
                                   
                                    
                              }
                            ?>                              
                            </select>
                    </div>

                      <?php }  ?>

                    <?php
                     
                        $date='';
                        if($row['date']!='' && $row['date']!='0000-00-00')
                          {
                            $date = str_replace('-', '/', $row['date']);
                            $date = date('d/m/Y', strtotime($date));
                          }

                      ?>

                  <div class="form-group col-md-4">
                      <label for="date">Date</label>
                      <input type="text" class="form-control datepicker" name="date" value="<?php echo $date ?>" disabled>
                  </div>


                    <div class="form-group col-md-4">
                      <label for="invoice_no">Invoice No</label>
                      <input type="text" class="form-control" name="invoice_no" value="<?php echo $row['invoice_no'] ?>"  disabled>
                   </div>

                   <div class="form-group col-md-4">
                      <label for="amount">Amount</label>
                      <input type="text" class="form-control" name="amount"  onkeypress="return NumericValidate(event,this)" value="<?php echo $row['amount'] ?>" disabled>
                   </div>


                   <div class="form-group col-md-4">
                      <label for="remark">Remark</label>
                      <textarea class="form-control" name="remark" rows="4" disabled><?php echo $row['remark'] ?></textarea>
                   </div>
                  
                    </div>


                          <div class="row">

<?php
  if($row['doc_file'] != ''){
   $prev = explode(',',$row['doc_file']);
  $prev_img_title = explode(',',$row['img_title']);
  foreach ($prev as $key => $imging) {
    $attend =  $dir.$imging;

    if($attend)
        {
        $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));

        $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');


        if(in_array($attendExt, $attend_allowExt)) 
        {

          ?>
                    <div class="form-group col-md-4 field-show-image">
                      <div class="image-upload">  
                        <div class="label">
                        <h6 class="title">Document File <?= $key+1 ?></h6>
                      
                      </div>
                      <div class="filed-form-control">  
                      <img src="<?php echo $dir.$imging ?>"  data-toggle="modal" data-target="#myModal" id="1" onerror="this.onerror=null; this.src='../../image/no-image.jpg'" height="300" width="300">
                        </div>
                      <div class="text-center mt-3"> (Click Image to Open)</div>

           
                      </div>
                      <br>
<input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                    </div>

          <?php
         
        }
        else
        {
          ?>
           <div class="form-group col-md-4 field-show-image pl-0">
                      <div class="image-upload">  
                        <div class="label">
                        <h6 class="title">Document File <?= $key+1 ?></h6>
                      
                      </div>
                      <div class="filed-form-control">  
             <img src="<?php echo $dir.$imging ?>"   id="1" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" height="300" width="300">
          <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

           
                      </div>
                      <br>
                      <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                    </div>
                  </div>
          <?php
        }

      }
    }
    }

?>                        
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img class="img-responsive" src="" width="600" height="600" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>






                
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
        $(document).ready(function () {
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
});

function NumericValidate(evt, element) {

     var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
    return false;
  else {
    var len = $(element).val().length;
    var index = $(element).val().indexOf('.');
    if (index > 0 && charCode == 46) {
      return false;
    }
    if (index > 0) {
      var CharAfterdot = (len + 1) - index;
      if (CharAfterdot > 3) {
        return false;
      }
    }

  }
  return true;       
}


    </script>
<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
  $dir = "/static_file_storage/"; 
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from bank_stock where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
    }
  }

  if(isset($_POST['Submit'])){
    $firm = $_POST['firm'];
    $mnt = $_POST['mnt'];
    $year = $_POST['year'];
    
    $Drawing_limit = $_POST['Drawing_limit'];
    $book_debt = $_POST['book_debt'];
    
    $certified_book = $_POST['certified_book'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    include_once('../../global_function.php'); 
    $data=getStaticFileStoragePath("bank-stock-stmt");  //from global_function.php
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path
    
    $imgArr=array();
    $filecount = count($_FILES['docimg']['tmp_name']);  
    foreach ($_FILES['docimg']['tmp_name'] as $key =>  $imges) {
      $img = $_FILES['docimg']['name'][$key];
      $imgTmp = $_FILES['docimg']['tmp_name'][$key];
      $imgSize = $_FILES['docimg']['size'][$key];

  
      if(!empty($img)){
        
        $imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        $allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $img = time().'_'.rand(1000,9999).'.'.$imgExt;
        // array_push($imgArr,$img);
        $imgArr[$key] = $img;
        if(in_array($imgExt, $allowExt)){

          if($imgSize < 5000000){
            move_uploaded_file($imgTmp ,$root_path.$img);
          }else{
            $errorMsg = 'Image too large';
            echo $errorMsg;
          }
        }else{
          $errorMsg = 'Please select a valid image';
          echo $errorMsg;
        }

      }else{
        $imgArr[$key] = '';
      }
    }
    
    $finalimg = array();
    if(count($imgArr) > 0){
      foreach($imgArr as $k => $v){
        if($v == "" && isset($_POST['oldfile'][$k])){
          $finalimg[] = $_POST['oldfile'][$k];
        }else{
          if($v!='' && $v!=null)
          {
            $finalimg[] = $store_path.$v;
          }
        }
      }
    }


    $img_title = $_POST['img_title'];
    $imgTitle = implode(',', $img_title);
    $imgStore = implode(',', $finalimg);
    
    $OldDBImg = explode(',', $row['docimg']); 
    $result1=array_diff($OldDBImg,$finalimg);
    foreach ($result1 as  $item) {
        if($item!='')
        {
          $item=trim($item);             
          unlink($unlink_path.$item); 
        }
    }
   
   
  
  
    if(!isset($errorMsg)){
      $sql = "update bank_stock
                  set firm = '".$firm."',
                    year = '".$year."',
                    mnt = '".$mnt."',
                    Drawing_limit = '".$Drawing_limit."',
                    book_debt = '".$book_debt."',
                    certified_book = '".$certified_book."',
                    docimg = '".$imgStore."',
                    img_title = '".$imgTitle."',
                    username = '".$username."',
                    updated_at = '".$timestamp."'


          where id=".$id;
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record updated successfully';
        $page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
      }
    }

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bank Stock Statement Edit</title>
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Bank Stock Statement List</span></a>
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
                              <li class="nav-item mr-2"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i><span class="pl-1">Back</span></a></li>
                              <li class="nav-item mr-2"><a href="show.php?id=<?php echo $row['id'] ?>" class="btn btn-success"><i class="fa fa-eye"></i><span class="pl-1">Show</span></a></li>
                              <li class="nav-item"><a class="btn btn-outline-primary" href="create.php"><i class="fa fa-user-plus"></i><span class="pl-1">Add Record</span></a></li>
                              
                            </ul>


          </div>
      </div>
    </nav>

      <!-- last change on Record START-->
          <?php
          $sqlLastChange="select username,updated_at from bank_stock where id='".$row['id']."'";

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

          <!-- last change on record END-->

      <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="card">
              <div class="card-header">
                Update Bank Stock Statement List
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">

                   <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<div class="row">
               <div class="form-group col-sm-4">
                <label for="firm">Firm</label>
                    <?php
                        $sql2 = "select * from party";
                        $result2 = mysqli_query($conn, $sql2);
                    ?>                      
                <select id="firm" name="firm" class="form-control">
                <?php                   
                    foreach ($conn->query($sql2) as $result2) 
                    {

                       $isFirmSelected =""; 
                       if($row['firm']==$result2['id'])
                       {
                         $isFirmSelected = "selected";
                       }

                        echo "<option  value='".$result2['id']."'".$isFirmSelected.">".$result2['party_name']. "</option>";
                    }
                ?>                             
                </select>
            </div>


                    <div class="form-group col-sm-4">
                      <label for="mnt">Select Month</label>
                      <?php
                            $sql = "select * from bank_stock";
                            $result = mysqli_query($conn, $sql);
                            
                          ?>
                      
                           <select name="mnt" class="form-control" >

                            <?php
                            $monthArr=['January','February','March','April','May','June','July','August','September','October','November','December'];
                            
                              foreach ($monthArr as $key => $value) {

                                if($row['mnt']==$value)
                                {
                                  echo "<option value='".$value."' selected>$value</option>";
                                }
                                else
                                {
                                  echo "<option value='".$value."'>$value</option>";
                                }

                                
                                
                              }


                            ?>

    
                        </select>
                    </div>

                   
                    
                    <div class="form-group col-sm-4">
                      <label for="party">Select Year</label>
                     <select name="year" class="form-control" id="selectElementId">
                                           
                           
                     </select>
                   </div>

                       
                  </div>

                  <div class="row">
                    <div class="form-group col-sm-6">
                      <label for="Drawing_limit">Drawing Power Limit</label>
                      <input type="text" name="Drawing_limit"class="form-control" value="<?php echo $row['Drawing_limit']; ?>" onkeypress="return NumericValidate(event,this)">
                    </div>

                    <div class="form-group col-sm-6">
                      <label for="book_debt">Book Debit</label>
                      <select class="form-control" id="selectbox" name="book_debt">
                         

                        <option <?php if($row['book_debt'] == 'yes')
                          echo "selected";

                          ?> value="yes" >Yes</option>

                        <option <?php if($row['book_debt'] == 'no')
                          echo "selected";

                          ?> value="no" >No</option>

                      </select><br>
                      <div id="ifYes">

                          
                          <label for="CA certified book debt "><b>CA certified </b> <input type="checkbox" name="certified_book" id="checkbox" value="1"
                            <?php echo $row['certified_book']==1?'checked':'' ?>

                            > </label>
                      </div>
                   </div>
                  </div>

                  <div class="row dynamicWrapper">

<?php

if ($row['docimg'] != '') {


$prev = explode(',',$row['docimg']);
$prev_img_title = explode(',',$row['img_title']);
foreach ($prev as $key => $imging){
if($imging)
$attend =  $dir.$imging;
{
$attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));
$attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');

if(in_array($attendExt, $attend_allowExt)) 
{ ?>

    
      <div class=" form-group  col-sm-4 imgcount dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="docimg">Document File <?= $key+1 ?></label>
          <div class="image-upload dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
            <?php } ?>
            <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$prev[$key] ?>" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />

            <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $prev[$key]; ?>">

            <input type="file" class="form-control" id="img<?= $key+1 ?>" onchange="readURL(this);" name="docimg[<?= $key?>]" value="">
            
            <br>

            <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>
      <?php
   

}else{
  ?>

  <div class=" form-group  col-sm-4 imgcount dynamic_field_<?= $key+1 ?>">
        <label class="image-label" for="docimg">Document File <?= $key+1 ?></label>
          <div class="image-upload dynamic_field">
            <?php if( $key != 0) {?>
              <button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this);">X</button>
            <?php } ?>
            <img id="preview-img<?= $key+1 ?>" src="<?php echo $dir.$imging ?>"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" class="img-fluid" height="250" width="300">
            <input type="hidden" name="oldfile[<?= $key?>]" value="<?php echo $imging; ?>">
            <div class="filed-form-control">  
                                 
              <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

                               
            </div>

            <input type="file" class="form-control" id="img<?= $key+1?>" onchange="readURL(this);" name="docimg[<?= $key?>]" value="">
            <br>
            <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>">
          </div>



      </div>

  <?php
}
}
}
}else{?>

<div class=" form-group  col-sm-4 imgcount dynamic_field_1">
    <label class="image-label" for="docimg">Document File 1</label>
      <div class="image-upload dynamic_field">
      
        <img id="preview-img1" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" />
        <input type="file" class="form-control" id="img1" onchange="readURL(this);" name="docimg[1]" value="">
        <br>
        <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]">
      </div>



  </div>


<?php }?>

  <div class="form-group form-group col-sm-4">
    <label class="image-label" for="docimg">Add more</label>
     <div class="image-upload">
      
     <button type="button" class=" btn btn-defult" id="add" style="height: 340px;width: 263px;"><i class="fa fa-plus" aria-hidden="true" style="width: 35%;height: 117px;"></i>
      </button>
    

  </div>

  </div>
  </div>


                    <div class="form-group" style="margin-left: 14px;">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
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
      var delCount=0;
        $(document).ready(function () {
            SelectBox();
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });

            $('#selectbox').on('change', function() {
             
                 SelectBox(); 
            });

            var i = 0;
            $("#add").click(function(){
              var classcount = $('.imgcount').length
              i=parseInt(classcount)+parseInt(delCount)+1;
              // alert(i);
              var varietyfieldHTML= `<div class=" img_section form-group  col-sm-4 pl-0 imgcount dynamic_field_`+i+`"><label class="image-label" for="docimg">Document File `+i+`</label><div class="image-upload dynamic_field"><button type="button" class="btn btn-danger" style="position: absolute;margin-left: 218px;" onclick="removeImg(this,`+i+`);">X</button><img id="preview-img`+i+`" src="#" alt="your image"  class="img-fluid" onerror="this.onerror=null; this.src='../../image/prev-image.jpg'" height="300" width="300" /><input type="file" class="form-control" id="img`+i+`" onchange="readURL(this,`+i+`);" name="docimg[]" value=""><br><input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]"></div></div>`;

           
            $('.imgcount').last().after(varietyfieldHTML);


            });

        });

        function removeImg(e,index) {
         $(e).parent('div').parent('div').remove();
         delCount=delCount+1; 
        }

        function SelectBox()
        {
           var val=$('#selectbox').val();

           if(val==='yes')
           {
              $('#ifYes').show();
           }
           else if(val==='no')
           {
              $('#ifYes').hide();              
              $('#checkbox').prop('checked', false);
           }

        }





var min = 1950,
    max = min + 100,
    select = document.getElementById('selectElementId');

var selectedYear="<?php echo $row['year']; ?>";


for (var i = min; i<=max; i++){

  if(i==selectedYear)
  {
      var opt = document.createElement('option');
      opt.value = i;
      opt.innerHTML = i;
      opt.selected=true;
      select.appendChild(opt);
  }
  else
  {
       var opt = document.createElement('option');
        opt.value = i;
        opt.innerHTML = i;
        select.appendChild(opt);  
  }
   
}


 function readURL(input) {
            var url = input.value;
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

            $(input).parent().find('span.error-keyup-110').hide();
            if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg")) 
            {

                var reader = new FileReader();

                const fsize = input.files[0].size;
                const file_size = Math.round((fsize / 1024));


               

                if(file_size>1150) //1.1 MB
                {
                  $(input).after('<span class="error error-keyup-110 text-danger">Image Size Should Be 1 MB or Lesser...</span>');
                  $(input).val(''); 

                   imgId = '#preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');

                }
                else
                {
                    reader.onload = function (e) {
                        imgId = '#preview-'+$(input).attr('id');
                        $(imgId).attr('src', e.target.result);
                    }

                     reader.readAsDataURL(input.files[0]);
                }
                

            }
            else
            {
                  imgId = '#preview-'+$(input).attr('id');
                  $(imgId).attr('src', '../../image/no-prev.jpg');
                  //$(imgId).find(".msg").html("This is not Image");
                 //$('.imagepreview').attr('src', '/assets/no_preview.png');
            }
}


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

       
  </body>
</html>
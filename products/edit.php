<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from products where id=".$id;
    $sql2 = "select * from product_sub_items where product_id=".$id;

     

    $result = mysqli_query($conn, $sql);
    $result2 = mysqli_query($conn, $sql2);
    

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
    }else {
      $errorMsg = 'Could not Find Any Record';
      echo $errorMsg;
    }

    $row2=array();

    $oldIdArr=array();
  

    if(mysqli_num_rows($result2) > 0)
    {
      while($record=mysqli_fetch_assoc($result2))
      {
          $row2[]=$record;
          $oldIdArr[]=$record['id'];



          
      }

    }

   

   


  }

  if(isset($_POST['Submit'])){
		$prod_name = $_POST['prod_name'];
    $prod_hsn = $_POST['prod_hsn'];
		$prod_rate = $_POST['prod_rate'];
    

    $prod_quality = $_POST['prod_quality'];
    $prod_variety = $_POST['prod_variety'];
    $prod_sub_variety = $_POST['prod_sub_variety'];


  /*  print_r($prod_quality);
    echo'<br>';
    print_r($prod_variety);
     echo'<br>';
    print_r($prod_sub_variety);*/






     $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

  
		if(!isset($errorMsg)){
			$sql = "update products
									set prod_name = '".$prod_name."',
										prod_hsn = '".$prod_hsn."',
                    prod_rate = '".$prod_rate."',
                    username = '".$username."',
                    updated_at = '".$timestamp."'
					          where id=".$id;
			$result = mysqli_query($conn, $sql);


      $IdArr=array();
			if($result)
      {
            foreach ($prod_quality as $key => $value) 
            {
              if(is_numeric($key))
              {
                  array_push($IdArr,$key);
                  $sql_pq = "update product_sub_items
                  set value = '".$value."'
                  where id=".$key;
                  $result_pq = mysqli_query($conn, $sql_pq);

              }
              else
              {
                $sql_pq="insert into product_sub_items(product_id,indicator,value) values('".$id."','1','".$value."')";
                $result_pq = mysqli_query($conn, $sql_pq);
              }
            }


            foreach ($prod_variety as $key => $value) 
            {
                if(is_numeric($key))
                {
                   array_push($IdArr,$key);
                   $sql_pv = "update product_sub_items
                  set value = '".$value."'
                  where id=".$key;
                  $result_pv = mysqli_query($conn, $sql_pv);
  
                }
                else
                {
                  $sql_pv="insert into product_sub_items(product_id,indicator,value) values('".$id."','2','".$value."')";
                  $result_pv = mysqli_query($conn, $sql_pv);
                }
            }

           


            foreach ($prod_sub_variety as $key => $value) 
            {
              if(is_numeric($key))
              {
                 array_push($IdArr,$key);
                  $sql_sv = "update product_sub_items
                  set value = '".$value."'
                  where id=".$key;
                  $result_sv = mysqli_query($conn, $sql_sv);

              }
              else
              {
                $sql_sv="insert into product_sub_items(product_id,indicator,value) values('".$id."','3','".$value."')";
                $result_sv = mysqli_query($conn, $sql_sv);
              }
            }

           /* print_r($IdArr);
            echo "<br>";
            print_r($oldIdArr);
            echo "<br>";*/
            $deletedId=array_diff($oldIdArr,$IdArr);

            foreach ($deletedId as $value) 
            {
              $sql_del = "delete from product_sub_items where id=".$value;
              mysqli_query($conn, $sql_del);
            }
           
          


				$successMsg = 'New record updated successfully';
				$page=1;
        if(isset($_GET['page']))
        {
          $page=$_GET['page'];
        }
        header("Location: index.php?page=$page");
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
        echo $errorMsg;
			}
		}

	}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Product Edit</title>
 
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Product</span></a>
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

                 <!-- last change on Record START-->
          <?php
          $sqlLastChange="select username,updated_at from products where id='".$row['id']."'";

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
                Edit Product Details
              </div>
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  <div class="row">
                    <div class="form-group col-sm-4">
                      <label for="prod_name">Product Name</label>
                      <input type="text" class="form-control" name="prod_name"  placeholder="Enter Product Name" value="<?php echo $row['prod_name']; ?>">
                    </div>
                    <div class="form-group col-sm-4">
                      <label for="prod_hsn">Product HSN</label>
                      <input type="text" class="form-control" name="prod_hsn" placeholder="Enter Product HSN" value="<?php echo $row['prod_hsn']; ?>">
                    </div>
                    <div class="form-group col-sm-4">
                      <label for="prod_rate">Product Rate</label>
                      <input type="text" class="form-control" name="prod_rate" placeholder="Enter Rate" value="<?php echo $row['prod_rate']; ?>">
                    </div>

                    <div class="col-md-5">
                     <div class="field_wrapper_bales">
                      <?php  
                      $q_count=0;
                      if(count($row2)>0)
                      {
                        $key=0;
                        
                        foreach ($row2 as $value) 
                        {
                          if($value['indicator']=='1')
                          {
                             $q_count=$q_count+1;
                         
                          $key=$key+1;
                          if($key==1)
                          {
                        
                      ?>
                  
                      <div class="bales_<?php echo $key ?>">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_variety">Product Quality</label>
                      <input type="text" class="form-control" name="prod_quality[<?php echo $value['id'] ?>]" placeholder="Enter Product Quality" value="<?php echo $value['value'] ?>">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_bales_button" title="Add Product Quality">Add</a>
                      </div>
                      </div>
                      </div>
                   
                     <?php 
                        }
                        //if key is not equal to 1
                        else
                        {
                        ?>
                        
                        <div class="bales_<?php echo $key ?>">
                          <div class="row">
                            <div class="form-group col-md-8">
                              <input type="text" class="form-control" name="prod_quality[<?php echo $value['id'] ?>]" placeholder="Enter Product Quality" value="<?php echo $value['value'] ?>">
                            </div>
                            <div class="col-md-4">
                              <a href="javascript:void(0);"  onclick="qualityVarRemove(this)" class="btn btn-danger remove_prod_bales">-</a>
                            </div>
                          </div>
                        </div>
                     
                        <?php
                            }
                          }
                        }
                      }
                      else // if no value Available in column
                      { ?>
                      
                      <div class="bales_1">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_variety">Product Quality</label>
                      <input type="text" class="form-control" name="prod_quality[new-1]" placeholder="Enter Product Quality" value="">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_bales_button" title="Add Product Quality">Add</a>
                      </div>
                      </div>
                      </div>
                    
                      <?php
                      } 
                      ?>

                       </div>
                    </div>

                    </div>


                     <div class="row">
                      <div class="col-md-5">
                    <div class="field_wrapper_variety">
                      <?php
                      $v_count=0;
                      if(count($row2)>0)
                      {
                        $key=0;
                        foreach ($row2 as $value) 
                        {
                          if($value['indicator']=='2')
                          {
                          $key=$key+1;
                          $v_count=$v_count+1;
                          if($key==1)
                          {
                        
                      ?>
                  
                      <div class="main_variety_<?php echo $key ?>">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_variety">Product Variety</label>
                      <input type="text" class="form-control" name="prod_variety[<?php echo $value['id'] ?>]" placeholder="Enter Product Variety" value="<?php echo $value['value'] ?>">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_variety_button" title="Add Variety">Add</a>
                      </div>
                      </div>
                      </div>
                    
                     <?php 
                        }
                        //if key is not equal to 1
                        else
                        {
                        ?>
                        <div class="main_variety_<?php echo $key ?>">
                          <div class="row">
                            <div class="form-group col-md-8">
                              <input type="text" class="form-control" name="prod_variety[<?php echo $value['id'] ?>]" placeholder="Enter Product Variety" value="<?php echo $value['value'] ?>">
                            </div>
                            <div class="col-md-4">
                              <a href="javascript:void(0);" onclick="varRemove(this)" class="btn btn-danger remove_prod_variety">-</a>
                            </div>
                          </div>
                        </div>
                        <?php
                            }
                          }
                        }
                      }
                      else // if no value Available in column
                      { ?>
                       
                      <div class="main_variety_1">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_variety">Product Variety</label>
                      <input type="text" class="form-control" name="prod_variety[new-1]" placeholder="Enter Product Variety" value="">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_variety_button" title="Add Variety">Add</a>
                      </div>
                      </div>
                      </div>
                 
                      <?php
                      } 
                      ?>
                    </div>
                  </div>

                


                   <!--  sub variety -->


                    <div class="col-md-5">
                      <div class="field_wrapper_sub_variety">
                      <?php 
                      $s_count=0; 
                      if(count($row2)>0)
                      {
                        $key=0;
                        foreach ($row2 as $value) 
                        {
                          if($value['indicator']=='3')
                          {
                            $key=$key+1;
                             $s_count=$s_count+1;
                            if($key==1)
                            {
                        
                      ?>
                   
                      <div class="sub_variety_<?php echo $key ?>">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_sub_variety">Product Sub Variety</label>
                      <input type="text" class="form-control" name="prod_sub_variety[<?php echo $value['id'] ?>]" placeholder="Enter Sub Product Variety" value="<?php echo $value['value'] ?>">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_variety_sub_button" title="Add Variety">Add</a>
                      </div>
                      </div>
                      </div>
                   
                     <?php 
                        }
                        //if key is not equal to 1
                        else
                        {
                        ?>
                       
                        <div class="sub_variety_<?php echo $key ?>">
                          <div class="row">
                            <div class="form-group col-md-8">
                              <input type="text" class="form-control" name="prod_sub_variety[<?php echo $value['id'] ?>]" placeholder="Enter Product Sub Variety" value="<?php echo $value['value'] ?>">
                            </div>
                            <div class="col-md-4">
                              <a href="javascript:void(0);" onclick="subVarRemove(this)" class="btn btn-danger remove_prod_variety">-</a>
                            </div>
                          </div>
                        </div>
                        
                        <?php
                            }
                          }
                        }
                      }
                      else // if no value Available in column
                      { ?>
                        
                      <div class="sub_variety_1">
                      <div class="row"> 
                        <div class="form-group col-md-8">
                      <label for="prod_sub_variety">Product Sub Variety</label>
                      <input type="text" class="form-control" name="prod_sub_variety[new-1]" placeholder="Enter Product Sub Variety" value="">                
                      </div>
                      <div class="col-md-4">
                      <a style="margin-top: 32px;" href="javascript:void(0);" class="btn btn-primary add_variety_sub_button" title="Add Sub Variety">Add</a>
                      </div>
                      </div>
                      </div>
                    
                      <?php
                      } 
                      ?>
                    </div>

                  </div>

                    </div>

                     
                 <div class="form-group">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Submit</button>
                    </div>
                </form>
              </div>
            </div>
         
        </div>
      </div>
</div>
</div>

<script type="text/javascript">


    //Add Product Quality
        var k="<?php echo $q_count ?>";
        if(k==='0')
         {
          k=1;
         }

        var field_wrapper_bales = $('.field_wrapper_bales'); 

          $('.add_bales_button').click(function(){    

              k=parseInt(k)+1;

              var balesfieldHTML= '<div class="bales_'+k+'"><div class="row"><div class="form-group col-md-8"><input type="text" class="form-control" name="prod_quality[new-'+k+']" placeholder="Enter Product Quality" value=""></div><div class="col-md-4"><a href="javascript:void(0);"  onclick="qualityVarRemove(this)" class="btn btn-danger remove_prod_bales">-</a></div></div></div>';

            $(field_wrapper_bales).last().append(balesfieldHTML);
            //$('.bales_'+knumClass+'').last().append(balesfieldHTML);
              
          });

         

          function qualityVarRemove(e)
          {

              $(e).parent('div').parent('div').parent('div').remove(); 
          }
          

  
    


      //Add Variety
        var i="<?php echo $v_count ?>";
         if(i==='0')
         {
          i=1;
         }

        var varietywrapper = $('.field_wrapper_variety'); 
         
          $('.add_variety_button').click(function()
          {    
              i=parseInt(i)+1;
              var varietyfieldHTML= '<div class="main_variety_'+i+'"><div class="row"><div class="form-group col-md-8"><input type="text" class="form-control" name="prod_variety[new-'+i+']" placeholder="Enter Product Variety" value=""></div><div class="col-md-4"><a href="javascript:void(0);" onclick="varRemove(this)" class="btn btn-danger remove_prod_variety">-</a></div></div></div>';

            //$('.main_variety_'+numClass+'').last().append(varietyfieldHTML);
            $(varietywrapper).last().append(varietyfieldHTML);
              
          });
          
          function varRemove(e)
          {

              $(e).parent('div').parent('div').parent('div').remove(); 
          }




          //Add Sub Variety
         var j="<?php echo $s_count ?>";
         if(j==='0')
         {
          j=1;
         }

        var Sub_varietywrapper = $('.field_wrapper_sub_variety'); 
         
          $('.add_variety_sub_button').click(function(){

            
              j=parseInt(j)+1;

              var Sub_varietyfieldHTML= '<div class="sub_variety_'+j+'"><div class="row"><div class="form-group col-md-8"><input type="text" class="form-control" name="prod_sub_variety[new-'+j+']" placeholder="Enter Product Sub Variety" value=""></div><div class="col-md-4"><a href="javascript:void(0);" onclick="varRemove(this)" class="btn btn-danger remove_prod_sub_variety">-</a></div></div></div>';

            //$('.sub_variety_'+numClass+'').last().append(Sub_varietyfieldHTML);
            $(Sub_varietywrapper).last().append(Sub_varietyfieldHTML);
              
          });
          
          /*$(Sub_varietywrapper).on('click', '.remove_prod_sub_variety', function(e){
              e.preventDefault();
              $(this).parent('div').parent('div').parent('div').remove();  
          });*/


      

          function subVarRemove(e)
          {

              $(e).parent('div').parent('div').parent('div').remove(); 
          }

         

  </script>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  </body>
</html>

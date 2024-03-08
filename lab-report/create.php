<?php
require_once('../db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from pur_conf where pur_conf=".$id;
    $result = mysqli_query($conn, $sql);
    $value = 'no';

   if (mysqli_num_rows($result) > 0) 
      {
      $row = mysqli_fetch_assoc($result);
      $value = 'yes';
      }
    else 
      {
      $errorMsg = 'Could not Find Any Record';
      }
  }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Lab Report</title>
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
        <a class="navbar-brand" href="index.php">Lab Report List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i>Back</a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">Create Lab Report</div>
              <div class="row">
              <div class="card-body col-sm-3 col-md-2">
                <h5>Contracted Qulity Specification</h5>
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                
                <div class="form-group">                     
                  <label>Product Length</label><br/>
                  <input type="text" class="form-control base-length" readonly value="<?php echo $row['pro_length'] ?>">
                </div>

                                <div class="form-group">                     
                  <label>Product Mean Length</label><br/>
                  <input type="text" class="form-control base-meanlen" readonly value="<?php echo $row['pro_meanlen'] ?>">
                </div>

                <div class="form-group">                     
                  <label>Product UI</label><br/>
                  <input type="text" class="form-control base-ui" readonly value="<?php echo $row['pro_ui'] ?>">
                </div>

                <div class="form-group">                     
                  <label>Product STR</label><br/>
                  <input type="text" readonly class="form-control base-str" value="<?php echo $row['pro_str'] ?>">
                </div>                  

                <div class="form-group">                     
                  <label>Product SFI</label><br/>
                  <input type="text" readonly class="form-control base-sfi" value="<?php echo $row['pro_sfi'] ?>">
                </div>                

                <div class="form-group">                     
                  <label>Product MIC</label><br/>
                  <input type="text" readonly class="form-control base-mic" value="<?php echo $row['pro_mic'] ?>">
                </div>                
                <div class="form-group">                     
                  <label>Product B+</label><br/>
                  <input type="text" readonly class="form-control base-bplus" value="<?php echo $row['pro_b'] ?>">
                </div>                
                <div class="form-group">                     
                  <label>Product CG</label><br/>
                  <input type="text" readonly class="form-control base-cg" value="<?php echo $row['pro_cg'] ?>">
                </div>
                <div class="form-group">                     
                  <label>Product Trash</label><br/>
                  <input type="text" readonly class="form-control base-trash" value="<?php echo $row['pro_trash'] ?>">
                </div>                
                <div class="form-group">                     
                  <label>Product Moisture</label><br/>
                  <input type="text" readonly class="form-control base-mois" value="<?php echo $row['pro_mois'] ?>">
                </div>                

                </form>
              </div>

              <!--- Lab Report Quality Specification -->

                   <div class="card-body col-sm-3 col-md-2">
                    <h5>Lab Report Quality Specification</h5>
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
              

                <div class="form-group">                     
                  <label for="pro_length">Product Length</label><br/>
                  <input type="text" name ="pro_length" class="form-control lab-length" value="" placeholder="Enter Pro Length">
                </div>

                                <div class="form-group">                     
                  <label for="pro_meanlen">Product Mean Length</label><br/>
                  <input type="text" name="pro_meanlen" class="form-control lab-meanlen" value="" placeholder="Enter Pro Mean Length">
                </div>

                <div class="form-group">                     
                  <label for="pro_ui">Product UI</label><br/>
                  <input type="text" name="pro_ui" value="" class="form-control lab-ui" placeholder="Enter Product UI">
                </div>

                <div class="form-group">                     
                  <label for="pro_str">Product STR</label><br/>
                  <input type="text" name="pro_str" value="" class="form-control lab-str" placeholder="Enter Pro STR">
                </div>                  

                <div class="form-group">                     
                  <label for="pro_sfi">Product SFI</label><br/>
                  <input type="text" name="pro_sfi" value="" class="form-control lab-sfi" placeholder="Enter SFI">
                </div>                

                <div class="form-group">                     
                  <label for="pro_mic">Product MIC</label><br/>
                  <input type="text" name="pro_mic" value="" class="form-control lab-mic" placeholder="Please enter MIC">
                </div>                
                <div class="form-group">                     
                  <label for="pro_b">Product B+</label><br/>
                  <input type="text" name="pro_b" value="" class="form-control lab-bplus" placeholder="Enter Pro B+">
                </div>                
                <div class="form-group">                     
                  <label for="pro_cg">Product CG</label><br/>
                  <input type="text" name="pro_cg" value="" class="form-control lab-cg" placeholder="Enter Product CG">
                </div>
                <div class="form-group">                     
                  <label for="pro_trash">Product Trash</label><br/>
                  <input type="text" name="pro_trash" value="" class="form-control lab-trash" placeholder="Enter Product Trash">
                </div>                
                <div class="form-group">                     
                  <label for="pro_mois">Product Moisture</label><br/>
                  <input type="text" name="pro_mois" value="" class="form-control lab-mois" placeholder="Enter Product Moisture">
                </div>                

                </form>
              </div>


                            <div class="card-body col-sm-3 col-md-2">
                              <h5>Variation Quality Specification</h5>
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                
                <div class="form-group">                     
                  <label>Product Length</label><br/>
                  <input type="text" class="form-control" readonly  id="length" value="">
                </div>

                                <div class="form-group">                     
                  <label>Product Mean Length</label><br/>
                  <input type="text" class="form-control" readonly id="meanlen" value="">
                </div>

                <div class="form-group">                     
                  <label>Product UI</label><br/>
                  <input type="text" class="form-control" readonly id="ui" value="">
                </div>

                <div class="form-group">                     
                  <label>Product STR</label><br/>
                  <input type="text" class="form-control" readonly id="str" value="">
                </div>                  

                <div class="form-group">                     
                  <label>Product SFI</label><br/>
                  <input type="text" class="form-control" readonly value="" id="sfi">
                </div>                

                <div class="form-group">                     
                  <label>Product MIC</label><br/>
                  <input type="text" readonly class="form-control" value="" id="mic">
                </div>                
                <div class="form-group">                     
                  <label>Product B+</label><br/>
                  <input type="text" readonly class="form-control" value="" id="bplus">
                </div>                
                <div class="form-group">                     
                  <label>Product CG</label><br/>
                  <input type="text" readonly class="form-control" value="" id="cg">
                </div>
                <div class="form-group">                     
                  <label>Product Trash</label><br/>
                  <input type="text" readonly class="form-control" value="" id="trash">
                </div>                
                <div class="form-group">                     
                  <label>Product Moisture</label><br/>
                  <input type="text" readonly class="form-control" value="" id="mois">
                </div>                


                </form>
              </div>
                       <div class="card-body col-sm-3 col-md-2">
                        <h5>Allowable In Quality Specification</h5>
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
               

                <div class="form-group">                     
                  <label>Product Length</label><br/>
                  <input type="text" class="form-control alw-length" placeholder="Enter Length" value="">
                </div>

                                <div class="form-group">                     
                  <label>Product Mean Length</label><br/>
                  <input type="text" class="form-control alw-meanlen" placeholder="Enter Mean Length" value="">
                </div>

                <div class="form-group">                     
                  <label>Product UI</label><br/>
                  <input type="text" class="form-control alw-ui" placeholder="Enter UI" value="">
                </div>

                <div class="form-group">                     
                  <label>Product STR</label><br/>
                  <input type="text" class="form-control alw-str" placeholder="Enter STR" value="">
                </div>                  

                <div class="form-group">                     
                  <label>Product SFI</label><br/>
                  <input type="text" class="form-control alw-sfi" placeholder="Enter SFI" value="">
                </div>                

                <div class="form-group">                     
                  <label>Product MIC</label><br/>
                  <input type="text" class="form-control alw-mic" placeholder="Enter MIC" value="">
                </div>                
                <div class="form-group">                     
                  <label>Product B+</label><br/>
                  <input type="text" class="form-control alw-bplus" placeholder="Enter B+" value="">
                </div>                
                <div class="form-group">                     
                  <label>Product CG</label><br/>
                  <input type="text" class="form-control alw-cg" placeholder="Enter CG" value="">
                </div>
                <div class="form-group">                     
                  <label>Product Trash</label><br/>
                  <input type="text" class="form-control alw-trash" placeholder="Enter Trash" value="">
                </div>                
                <div class="form-group">                     
                  <label>Product Moisture</label><br/>
                  <input type="text" class="form-control alw-mois" placeholder="Enter Moisture" value="">
                </div>                

                </form>
              </div>
                       <div class="card-body col-sm-3 col-md-2">
                        <h5>Net Variation In Quality Specification</h5>
                <form class="" action="add.php" method="post" enctype="multipart/form-data">


                <div class="form-group">                     
                  <label>Product Length</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-length">
                </div>

                                <div class="form-group">                     
                  <label>Product Mean Length</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-meanlength">
                </div>

                <div class="form-group">                     
                  <label>Product UI</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-ui">
                </div>

                <div class="form-group">                     
                  <label>Product STR</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-str">
                </div>                  

                <div class="form-group">                     
                  <label>Product SFI</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-sfi">
                </div>                

                <div class="form-group">                     
                  <label>Product MIC</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-mic">
                </div>                
                <div class="form-group">                     
                  <label>Product B+</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-bplus">
                </div>                
                <div class="form-group">                     
                  <label>Product CG</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-cg">
                </div>
                <div class="form-group">                     
                  <label>Product Trash</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-trash">
                </div>                
                <div class="form-group">                     
                  <label>Product Moisture</label><br/>
                  <input type="text" readonly class="form-control" value="" id="net-mois">
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
    </div>
  </div>




     <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

     <script type="text/javascript">


        $(document).ready(function(){
$('input[type="text"]').keyup(function () {
  var baselength = parseFloat($('.base-length').val());
  var lablength = parseFloat($('.lab-length').val());
  var basemeanlen = parseFloat($('.base-meanlen').val());
  var labmeanlen = parseFloat($('.lab-meanlen').val());
  var baseui = parseFloat($('.base-ui').val());
  var labui = parseFloat($('.lab-ui').val());
  var basestr = parseFloat($('.base-str').val());
  var labstr = parseFloat($('.lab-str').val());  
  var basesfi = parseFloat($('.base-sfi').val());
  var labsfi = parseFloat($('.lab-sfi').val());  
  var basemic = parseFloat($('.base-mic').val());
  var labmic = parseFloat($('.lab-mic').val());    
  var basebplus = parseFloat($('.base-bplus').val());
  var labbplus = parseFloat($('.lab-bplus').val());    
  var basecg = parseFloat($('.base-cg').val());
  var labcg = parseFloat($('.lab-cg').val());   
  var basetrash = parseFloat($('.base-trash').val());
  var labtrash = parseFloat($('.lab-trash').val());   
  var basemois = parseFloat($('.base-mois').val());
  var labmois = parseFloat($('.lab-mois').val());   
  
  var varlength = parseFloat($('#length').val());
  var alwlength = parseFloat($('.alw-length').val());

  var varmeanlen = parseFloat($('#meanlen').val());
  var alwmeanlen = parseFloat($('.alw-meanlen').val());

  var varui = parseFloat($('#ui').val());
  var alwui = parseFloat($('.alw-ui').val());

  var varstr = parseFloat($('#str').val());
  var alwstr = parseFloat($('.alw-str').val());

  var varsfi = parseFloat($('#sfi').val());
  var alwsfi = parseFloat($('.alw-sfi').val());

  var varmic = parseFloat($('#mic').val());
  var alwmic = parseFloat($('.alw-mic').val());

  var varbplus = parseFloat($('#bplus').val());
  var alwbplus = parseFloat($('.alw-bplus').val());

  var varcg = parseFloat($('#cg').val());
  var alwcg = parseFloat($('.alw-cg').val());

  var vartrash = parseFloat($('#trash').val());
  var alwtrash = parseFloat($('.alw-trash').val());

  var varmois = parseFloat($('#mois').val());
  var alwmois = parseFloat($('.alw-mois').val());

          var length = (baselength-lablength);
          var meanlen = (basemeanlen-labmeanlen);
          var ui = (baseui-labui);
          var str = (basestr-labstr);
          var sfi = (basesfi-labsfi);
          var mic = (basemic-labmic);
          var bplus = (basebplus-labbplus);
          var cg = (basecg-labcg);
          var trash = (basetrash-labtrash);
          var mois = (basemois-labmois);
          var netlength = (varlength-alwlength);
          var netmeanlen = (varmeanlen-alwmeanlen);
          var netui = (varui-alwui);
          var netstr = (varstr-alwstr);
          var netsfi = (varsfi-alwsfi);
          var netmic = (varmic-alwmic);
          var netbplus = (varbplus-alwbplus);
          var netcg = (varcg-alwcg);
          var nettrash = (vartrash-alwtrash);
          var netmois = (varmois-alwmois);


          if(netlength<0){netlength=0;};
          

          $("input#length").val(length);
          $("input#meanlen").val(meanlen);
          $("input#ui").val(ui);
          $("input#str").val(str);
          $("input#sfi").val(sfi);
          $("input#mic").val(mic);
          $("input#bplus").val(bplus);
          $("input#cg").val(cg);
          $("input#trash").val(trash);
          $("input#mois").val(mois);
          $("input#net-length").val(netlength);
          $("input#net-meanlength").val(netmeanlen);
          $("input#net-ui").val(netui);
          $("input#net-str").val(netstr);
          $("input#net-sfi").val(netsfi);
          $("input#net-mic").val(netmic);
          $("input#net-bplus").val(netbplus);
          $("input#net-cg").val(netcg);
          $("input#net-trash").val(nettrash);
          $("input#net-mois").val(netmois);
});
});

</script>

</body>
</html>
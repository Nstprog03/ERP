<?php
  include('../db.php');

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Product Confirmation Database</title>
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
        <div class="container">
          <a class="navbar-brand" href="index.php">Product Confirmation Database</a>
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

      <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Product Confirmation List</div>
                      <div class="card-body">


<form action="" method="post" class="mb-3">
      <div class="select-block">
        
                       <?php
                            $sql = "select * from pro_conso";
                            $result = mysqli_query($conn, $sql);
                            $sql2 = "SELECT DISTINCT party FROM pro_conso";
                            $result2 = mysqli_query($conn, $sql2);
                          ?>   
               
                      <div class="form-group">
                      <label for="pur_conf">Purchase Party</label>
                          <select name="pur_conf_party" class="form-control" onchange="$('select[name=pur_conf] option').hide();$('select[name=pur_conf] option[data-party='+this.value+']').show();">
                            <option value="" disabled selected>Choose Parties</option>
                            <?php                   
                              foreach ($conn->query($sql2) as $result2) 
                              {
                                    echo "<option  value=" .$result2['party']. ">" .$result2['party']. "</option>";
                              }

                            ?>                              
                          </select>     
                        </div>
                          <div class="form-group">
                      <label for="pur_conf">Purchase Confirmation No</label>              
                           <select name="pur_conf" class="form-control">
                            <option value="" disabled selected>Choose option</option>
                            <?php                   
                              foreach ($conn->query($sql) as $result) 
                              {
                                    echo "<option data-party=" .$result['party']. " value=" .$result['pur_conf']. ">" .$result['pur_conf']. "</option>";
                              }

                            ?>                              
                            </select>
                    </div>
        
      </div>

      <input type="submit" name="submit" vlaue="Choose options">
    </form>

    <?php
      if(isset($_POST['submit'])){
        if(!empty($_POST['pur_conf'])) {
          $selected = $_POST['pur_conf'];
          echo 'You have chosen: ' . $selected;
        } else {
          echo 'Please select the value.';
        }
      }
    ?>
                        
                                            
                  
                              <a href="create.php?id=<?php echo  $selected ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                       
                         
                    </div>
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
  </body>
</html>

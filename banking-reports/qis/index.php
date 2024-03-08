<?php
session_start();
include('../../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>QIS Reports</title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> QIS Database</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
         <!--  <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
              <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>
              </ul>
          </div> -->
        </div>
      </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">QIS Report</div>
                      <div class="card-body">
                         <form action="set_session.php" method="post">
                            <div class="row">

                              


                              <div class="col-md-12">
                          <div class="form-group">
                            <label for="firm">Select Firm</label>
                                <?php
                                    $sql = "select * from party";
                                    $result = mysqli_query($conn, $sql);
                                ?>                      
                            <select name="firm" class="form-control">
                            <?php                   
                                foreach ($conn->query($sql) as $result) 
                                {
                                    echo "<option  value='".$result['party_name'].'/'.$result['id']."'>".$result['party_name']. "</option>";
                                }
                            ?>                              
                            </select>
                        </div>
                      </div>

                      <div class="col-md-12">
                                <div class="form-group">
                                  <label for="firm">Select Form</label>                     
                                  <select name="form" class="form-control">
                                     <option value="form1">Form 1</option> 
                                     <option value="form2">Form 2</option>
                                  </select>
                                </div>
                              </div>

                      <div class="col-md-12">
                                <div class="form-group">
                                  <label for="firm">Select Quarter</label>             
                                  <select name="quarter" class="form-control">
                                     <option value="quarter1">Quarter 1 (Jan, Feb, March)</option> 
                                     <option value="quarter2">Quarter 2 (April , May, June)</option>
                                     <option value="quarter3">Quarter 3 (July, Aug, Sept)</option>
                                     <option value="quarter4">Quarter 4 (Oct, Nov, Dec)</option>
                                  </select>
                                </div>
                              </div>

                            


                              <div class="form-group col-md-12">
                                  <label for="year">Select Year</label>
                                <select name="year" class="form-control" id="selectElementId">
                                </select>
                              </div>

                              <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" name="submit" value="submit" class="btn btn-primary waves">Submit</button>
                                </div>
                              </div>

                          



                          </div>

                         </form>
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

      var min = 1950,
      max = min + 100,
      select = document.getElementById('selectElementId');

      //getCurrent Year
       var d = new Date();
       var curYear = d.getFullYear();



      for (var i = min; i<=max; i++){

        if(i==curYear)
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

    </script>
  </body>
</html>

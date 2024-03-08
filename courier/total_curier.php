<?php
  require_once('../db.php');
  $assreport_dir = 'files/courier/';
  $currentDate = new DateTime();
  $date = $currentDate->add(new DateInterval('P2D'));
  $before2day = $date->format('Y-m-d');

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>View Courier Details</title>
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

<style type="text/css">
.modal-dialog {width:800px;}
.thumbnail {margin-bottom:6px;}
.modal.fade .modal-dialog {
      -webkit-transform: translate(0, 0);
      -ms-transform: translate(0, 0); // IE9 only
          transform: translate(0, 0);

 }
</style>
    
    
    
  </head>
  <body>


    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

            
      <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php">Total Courier Details</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" 
              id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
                <ul class="navbar-nav">
                  <a class="btn btn-outline-danger" 
                    href="http://erp.local.com/index.php">
                    <i class="fa fa-sign-out-alt"></i><span>Back</span>
                  </a>
                </ul>
          </div>
        </div>
      </nav>

      <div class="container">
        <div class="row justify-content-center">
          <div class="card">
            <div class="card-header">
              Total Courier Details 
            </div>
            <div class="card-body">
              <div class="row">
               
                <div class="col-md">
                  <div class="row">

                    <table class="table">
  <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Send Date</th>
      <th scope="col">Docket Type</th>
    </tr>
  </thead>
  <tbody>
    <?php $sql = "select * from courier where send_date = '$before2day'";
            $result = mysqli_query($conn, $sql);        
            foreach($conn->query($sql) as $result) 
            { ?>
    <tr>
      <th scope="row"><a href="show.php?id=<?php echo $result['id'] ?>"><?php echo $result['id']; ?></a></th>
      <td><a href="show.php?id=<?php echo $result['id'] ?>"><?php echo $result['send_date']; ?></a></td>
      <td><a href="show.php?id=<?php echo $result['id'] ?>"><?php echo $result['docket_type']; ?></a></td>

    </tr>
    <?php } ?>
  </tbody>
</table>


                      
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
    </script>
    </body>
  </html>

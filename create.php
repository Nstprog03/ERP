<?php
  include('add.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>PHP CRUD</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">
  </head>
  <body>

    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
      <div class="container">
        <a class="navbar-brand" href="index.php">PHP CRUD WITH IMAGE</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto"></ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item"><a class="btn btn-outline-danger" href="index.php"><i class="fa fa-sign-out-alt"></i></a></li>
            </ul>
        </div>
      </div>
    </nav>

      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">Create</div>
              <div class="card-body">
                <form class="" action="add.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                      <label for="party_name">Party Name</label>
                      <input type="text" class="form-control" name="party_name"  placeholder="Enter Party Name" value="">
                    </div>
                    <div class="form-group">
                      <label for="party_email">Party Email</label>
                      <input type="text" class="form-control" name="party_email" placeholder="Enter Party Email" value="">
                    </div>
                    <div class="form-group">
                      <label for="party_address">Party Address</label>
                      <input type="text" class="form-control" name="party_address" placeholder="Enter Party Address" value="">
                    </div>

                    <div class="form-group">
                      <label for="iec_code">IEC Code</label>
                      <input type="text" class="form-control" name="iec_code" placeholder="Enter IEC Code" value="">
                    </div>

                    <div class="form-group">
                      <label for="ud_aadhar">Udhyog Aadhaar</label>
                      <input type="text" class="form-control" name="ud_aadhar" placeholder="Enter Udhyog Aadhaar" value="">
                    </div>

                    <div class="form-group">
                      <label for="contact_per">Contact Person</label>
                      <input type="text" class="form-control" name="contact_per" placeholder="Enter Contact Person" value="">
                    </div>                    

                    <div class="form-group">
                      <label for="contact_number">Contact Number</label>
                      <input type="text" class="form-control" name="contact_number" placeholder="Enter Contact Number" value="">
                    </div>    


                    <div class="form-group">
                      <label for="bank_name">Bank Name</label>
                      <input type="text" class="form-control" name="bank_name" placeholder="Enter Bank Name" value="">
                    </div>    

                    <div class="form-group">
                      <label for="bank_ac_number">Bank Account Number</label>
                      <input type="text" class="form-control" name="bank_ac_number" placeholder="Enter Bank Account Number" value="">
                    </div>    


                    <div class="form-group">
                      <label for="bank_branch">Bank Branch Name</label>
                      <input type="text" class="form-control" name="bank_branch" placeholder="Bank Branch Name" value="">
                    </div>    


                    <div class="form-group">
                      <label for="waterid_img">Water ID Image</label>
                      <input type="file" class="form-control" name="waterid_img" value="">
                    </div>

                    <div class="form-group">
                      <label for="gst_img">GST Image</label>
                      <input type="file" class="form-control" name="gst_img" value="">
                    </div>

                    <div class="form-group">
                      <label for="cheque_img">Cheque Image</label>
                      <input type="file" class="form-control" name="cheque_img" value="">
                    </div>

                    <div class="form-group">
                      <label for="iec_img">IEC Image</label>
                      <input type="file" class="form-control" name="iec_img" value="">
                    </div>

                    <div class="form-group">
                      <label for="msme_img">MSME Image</label>
                      <input type="file" class="form-control" name="msme_img" value="">
                    </div>

                    <div class="form-group">
                      <label for="fact_img">Fact Image</label>
                      <input type="file" class="form-control" name="fact_img" value="">
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

    <script src="js/bootstrap.min.js" charset="utf-8"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
  </body>
</html>

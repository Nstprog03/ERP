<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}
$getYear=$_SESSION['pur_financial_year'];
$year_array=explode("/",$getYear);

if (isset($_GET['id'])) {

  $id = $_GET['id'];
  $sql = "select * from pur_conf where id=".$id;

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
    }
   

}



?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Edit Product Confirmation Record</title>
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
       
       <link rel="stylesheet" href="../PHPLibraries/richtexteditor/rte_theme_default.css" />
    <script type="text/javascript" src="../PHPLibraries/richtexteditor/rte.js"></script>
    <script type="text/javascript" src='../PHPLibraries/richtexteditor/plugins/all_plugins.js'></script>

     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

         $(".datepicker").datepicker({
            
              dateFormat: "dd/mm/yy",
               changeMonth: true,
                changeYear: true,
              maxDate: new Date('<?php echo($year_array[1]) ?>'),
              minDate: new Date('<?php echo($year_array[0]) ?>')
           
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
        <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Edit Product Confirmation Record</span></a>
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
                  <div class="firm-selectio">
             <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["pur_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["pur_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
         $sqlLastChange="select username,updated_at from pur_conf where id='".$row['id']."'";

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
          
            <div class="card edit_pur_report">
              <div class="card-header">Edit Product Confirmation Report</div>
              <div class="card-body">
                <form class="" action="update.php" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="page" value="<?php echo $page ?>">

                  <div class="row">
                   
                  <div class="form-group col-md-4">
                    <label for="pur_conf">Purchase Confirmation No</label>
                    <input type="text" class="form-control" id="pur_conf" name="pur_conf" placeholder="Enter Purchase Confirmation" value="<?php echo $row['pur_conf']; ?>" readonly>
                    </div>

                     <div class="form-group col-md-2">
                      <label> </label>
                     <button style="margin-top: 32px" type="button" class="btn btn-primary" id="btn_regenrate">Regenerate</button>
                      </div>

                  </div>

                  <div class="row">

                  <div class="form-group col-md-4">
                    <label for="conf_type">Confirmation Type</label>
                      
                    <select name="conf_type" class="form-control">
                      <option value="0" <?php if($row['conf_type']==0){echo "selected";} ?>>Original</option>
                      <option value="1" <?php if($row['conf_type']==1){echo "selected";} ?>>Revised</option>
                      <option value="2" <?php if($row['conf_type']==2){echo "selected";} ?>>Cancel</option>
                    </select>
                  </div>


                    <div class="form-group col-md-4">
                <label for="pur_firm">Firm</label>
                    <?php
                        $sql2 = "select * from party";
                        $result2 = mysqli_query($conn, $sql2);
                    ?>                      
                <select id="pur_firm" name="pur_firm" class="form-control">
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


            


             <div class="form-group col-md-4">

                <label for="financial_year">Select Financial Year</label>
                    <?php
                        $sql3 = "select * from financial_year order by id DESC";
                        $result3 = mysqli_query($conn, $sql3);
                    ?>                      
                <select id="financial_year" name="financial_year" class="form-control">
                <?php                   
                    foreach ($conn->query($sql3) as $result3) 
                    {
                       
                        $sdate = date("Y", strtotime($result3['startdate']));
                        $edate = date("Y", strtotime($result3['enddate']));

                        $dt=$result3['id'];

                        $isYearSelected =""; 
                       if($dt==$row['financial_year'])
                       {
                         $isYearSelected = "selected";
                       }
                        echo "<option value='".$dt."'".$isYearSelected.">" .$sdate."-".$edate."</option>";
                    }
                ?>                              
                </select>
            </div>

                <?php
                $ReportDate='';
                if($row['pur_report_date']!='' && $row['pur_report_date']!='0000-00-00')
                {
                $ReportDate=date("d/m/Y", strtotime($row['pur_report_date']));
                }
                ?>

                  <div class="form-group col-md-4">
                    <label for="pur_date">Select Report Date :</label>
                     <input class="form-control datepicker" type="text"  name="pur_date" required="" value="<?php echo $ReportDate; ?>" autocomplete="off">
                  </div>


                  <div class="form-group col-md-4">
                    <label for="party">Select External Party </label>

                      <a class="btn btn-primary btn-sm" target="_blank" href="/external-party/create.php"><i class="fa fa-user-plus"></i></a>

                    <?php
                      $sql4 = "select * from external_party";
                      $result4 = mysqli_query($conn, $sql4);
                    ?>                      
                    <select name="party" id="party" class="form-control searchDropdown" data-live-search="true" onchange="get_GSTNO(this.value)">
                      <?php                   
                        foreach ($conn->query($sql4) as $result4) 
                        {
                           $isEPSelected =""; 
                         if($result4['id']==$row['party'])
                         {
                           $isEPSelected = "selected";
                         }

                          echo "<option  value='".$result4['id']."'".$isEPSelected.">" .$result4['partyname']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="party">GST No.:</label>
                    <input type="text" class="form-control set-gst-no" placeholder="GST No" readonly="readonly">
                </div>


                  <div class="form-group col-md-4">
                    <label for="broker">Select Broker</label>
                    <a class="btn btn-primary btn-sm" target="_blank" href="/broker/create.php"><i class="fa fa-user-plus"></i></a>
                    <?php
                      $sql = "select * from broker";
                      $result = mysqli_query($conn, $sql);                            
                    ?>                      
                    <select name="broker" class="form-control searchDropdown" data-live-search="true">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {

                           $isBKSelected =""; 
                         if($result['id']==$row['broker'])
                         {
                           $isBKSelected = "selected";
                         }


                          echo "<option  value='".$result['id']."'".$isBKSelected.">" .$result['name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label for="product_name">Select Product</label>


                    <?php
                      $query = "select * from products";
                      $find = mysqli_query($conn, $query);
                    ?>                      
                    <select name="product_name" data-live-search="true" class="form-control searchDropdown">
                      <?php                   
                        foreach ($conn->query($query) as $find) 
                        {
                          $isTselected='';
                          if($find['id']==$row['product_name'])
                          {
                            $isTselected='selected';
                          }
                          echo "<option  value='".$find['id']."'".$isTselected.">" .$find['prod_name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_length">Product Length</label>
                      <input type="text" class="form-control" name="pro_length"  placeholder="Product Length" value="<?php if(isset($row)){ echo  $row['pro_length'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_mic">Product MIC</label>
                      <input type="text" class="form-control" name="pro_mic"  placeholder="Product MIC" value="<?php if(isset($row)){ echo  $row['pro_mic'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_rd">Product RD</label>
                      <input type="text" class="form-control" name="pro_rd"  placeholder="Product RD" value="<?php if(isset($row)){ echo  $row['pro_rd'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_trash">Product Trash</label>
                      <input type="text" class="form-control" name="pro_trash"  placeholder="Product Trash" value="<?php if(isset($row)){ echo  $row['pro_trash'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">
                      <label for="pro_mois">Product Moisture</label>
                      <input type="text" class="form-control" name="pro_mois"  placeholder="Product Moisture" value="<?php if(isset($row)){ echo  $row['pro_mois'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">                      
                    <label for="bales">No. Of Bales</label>
                    <input type="text" class="form-control" placeholder="No. Of Bales"  name="bales" value="<?php if(isset($row)){ echo  $row['bales'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>
                  <div class="form-group col-md-4">                      
                    <label for="candy_rate">Candy Rate</label>
                    <input type="text" class="form-control" placeholder="Candy Rate"  name="candy_rate" value="<?php if(isset($row)){ echo  $row['candy_rate'];}?>" onkeypress="return NumericValidate(event,this)">
                  </div>

                  <div class="form-group col-md-4">                  
                    <label for="dispatch">Dispatch</label>
                  <!--   <input type="text" class="form-control" placeholder="Enter dispatch" onkeypress="return lettersValidate(event)"  name="dispatch" id="dispatch" value="<?php if(isset($row)){ echo  $row['dispatch'];}?>"> -->

                   <select name="dispatch" class="form-control">
                      <option value="" <?php if($row['dispatch']==''){ echo 'selected';} ?>>Select</option>
                      <option value="FOR" <?php if($row['dispatch']=='FOR'){ echo 'selected';} ?>>FOR</option>
                      <option value="Regular" <?php if($row['dispatch']=='Regular'){ echo 'selected';} ?>>Regular</option>                          
                    </select>

                  </div>

                

                  <div class="form-group col-md-4">
                    <div class="form-group">
                      <label for="delivery_date">Delivery Date</label>
                       <input type="text" placeholder="Delivery Date" class="form-control datepicker "   name="delivery_date" autocomplete="off" value="<?php echo $row['delivery_date']; ?>"  required>
                      </div>
                  </div>

                  <div class="form-group col-md-4">
                    <div class="form-group">
                      <label for="station">Station</label>
                       <input type="text" placeholder="Enter Station" class="form-control "  name="station" value="<?php echo $row['station']; ?>">
                      </div>
                  </div>

                  

                  </div>



                    <h4>Transport Details</h4>
                  <br>
                  <div class="row">
                    
                    <div class="form-group col-md-4">
                    <label for="transport_name">Select Transnport Name</label>
                    <a class="btn btn-primary btn-sm" target="_blank" href="/transport/create.php"><i class="fa fa-user-plus"></i></a>

                    <?php
                      $sql = "select * from transport";
                      $result = mysqli_query($conn, $sql);
                    ?>                      
                    <select name="transport_name" class="form-control searchDropdown" data-live-search="true">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        {

                          $isTselected='';
                          if($row['trans_name']==$result['id'])
                          {
                            $isTselected='selected';
                          }

                          echo "<option  value='".$result['id']."'".$isTselected.">" .$result['trans_name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>


                
                   <div class="form-group col-md-4">
                    <label for="trans_pay_type">Select Payment Type</label>

                                     
                    <select name="trans_pay_type" class="form-control">
                       <option value="to_be_build"
                       <?php 
                           if($row['trans_pay_type']=='to_be_build')
                            {
                              echo "selected";
                            }
                       ?>>To Be Build</option>


                          <option value="to_be_pay"
                          <?php 
                           if($row['trans_pay_type']=='to_be_pay')
                            {
                              echo "selected";
                            }
                       ?>>To Be Pay</option>

                    </select>
                  </div>


                      
                    
                  </div>

                   <br>




                  <div class="row">
                          <div class="col-md-4">
                              <div class="form-group">  
                                <label for="dispatch">No. Of Vehicle</label>
                                <input type="text" class="form-control" placeholder="Enter No. Of Vehicle"    name="no_of_vehicle" id="no_of_vehicle" value="<?php if(isset($row)){ echo  $row['no_of_vehicle'];}?>" onkeypress="return OnlyNumberValidation(event)">
                              </div>
                          </div>
                           <div class="col-md-3">
                            <div style="margin-top: 30px" class="form-group">
                              <label></label>
                             <button type="button" id="btn_add_veh" class="btn btn-success">Add</button>
                           </div>
                           </div>                         
                  </div> 

                  <div id="" class="row">
                    <div class="col-md-1"></div>
                    <div id="veh_col" class="col-md-5">
                      <?php 
                        if($row['vehicle_no']!='' || $row['vehicle_no']!=null)
                        {
                          $getNos=json_decode($row['vehicle_no']);  
                          if ($getNos!='') {
                            foreach ($getNos as $value) {
                             echo "
                              <div class='form-group vehClass'><input type='text' placeholder='Enter Vehicle No.'' class='form-control' name='veh_nos[]' value='".$value."'/></div>
                             ";
                            }

                          }
                        }

                      ?>

                    </div>
                  </div>


                     <br>
                  <h4>Insurance</h4>
                  <div class="row">
                    
                      <div class="form-group col-md-6">  
                    <label for="ins_cmp_name">Company Name</label>
                    <input type="text" class="form-control" placeholder="Enter Insurance Company Name"  name="ins_cmp_name" value="<?php echo $row['ins_cmpny'] ?>">
                  </div>

                  <div class="form-group col-md-6">  
                    <label for="ins_policy_no">Insaurance Policy No.</label>
                    <input type="text" class="form-control" placeholder="Enter Insaurance Policy No."  name="ins_policy_no" value="<?php echo $row['ins_policy_no'] ?>">
                  </div>
                      
                    
                  </div>


                  <div class="row">
                   
                    <div class="form-group col-md-6">  
                        <label for="pay_term">Payment Terms </label>
                        <input type="text" class="form-control" placeholder="Enter Payment Terms"  name="pay_term"  value="<?php echo $row['pay_term'] ?>">
                    </div>

                    

                    <div class="form-group col-md-6">
                    <label for="party">Laboratory Master</label>
                    <?php
                      $sql = "select * from laboratory_master";
                      $result = mysqli_query($conn, $sql);
                    ?>                      
                    <select name="laboratory_master" class="form-control">
                      <?php                   
                        foreach ($conn->query($sql) as $result) 
                        { 
                          echo "<option  value='".$result['id']."'";
                            if(isset($row['laboratory_master']) && $result['id'] == $row['laboratory_master'])
                            {
                              echo 'selected';
                            }
                          echo ">" .$result['lab_name']. "</option>";
                        }
                      ?>                              
                    </select>
                  </div>


                   
                 


                  <div class="form-group col-md-4">
                    <label for="spl_rmrk">Special Remark</label>
                    <textarea class="form-control" name="spl_rmrk" id="w3review" rows="4" cols="60" placeholder="Special Remark"><?php if(isset($row)){ echo  $row['spl_rmrk'];}?></textarea>
                  </div>



                   </div>
                    <div class="row">
                    <div class="col-md-12">
                     <label for="bill_inst">Terms & Condition</label>
                      <textarea class="form-control" name="term_condtion" id="div_editor1" rows="4" cols="60"><?php echo $row['term_condtion'] ?></textarea>
                    </div>
                  </div>

                  <br>

                  <div class="form-group">
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      <button type="submit" name="Submit" class="btn btn-primary waves">Update</button>
                  </div>
                </form>
                           
              </div>
            </div>
          
        </div>
      </div>

</div>
</div>

<script>  
 $(document).ready(function(){ 

    var editor1 = new RichTextEditor("#div_editor1");

  // editor1.setHTMLCode("");

  function btngetHTMLCode() {
    alert(editor1.getHTMLCode())
  }

  function btnsetHTMLCode() {
    editor1.setHTMLCode("<h1>editor1.setHTMLCode() sample</h1><p>You clicked the setHTMLCode button at " + new Date() + "</p>")
  }
  function btngetPlainText() {
    alert(editor1.getPlainText())
  }


      $('#btn_regenrate').on('click', function() {
              GetNewConfNo();
      });


    function GetNewConfNo() {

        var firm_id=$('#pur_firm :selected').val();
        var fin_year=$('#financial_year :selected').val();
        var main_conf_no=$('#pur_conf').val();

        $.ajax({
            type: "POST",
            url: 'getData.php',
            data: {
              firm_id:firm_id,
              fin_year:fin_year,
              main_conf_no:main_conf_no
            },
            success: function(response)
            {
              console.log(response)

                var jsonData = JSON.parse(response);
                
                $('#pur_conf').val(jsonData.new_conf_no);

                 
           }
       });
      
    }



    $('#btn_add_veh').on('click', function() {

          var numClass = $('.vehClass').length
          var count_veh = parseInt($('#no_of_vehicle').val());
           
          if(count_veh>numClass)
          {
            
            for (i = 0; i < count_veh-numClass; i++) 
            {
              $('#veh_col').append('<div class="form-group vehClass"><input type="text" placeholder="Enter Vehicle No." class="form-control" name="veh_nos[]"/></div>');
            }
          }
          else
          {
            $( ".vehClass" ).each(function(index) {
              var noofveh = index + 1;
              if(noofveh > count_veh ){
                $(this).remove();
              }
            });
          }
          


    });

    //get selected dropdown value id
    if($('.edit_pur_report').is(':visible')){
      var selected_party_id= $('#party :selected').val();
      getSelectedPartyId_GSTNo(selected_party_id);
    }


});

 </script>
   
  <script type="text/javascript">
  function lettersValidate(key) {
    var keycode = (key.which) ? key.which : key.keyCode;

    if ((keycode > 64 && keycode < 91) || (keycode > 96 && keycode < 123))  
    {     
           return true;    
    }
    else
    {
        return false;
    }
         
}

function OnlyNumberValidation(key) {
    var keycode = (key.which) ? key.which : key.keyCode;

    if (keycode >= 48 && keycode <= 57)  
    {     
           return true;    
    }
    else
    {
        return false;
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

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

      
         
  </body>
</html>

<script>

  function getSelectedPartyId_GSTNo(party_id){
      $.ajax({
                type: "POST",
                url: 'get_GSTNO.php',
                data: {party_id:party_id},
                success: function(response)
                {
                    var jsonData = JSON.parse(response);
                //   console.log(jsonData);

                  if(jsonData.status==true){

                      if(jsonData.gstin_data!=''){
                          $('.set-gst-no').val(jsonData.gstin_data);
                    }else{
                      $('.set-gst-no').val(''); 
                    }
                  }else{
                    $('.set-gst-no').val(''); 
                  }
              }
          });
  }


  function get_GSTNO(party_id){
    
        $.ajax({
            type: "POST",
            url: 'get_GSTNO.php',
            data: {party_id:party_id},
            success: function(response)
            {
                var jsonData = JSON.parse(response);
             //   console.log(jsonData);

              if(jsonData.status==true){

                  if(jsonData.gstin_data!=''){
                      $('.set-gst-no').val(jsonData.gstin_data);
                }else{
                  $('.set-gst-no').val(''); 
                }
              }else{
                $('.set-gst-no').val(''); 
              }
           }
       });
  }
</script>

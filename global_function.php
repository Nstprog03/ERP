<?php

function getFileStoragePath($module_directory,$fyear_id=null)
{
  include 'db.php';

  $root_folder=$_SERVER['DOCUMENT_ROOT']."/file_storage/";

  //get current financial year
  $curYear='';
  $curDate=date('Y-m-d');
  $sql = "select * from financial_year";
  foreach ($conn->query($sql) as $result2)
  {
     //get Start Year And End Year
      $syear = date("y", strtotime($result2['startdate']));
      $eyear = date("y", strtotime($result2['enddate']));

     $startdate=date('Y-m-d', strtotime($result2['startdate']));
     $enddate=date('Y-m-d', strtotime($result2['enddate']));

     if(isset($fyear_id) && $fyear_id!='') //module which have financial year in pre-seclection.
     {
      if($result2['id']==$fyear_id)
      {
        $curYear=$syear.'_'.$eyear.'/';
      }
     }
     else if($curDate>=$startdate && $curDate<=$enddate) // normal  module
     {
        $curYear=$syear.'_'.$eyear.'/';
     }

  }

  $root_path=$root_folder.$curYear.$module_directory.'/';

  if (!file_exists($root_path)) {
    mkdir($root_path, 0777, true);
 }

 $store_path=$curYear.$module_directory.'/';

 $data[0]=$root_path; // file move path
 $data[1]=$store_path; // db store path

 

  return $data;
}


function getStaticFileStoragePath($module_directory)
{
  include 'db.php';

  $root_folder=$_SERVER['DOCUMENT_ROOT']."/static_file_storage/";



  $root_path=$root_folder.$module_directory.'/';

  if (!file_exists($root_path)) {
    mkdir($root_path, 0777, true);
 }

 $store_path=$module_directory.'/';

 $data[0]=$root_path; // file move path
 $data[1]=$store_path; // db store path

 

  return $data;
}



?>
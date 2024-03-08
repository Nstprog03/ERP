<?php

include '../db.php';
include '../global_function.php';




//-----------------------------config parameter------------------------------------


$folder_name="audit-report";
$db_table="party_audit_report";
$db_file_column="docimg";

$old_folder="files/";





//-------------------------------------------------------------------------





 $dataArr=array();
 $sql="SELECT id,$db_file_column FROM $db_table";
 $result=mysqli_query($conn,$sql);
 if(mysqli_num_rows($result)>0)
 {
  $i=0;
   while ($row=mysqli_fetch_assoc($result)) 
   {
      if($row[$db_file_column]!='')
      {

         $data=getStaticFileStoragePath($folder_name);
         $root_path=$data[0]; // file move path
         $store_path=$data[1]; // db store path



        $fileArr=explode(',',$row[$db_file_column]);

        $newFileArr=array();
        foreach ($fileArr as $key => $item)
        {
          $newPath=$store_path.$item;

          $oldFolderPath=$old_folder.$item;
          $newFolderPath=$root_path.$item;


          rename($oldFolderPath, $newFolderPath);
          $newFileArr[]=$newPath;
        }

        $newFileArr=implode(',', $newFileArr);
        $sqlUpdate="UPDATE $db_table SET $db_file_column='".$newFileArr."' WHERE id='".$row['id']."'";
        mysqli_query($conn,$sqlUpdate);


        //this array is just for testing purpose
        $dataArr[$i]['id']=$row['id'];
        $dataArr[$i][$db_file_column]=$newFileArr;


        $i++;
      }
   }
 }


echo "<pre>";
print_r($dataArr);
exit;

?>
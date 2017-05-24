<?php
  class general{

    public function deleteFiles(){

     $src1 = glob("temp/uploaded/*");
     foreach ($src1 as $key1 => $value1) {
       if (is_file($value1)) {
         @unlink($value1);
       }
     }

     $src2 = glob("temp/resized/*");
     foreach ($src2 as $key2 => $value2) {
       if (is_file($value2)) {
         @unlink($value2);
       }
     }

   }

  }
?>

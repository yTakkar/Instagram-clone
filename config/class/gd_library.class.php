<?php
  class gd_library{

    public function resize($target, $newcopy, $w, $h, $ext){
      list($w_orig, $h_orig) = getimagesize($target);
      if ($w_orig > $w) {
        $scale_ratio = $w_orig / $h_orig;
        if (($w / $h) > $scale_ratio) {
          $w = $h * $scale_ratio;
        } else {
          $h = $w / $scale_ratio;
        }
      } else if ($w_orig < $w) {
        $w = $w_orig;
        $h = $h_orig;
      }

      $img = "";
      $ext = strtolower($ext);
      if ($ext == "gif"){
        $img = imagecreatefromgif($target);
      } else if($ext =="png"){
        $img = imagecreatefrompng($target);
      } else {
        $img = imagecreatefromjpeg($target);
      }
      $tci = imagecreatetruecolor($w, $h);
      // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
      imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
      if ($ext == "gif"){
        imagegif($tci, $newcopy);
      } else if($ext =="png"){
        imagepng($tci, $newcopy);
      } else {
        imagejpeg($tci, $newcopy, 84);
      }
    }

  }
?>

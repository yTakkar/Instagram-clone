<?php

  $str =
  "##coldplay and the #pople of india
second line
third #ldap_first_reference
#fourth line";

  // include '../config/class/hashtag.class.php';
  // $hashtag = new hashtag;
  //
  // echo $hashtag->lineBreakHashtag($str, "post");

  $array = preg_split('/(\r\n|\r|\n)/', $str);
  $fir_arr = preg_split('/(\r\n|\r|\n| )/', $str);
  $sec_arr = array();
  $th_arr = array();
  // echo "<pre>", print_r($array), "</pre>";

  foreach ($array as $line) {
    $word = explode(' ', $line);
    array_push($sec_arr, $word[0]);
  }

  foreach ($sec_arr as $each) {
    if($each[0] == "#"){

      $two = substr($each, 1);
      $t = preg_replace("#[\#]#", "", $two);

      if($t != ""){
        array_push($th_arr, "<a class='hashtag' href='/faiyaz/Instagram/hashtag?tag={$t}'>{$each}</a> ");
      } else {
        array_push($th_arr, $each." ");
      }

    } else {
      array_push($th_arr, $each." ");
    }

  }

  // echo "<pre>", var_dump($sec_arr), "</pre>";
  // echo "<pre>", var_dump($th_arr), "</pre>";
  // echo "<pre>", var_dump($fir_arr), "</pre>";

  $final = preg_replace($fir_arr, $sec_arr, $th_arr);

  echo "<pre>", var_dump($fir_arr), "</pre>";
  echo "<pre>", var_dump($final), "</pre>";

?>

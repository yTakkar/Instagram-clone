<?php
  try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=instagram;charset=utf8mb4', 'root', 'iamaprogrammer');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

?>

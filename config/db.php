<?php
  try {
    $db = new PDO('mysql:host=host;dbname=instagram;charset=utf8mb4', 'root', 'user');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }

?>

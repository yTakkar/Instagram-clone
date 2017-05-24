<?php
  include '../../config/declare.php';
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include '../../config/classesGetter.php';
    $group = new group;

    if (isset($_GET['create_group'])) {
      $x = $group->create_group($_GET['create_group'], $_GET['grp_bio']);
      $x;
      $array = array('mssg' => $x);
      echo json_encode($array);
    }

    if (isset($_GET['joinGrp'])) {
      $group->joinGrp($_GET['joinGrp']);
    }

    if (isset($_GET['leaveGrp'])) {
      $group->leaveGrp($_GET['leaveGrp']);
    }

    if (isset($_GET['getGrpMem'])) {
      $group->getGrpMembers($_GET['getGrpMem'], $_GET['getGrpGrp']);
    }

    if (isset($_GET['grpAddMem'])) {
      $group->addGrpMembers($_GET['grpAddMem'], $_GET['grpAdd']);
    }

    if (isset($_POST['editname'])) {
      $group->editGrp($_POST['editname'], $_POST['editbio'], $_POST['editpri'], $_POST['editGrp']);
      $array = array(
        'name' => $group->GETgrp($_POST['editGrp'], "grp_name"),
        "bio" => $group->GETgrp($_POST['editGrp'], "grp_bio"),
        "pri" => $group->GETgrp($_POST['editGrp'], "grp_privacy"),
      );
      echo json_encode($array);
    }

    if (isset($_GET['dltGrp'])) {
      $group->dltGrp($_GET['dltGrp']);
    }

    if (isset($_GET['remMem'])) {
      $group->removeMember($_GET['remMem'], $_GET['remG']);
    }

    if (isset($_POST['inviteToGrp'])) {
      $group->selectToInvite($_POST['inviteToGrp']);
    }

    if (isset($_POST['inviteTo'])) {
      $to = preg_replace("#[^0-9]#i", "", $_POST['inviteTo']);
      $post = preg_replace("#[^0-9]#i", "", $_POST['inviteGrp']);

      $s = $group->inviteGrp($to, $post);
      $s;

      $array = array('s' => $s);
      echo json_encode($array);
    }

    if (isset($_GET['grpFeeds'])) {
      $group->getGrpPost($_GET['grpGrp'], "ajax", $_GET['grpFeeds']);
    }

    if (isset($_GET['selectForGrpAdmin'])) {
      $group->selectForGrpAdmin($_GET['selectForGrpAdmin']);
    }

    if (isset($_GET['cgaUser'])) {
      $i = $group->changeGrpAdmin($_GET['cgaUser'], $_GET['cgaGrp']);
      $i;
      $array = array("mssg" => $i);
      echo json_encode($array);
    }

    if (isset($_GET['grpMFeeds'])) {
      $group->grpMembers($_GET['grpMGrp'], "ajax", $_GET['grpMFeeds']);
    }

  }
?>

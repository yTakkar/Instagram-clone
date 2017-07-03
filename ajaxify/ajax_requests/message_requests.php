<?php
  session_start();
  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {

    include_once '../../config/class/needy_class.php';
    include '../../config/class/message.class.php';
    $message = new message;

    if (isset($_GET['getPeople'])) {
      $message->getPeople($_GET['getPeople']);
    }

    if (isset($_GET['mssgViaBtn'])) {
      $message->mssgViaBtn($_GET['mssgViaBtn'], $_GET['viaTo'], $_GET['cname']);
    }

    if (isset($_GET['selectC'])) {
      $message->getMessages($_GET['selectC'], $_GET['user']);
    }

    if (isset($_POST['messageText'])) {
      if ($_POST['mssgOf'] == "user") {
        $tmt = $_POST['mssgTo'];
      } else if($_POST['mssgOf'] == "group") {
        $tmt = "";
      }
      $message->sendMessageText($_POST['messageText'], $tmt, $_POST['mssgCon'], $_POST['mssgOf']);
    }

    if (isset($_FILES['mssgImage'])) {
      if ($_POST['conImgBy'] == "user") {
        $imt = $_POST['mIto'];
      } else if($_POST['conImgBy'] == "group") {
        $imt = "";
      }
      $m = $message->sendMessageImage($_FILES['mssgImage'], $imt, $_POST['conImg'], $_POST['conImgBy']);
      $m;
      $array = array("m" => $m);
      echo json_encode($array);
    }

    if (isset($_GET['sticker'])) {
      if ($_GET['stickerBy'] == "user") {
        $smt = $_GET['stickerTo'];
      } else if ($_GET['stickerBy'] == "group") {
        $smt = "";
      }
      $sticker = $message->sendMessageSticker($_GET['sticker'], $smt, $_GET['stickerCon'], $_GET['stickerBy']);
      $sticker;
      $array = array("sticker" => $sticker);
      echo json_encode($array);
    }

    if (isset($_GET['getAllUnreadMssg'])) {
      $c = $message->getAllUnreadMssg();
      $c;
      $array = array("count" => $c);
      echo json_encode($array);
    }

    if (isset($_GET['deleteAllMssg'])) {
      $message->deleteAllMssg($_GET['deleteAllMssg'], $_GET['dltAllBy']);
    }

    if (isset($_GET['dlt_con'])) {
      $message->deleteConversation($_GET['dlt_con'], $_GET['dlt_con_by']);
    }

    if (isset($_GET['editValue'])) {
      if ($_GET['editOf'] == "user") {
        $ecm = $_GET['editU'];
      } else if($_GET['editOf'] == "group") {
        $ecm = "";
      }
      $message->editConName($_GET['editValue'], $_GET['editCon'], $ecm, $_GET['editOf']);
    }

    if (isset($_GET['dltmssg'])) {
      $message->deleteMessage($_GET['dltmssg'], $_GET['dltconid'], $_GET['mssgType'], $_GET['dltmssgby']);
    }

    if (isset($_GET['editText'])) {
      $edit = $message->editMessage($_GET['editText'], $_GET['editMssg']);
      $edit;
      $array = array("return", trim($edit));
      echo json_encode($array);
    }

    if (isset($_GET['updateCon'])) {
      $c = $message->conUnreads($_GET['updateCon']);
      $c;
      $array = array("cons" => $c);
      echo json_encode($array);
    }

    if (isset($_GET['grpUpdateCon'])) {
      $gc = $message->GrpConUnreads($_GET['grpUpdateCon']);
      $gc;
      $array = array("cons" => $gc);
      echo json_encode($array);
    }

    if (isset($_GET['conUpdateCon'])) {
      $uc = $message->conUnreads($_GET['conUpdateCon']);
      $uc;
      $array = array("uC" => $uc);
      echo json_encode($array);
    }

    if (isset($_GET['conUpdateGrpCon'])) {
      $uc = $message->GrpConUnreads($_GET['conUpdateGrpCon']);
      $uc;
      $array = array("uC" => $uc);
      echo json_encode($array);
    }

    if (isset($_GET['conInfo'])) {
      $message->conInfo($_GET['conInfo']);
    }

    if (isset($_GET['grpConInfo'])) {
      // echo $_GET['grpConInfo'];
      $message->grpConInfo($_GET['grpConInfo']);
    }

    if (isset($_GET['addGrpValue'])) {
      $message->getGrpMembersForAdd($_GET['addGrpValue'], $_GET['except']);
    }

    if (isset($_POST['addGrpName'])) {
      $grp = $message->addGroup($_POST['addGrpName'], $_POST['addGrpMembers'], $_FILES['grpAvatar']);
    }

    if (isset($_GET['selectGrpCon'])) {
      $message->getGrpMessages($_GET['selectGrpCon']);
    }

    if (isset($_GET['leaveGrp'])) {
      $session = $_SESSION['id'];
      $message->leaveGrpCon($_GET['leaveGrp'], $session, "leave");
    }

    if (isset($_GET['removeGrpMem'])) {
      $rem = $message->leaveGrpCon($_GET['removeGrpMem'], $_GET['removeGrpId'], "remove");
      $rem;
      $array = array(
        'membersLeft' => $message->grpMemCount($_GET['removeGrpMem'])
      );
      echo json_encode($array);
    }

    if (isset($_FILES['edit_grp_con_ava'])) {
      $x = $message->changeGrpConAvatar($_FILES['edit_grp_con_ava'], $_POST['edit_grp_con_grp']);
      $x;
      $array = array('grp_av' => $x);
      echo json_encode($array);
    }

    if (isset($_GET['getGrpConMem'])) {
      $message->getGrpConMembers($_GET['getGrpConMem'], $_GET['getGrpConMemGrp']);
    }

    if (isset($_GET['grpConAddMem'])) {
      $message->addGrpConMembers($_GET['grpConAddMem'], $_GET['grpConAdd']);
    }

    if (isset($_GET['selectForGrpConAdmin'])) {
      $message->selectForGrpConAdmin($_GET['selectForGrpConAdmin']);
    }

    if (isset($_GET['cgcaUser'])) {
      $i = $message->changeGrpConAdmin($_GET['cgcaUser'], $_GET['cgcaGrp']);
      $i;
      $array = array("mssg" => $i);
      echo json_encode($array);
    }

  }
?>

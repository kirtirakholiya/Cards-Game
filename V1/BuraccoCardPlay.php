<?php 
include "Include/Connection.php";
include "Include/BuracoUtilityFun.php";

$user_id=$_REQUEST['user_id'];
$room_id =$_REQUEST['room_id'];
$card_code=$_REQUEST['card_code'];
//$type=$_REQUEST['type'];/* 1=> card picked from dack, 2=>card picked from dropped cards */
$cards_available=$_REQUEST['cards_available'];


//if($cards_available == 1){

    $players = getNextPreviousPlayer($conn, $room_id, $user_id);
    if($cards_available == 1){
    // if($type == 1){
        $result1 = $conn->query("INSERT INTO tbl_userdroppedcardcode(room_id,user_id,dropped_card_code,created_date) values('".$room_id."','".$user_id."','".$card_code."','".date ("Y-m-d H:i:s")."')");
    // }else{
        
    //     $result1 = $conn->query("SELECT * FROM `tbl_userdroppedcardcode` WHERE room_id='".$room_id."' AND user_id='".$players[0]."' ORDER BY id DESC limit 1 ");
    //     $row = $result1->fetch_assoc();
    //     $conn->query("delete from tbl_userdroppedcardcode where id='".$row['id']."'");
    
    //     $result1 = $conn->query("INSERT INTO tbl_userdroppedcardcode(room_id,user_id,dropped_card_code,created_date) values('".$room_id."','".$user_id."','".$card_code."','".date ("Y-m-d H:i:s")."')");
    // }

    CalculateUserPoints();
    
    $conn->query("UPDATE tbl_buracojoinroom SET current=0 WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
    $conn->query("UPDATE tbl_buracojoinroom SET current=1 WHERE room_id='".$room_id."' AND user_id='".$players[1]."'");
    }

    $result2 = $conn->query("UPDATE tbl_buracojoinroom SET cards_available='".$cards_available."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
    
    $jsonarr=array('ws_status'=>true,'Message'=>'Success','next_userid'=>$players[1]);        
    echo json_encode($jsonarr);

/*}else{

    $result2 = $conn->query("UPDATE tbl_buracojoinroom SET cards_available='".$cards_available."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");

    $jsonarr=array('ws_status'=>true,'Message'=>'Success - flag is updated');        
    echo json_encode($jsonarr);
}*/


function CalculateUserPoints(){

    global $conn;
    global $user_id;
    global $room_id;

    $query1 = $conn->query("SELECT * FROM tbl_buracosubmittedcards WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
    if($query1->num_rows == 0){
        return;
    }

    $cardsDataArray = array();
    $currentRoundCardPoints = 0;

    $query2 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
    $userData = $query2->fetch_assoc();
    $rulesFlag = $userData['rules_flag'];
    $currentUserPoints = $userData['points'];

    while($row = $query1->fetch_assoc()){

        $currentRoundCardPoints += $row['points'];
        array_push($cardsDataArray, $row);
    }


    if($rulesFlag == 1){

        if($currentRoundCardPoints >= 100){
            $currentUserPoints += $currentRoundCardPoints;

            foreach ($cardsDataArray as $value) {    
                $conn->query("INSERT INTO tbl_buracouserpoints (room_id, user_id, cards_code, points, created_date, card_type) VALUES ('".$room_id."', '".$user_id."', '".$value['cards_code']."', '".$value['points']."', '".date ("Y-m-d H:i:s")."','".$value['card_type']."')");
            }

            $query3 = $conn->query("SELECT GROUP_CONCAT(team1) AS team1, GROUP_CONCAT(team2) AS team2 FROM tbl_buracoteam WHERE room_id='".$room_id."' GROUP BY room_id");
            $teamIds = $query3->fetch_assoc();
    
            $team1_userId = $teamIds['team1'];
            $team2_userId = $teamIds['team2'];
    
            if(strpos($team1_userId, strval($user_id)) !== false){
    
                $conn->query("UPDATE tbl_buracojoinroom SET rules_flag=0 WHERE room_id='".$room_id."' AND user_id IN($team1_userId)");
    
            }elseif(strpos($team2_userId, strval($user_id)) !== false){
    
                $conn->query("UPDATE tbl_buracojoinroom SET rules_flag=0 WHERE room_id='".$room_id."' AND user_id IN($team2_userId)");
            }

        }else{    
            $currentUserPoints -= $currentRoundCardPoints;
        }
    }else{
        $currentUserPoints += $currentRoundCardPoints;

        foreach ($cardsDataArray as $value) {    
            $conn->query("INSERT INTO tbl_buracouserpoints (room_id, user_id, cards_code, points, created_date, card_type) VALUES ('".$room_id."', '".$user_id."', '".$value['cards_code']."', '".$value['points']."', '".date ("Y-m-d H:i:s")."','".$value['card_type']."')");
        }
    }

    $conn->query("DELETE FROM tbl_buracosubmittedcards WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
    $conn->query("UPDATE tbl_buracojoinroom SET points='".$currentUserPoints."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
}
?>

<?php
include "Include/Connection.php";
include "Include/BuracoUtilityFun.php";

$room_id = $_REQUEST['room_id'];
$team1 = explode(',',$_REQUEST['team1']);
$team2 = explode(',',$_REQUEST['team2']);
$type = $_REQUEST['type']; /*1=>select,2=>random */


if($type==1){

    if(count($team1) >0 && count($team1) < 4 && count($team2) >0 && count($team2) < 4){
        foreach($team1 as $k=> $val){
            $query = mysqli_query($conn,"INSERT INTO tbl_buracoteam(room_id,team1,team2,created_date) values('".$room_id."','".$val."','".$team2[$k]."','".date ("Y-m-d H:i:s")."')");
        }

        $teamA_Plyers = implode(',', $team1);
        $teamB_Plyers = implode(',', $team2);

        $result1 = mysqli_query($conn,"SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");
        $user_id = ($result1->fetch_assoc())['br_user_id'];

        $joinedUsers = GetJoinedUsersId($conn, $room_id, $user_id);

        $jsonarr=array('ws_status'=>true,'Message'=>'Team has been inserted successfully','Team1'=>$teamA_Plyers,'Team2'=>$teamB_Plyers,'usersequencedata'=>$joinedUsers);        
        echo json_encode($jsonarr);
    }else{
        $jsonarr=array('ws_status'=>false,'Message'=>'Team has atleast one user');        
        echo json_encode($jsonarr);
    }
}else{
    if(count($team1) >0 && count($team1) < 6){
    shuffle($team1);
    $cnt= count($team1) / 2;
    $teamA = $teamB =array();
    for($index = 0; $index < $cnt; $index++){
        $teamA[] =$team1[$index];
        $teamB[] =$team1[$index+$cnt];
        $query = mysqli_query($conn,"INSERT INTO tbl_buracoteam(room_id,team1,team2,created_date) values('".$room_id."','".$team1[$index]."','".$team1[$index + $cnt]."','".date ("Y-m-d H:i:s")."')");
    }

    $teamA_Plyers = implode(',', $teamA);
    $teamB_Plyers = implode(',', $teamB);

    $result1 = mysqli_query($conn,"SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");
    $user_id = ($result1->fetch_assoc())['br_user_id'];

    $joinedUsers = GetJoinedUsersId($conn, $room_id, $user_id);

    $jsonarr=array('ws_status'=>true,'Message'=>'Team has been inserted successfully','Team1'=>$teamA_Plyers,'Team2'=>$teamB_Plyers,'usersequencedata'=>$joinedUsers);        
     echo json_encode($jsonarr);  
    }else{
        $jsonarr=array('ws_status'=>false,'Message'=>'Team has atleast one user');        
        echo json_encode($jsonarr);
    }
}
?>
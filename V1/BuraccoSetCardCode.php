<?php   


include "Include/Connection.php";

@$room_id=$_REQUEST['room_id'];
@$buraco1 = $_REQUEST['buraco1'];
@$buraco2 = $_REQUEST['buraco2'];
@$cardRedistributeFlag = $_REQUEST['flag'];
@$firstDefaultCardDropped = $_REQUEST['firstdefaultdropcard'];

$data=array();
response(addData($room_id,$buraco1,$buraco2,$firstDefaultCardDropped));

function response($data){
    
    echo json_encode($data);
}

function updateRulesFlag($room_id){

    global $conn;
    
    $team1Score = 0;
    $team2Score = 0;

    $query1 = $conn->query("SELECT GROUP_CONCAT(team1) AS team1, GROUP_CONCAT(team2) AS team2 FROM tbl_buracoteam WHERE room_id='".$room_id."' GROUP BY room_id");
    $teamIds = $query1->fetch_assoc();

    $team1_userId = $teamIds['team1'];
    $team2_userId = $teamIds['team2'];

    $query2 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team1_userId)");
    $team1Score = ($query2->fetch_assoc())['score'];
    $query3 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team2_userId)");
    $team2Score = ($query3->fetch_assoc())['score'];

    $query4 = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");
    $bid = ($query4->fetch_assoc())['br_bid'];

    if($team1Score >= $bid/2){

        $conn->query("UPDATE tbl_buracojoinroom SET rules_flag=1 WHERE room_id='".$room_id."' AND user_id IN($team1_userId)");
    }

    if($team2Score >= $bid/2){

        $conn->query("UPDATE tbl_buracojoinroom SET rules_flag=1 WHERE room_id='".$room_id."' AND user_id IN($team2_userId)");
    }
}


function addData($room_id,$buraco1,$buraco2,$firstDefaultCardDropped){
    
    global $conn;
    
    try {
       
        $query = $conn->query("update tbl_buroom set buraco1='".$buraco1."',buraco2='".$buraco2."',firstpageofdack='".$firstDefaultCardDropped."' where br_id='".$room_id."'");

        if($cardRedistributeFlag == 1){

            updateRulesFlag($room_id);
        }
       
        $data['msg']="Success";
        $data['result']=1;
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
}
?>
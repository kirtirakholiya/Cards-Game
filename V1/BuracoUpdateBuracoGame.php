<?php   
include "Include/Connection.php";


@$room_id=$_REQUEST['room_id'];
@$user_id=$_REQUEST['user_id'];
@$cards_code = explode(',',$_REQUEST['cards_code']);

$data=array();
response(addData($room_id,$user_id,$cards_code));

function CalculatePoints($cards){

    $card_points = array('2'=>10,'3'=>5,'4'=>5,'5'=>5,'6'=>5,'7'=>5,'8'=>10,'9'=>10, '0'=>10,'J'=>10,'Q'=>10,'K'=>10,'A'=>15,'X'=>10);
    $points = 0;

    $skipCard1 = 'X';
    $skipCard2 = '2';

    foreach ($cards as $val) {

        if((substr($val, 0, -1) != $skipCard1) && (substr($val, 0, -1) != $skipCard2)){
            $points += $card_points[substr($val, 0, -1)];
        }
    }

    return $points;
}

function response($data){
    
    echo json_encode($data);
}

function addData($room_id,$user_id,$cards_code){
    
    global $conn;
    
    try {
        $query = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");

        if($query->num_rows > 0){

            $cardPoint = CalculatePoints($cards_code);

            $query1 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
            $currentUserPoints = ($query1->fetch_assoc())['points'];

            $currentUserPoints -= $cardPoint;

            $conn->query("UPDATE tbl_buracojoinroom SET points='".$currentUserPoints."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
            $conn->query("UPDATE tbl_buroom SET buraco_cnt=2 WHERE br_id='".$room_id."'");

            $query3 = $conn->query("SELECT GROUP_CONCAT(team1) AS team1, GROUP_CONCAT(team2) AS team2 FROM tbl_buracoteam WHERE room_id='".$room_id."' GROUP BY room_id");
            $teamIds = $query3->fetch_assoc();
        
            $team1_userId = $teamIds['team1'];
            $team2_userId = $teamIds['team2'];
        
            $query4 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team1_userId.)");
            $score_team1 = ($query4->fetch_assoc())['score'];

            $query5 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team2_userId)");
            $score_team2 = ($query5->fetch_assoc())['score'];

            $result = mysqli_query($conn,"Select * from tbl_buroom where br_id='".$room_id."' and status=1");
            $bidvalue = ($result->fetch_assoc())['br_bid'];

            $data['msg']="Success";
            $data['team1']=$score_team1;
            $data['team2']=$score_team2;
            $data['bidValue']=$bidvalue;
            $data['result']=1;

        }else{

            $data['msg']="No room found";
            $data['result']=0;
        }
        
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
}
?>
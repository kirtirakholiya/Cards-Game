<?php   
include "Include/Connection.php";

@$room_Id =$_REQUEST['room_id'];

$data=array();
response(addData($room_Id));

function response($data){
    
    echo json_encode($data);
}

function GetWinnerUserIDs($room_id){
    global $conn;

    $winnerIds = array();
    $query2 = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");

    $bid = ($query2->fetch_assoc())['br_bid'];

    $query3 = $conn->query("SELECT GROUP_CONCAT(team1) AS team1, GROUP_CONCAT(team2) AS team2 FROM tbl_buracoteam WHERE room_id='".$room_id."' GROUP BY room_id");
    $teamIds = $query3->fetch_assoc();

    $team1_userId = $teamIds['team1'];
    $team2_userId = $teamIds['team2'];

    $query4 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN(".$team1_userId.")");
    $team1_Score = ($query4->fetch_assoc())['score'];

    $query5 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN(".$team2_userId.")");
    $team2_Score = ($query5->fetch_assoc())['score'];

    if(($team1_Score >= $bid) && ($team2_Score < $bid)){

        $winnerIds = explode(',', $team1_userId);
    }elseif(($team2_Score >= $bid) && ($team1_Score < $bid)){

        $winnerIds = explode(',', $team2_userId);
    }

    return $winnerIds;
}

function addData($room_Id){
    
    global $conn;
    try{

        $winneruserids = GetWinnerUserIDs($room_Id);
    
        $query1 = $conn->query("SELECT u.user_id,u.user_name,u.Img FROM tbl_buracojoinroom j left join tbl_registration u on u.user_id=j.user_id WHERE j.room_id='".$room_Id."'");
        $dataarray = array();

        while($row = $query1->fetch_assoc()){

            $query6 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_Id."' AND user_id='".$row['user_id']."'");
            $score = ($query6->fetch_assoc())['points'];

            $row['score'] = ($score != '') ? $score : '0' ;

            if(COUNT($winneruserids) != 0){

                if(in_array($row['user_id'],$winneruserids)){
                    $row['winning_status'] ='win';
                }else{
                    $row['winning_status'] ='lose';
                }
            }else{

                $row['winning_status'] ='tie';
            }

            $dataarray[] = $row;
        }

        $data['data']= $dataarray;
        $data['msg']="Score Summary";
        $data['result']=1;
        return $data;
  
    }catch (\Throwable $th){
        $data[]=array("Message"=>"Failed");
    }
}

?>
<?php   

include "Include/Connection.php";
include "Include/BuracoUtilityFun.php";

@$user_id=$_REQUEST['user_id'];
@$room_Id =$_REQUEST['room_id'];

$data=array();
response(addData($user_id,$room_Id));

function response($data){
    echo json_encode($data);
}

function GetAllUserPairOrSequence($room_Id){

    global $conn;
    $finalarray = array();
    $query2 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_Id."'");

    $index = 1;
    while($row = $query2->fetch_assoc()){

        $userCardData = $pairArray = $sequenceArray = array();

        $querypoints = $conn->query("SELECT * FROM tbl_buracouserpoints WHERE room_id='".$room_Id."' and user_id='".$row['user_id']."'");
        
        while($row12 = $querypoints->fetch_assoc()){
            
            if($row12['card_type'] == 'pair'){
                array_push($pairArray, $row12['cards_code']);

            }else if($row12['card_type'] == 'sequence'){
                array_push($sequenceArray, $row12['cards_code']);
            }
        }

        $queryUserInfo = $conn->query("SELECT * FROM tbl_registration WHERE user_id='".$row['user_id']."'");

        if($userInfo = $queryUserInfo->fetch_assoc()){
            $userCardData['id'] = $row['user_id'];
            $userCardData['name'] = $userInfo['user_name'];
            $userCardData['profile'] = ($userInfo['Img']) ? IMG_URL.$userInfo['Img'] : 'null';
        }
        $userCardData['Pair'] = $pairArray;
        $userCardData['Sequence'] = $sequenceArray;

        $userKey = "User".$index;
        ++$index;

        $finalarray[$userKey] = $userCardData;
    }
    
    return $finalarray;
}

function addData($user_id,$room_Id){
    
    global $conn;
    try{

        $query1 = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_Id."'");

        if($query1->num_rows > 0){

            $buraco_cnt = ($query1->fetch_assoc())['buraco_cnt'];

            $query2 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_Id."' AND current=1");
            $nextuserid = ($query2->fetch_assoc())['user_id'];

            $query3 = $conn->query("SELECT GROUP_CONCAT(team1) AS team1, GROUP_CONCAT(team2) AS team2 FROM tbl_buracoteam WHERE room_id='".$room_Id."' GROUP BY room_id");
            $teamIds = $query3->fetch_assoc();
        
            $team1_userId = $teamIds['team1'];
            $team2_userId = $teamIds['team2'];
        
            $query4 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_Id."' AND user_id IN($team1_userId.)");
            $score_team1 = ($query4->fetch_assoc())['score'];
        
            $query5 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_Id."' AND user_id IN($team2_userId)");
            $score_team2 = ($query5->fetch_assoc())['score'];

            $query6 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_Id."' AND cards_available=0");

            $players = getNextPreviousPlayer($conn, $room_Id, $nextuserid);

            $query7 = $conn->query("SELECT * FROM `tbl_userdroppedcardcode` WHERE room_id='".$room_Id."' AND user_id='".$players[0]."' ORDER BY id DESC limit 1 ");
            $row = $query7->fetch_assoc();

            $query8 = $conn->query("SELECT GROUP_CONCAT(dropped_card_code) AS dropped_cards FROM tbl_userdroppedcardcode WHERE room_id='".$room_Id."'");

            $maxbidqry = $conn->query("Select * from tbl_buroom Where br_id='".$room_Id."'"); 
            $maxBid = $maxbidqry->fetch_assoc();

            if($query8->num_rows > 0){
                $droppcarsarray = explode(",",($query8->fetch_assoc())['dropped_cards']);
            }

            if($maxBid['firstpageofdack'] != '') {
                array_unshift($droppcarsarray,$maxBid['firstpageofdack']);
            }

            $data['maxBid']=  ($maxBid['br_bid'] != '') ? $maxBid['br_bid'] : 0;
            $data['Buraco_cnt']=$buraco_cnt;
            $data['NextUserId']=$nextuserid;
            $data['Score_Team1']=$score_team1;
            $data['Score_Team2']=$score_team2;
            $data['cards_available']=($query6->num_rows > 0) ? 0 : 1;
            $data['PreUserCardCode']=($query7->num_rows > 0) ? $row['dropped_card_code'] : '-';
            $data['AllDroppedCards']=(count($droppcarsarray) > 0) ? $droppcarsarray : array();
            $data['msg']="Success";
            $data['result']=1;
            $data['UserCardData']=GetAllUserPairOrSequence($room_Id);

        }else{

            $data['msg']="No room found";
            $data['result']=0;
        }

        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed", "result"=>0);
    }    
       
}

?>
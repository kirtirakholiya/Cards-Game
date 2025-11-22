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

    $query1 = $conn->query("SELECT * FROM tbl_bid WHERE room_id='".$room_id."'");
    $biddata = $query1->fetch_assoc();

    $highestBidUserId = $biddata['user_id'];
    $partnerCard1 = $biddata['partner_card1'];
    $partnerCard2 = $biddata['partner_card2'];

    $query2 = $conn->query("SELECT * FROM tbl_bid_user WHERE room_id='".$room_id."' AND user_id='".$highestBidUserId."'");

    $highestBid = ($query2->fetch_assoc())['bid'];

    $users1Array = array();
    $users1Array[] = $highestBidUserId;
    $query3 = $conn->query("SELECT * FROM `tbl_bqcardplay` WHERE room_id='".$room_id."' AND (card_code='".$partnerCard1."' OR card_code='".$partnerCard2."')");

    while($row = $query3->fetch_assoc()){
        $users1Array[] = $row['user_id'];
    }
    $team1userids = implode(',',$users1Array);
   

    $query5 = $conn->query("SELECT group_concat(user_id) as team2ids FROM tbl_joinroom WHERE room_id='".$room_id."' and user_id NOT IN($team1userids)");
    $team2userids = ($query5->fetch_assoc())['team2ids'];
    $users2Array = explode(',',$team2userids);

    $query4 = $conn->query("SELECT SUM(points) as score FROM `tbl_roundwinner` WHERE room_id='".$room_id."' and user_id IN($team1userids)");
    $team1scrore = ($query4->fetch_assoc())['score'];

    if($team1scrore >= $highestBid){

        // foreach ($users1Array as $value) {

        //     $query6 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND user_id='".$value."'");
        //     $userFinalScore = ($query6->fetch_assoc())['final_score'];

        //     $userFinalScore += $highestBid;
        //     $conn->query("UPDATE tbl_joinroom SET final_score='".$userFinalScore."' WHERE room_id='".$room_id."' AND user_id='".$value."'");
        // }

        return $users1Array;
    }else{

        // foreach ($users1Array as $value) {

        //     $query6 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND user_id='".$value."'");
        //     $userFinalScore = ($query6->fetch_assoc())['final_score'];

        //     $userFinalScore -= $highestBid;
        //     $conn->query("UPDATE tbl_joinroom SET final_score='".$userFinalScore."' WHERE room_id='".$room_id."' AND user_id='".$value."'");
        // }

        return $users2Array;
    }
 
}

function addData($room_Id){
    
    global $conn;
    try{

        $winneruserids = GetWinnerUserIDs($room_Id);

        // print_r($winneruserids);
        // die;
    
        $query1 = $conn->query("SELECT u.user_id,u.user_name,u.Img FROM tbl_joinroom j left join tbl_registration u on u.user_id=j.user_id WHERE j.room_id='".$room_Id."'");
        $dataarray = array();
        while($row = $query1->fetch_assoc()){

            $scquery = $conn->query("SELECT SUM(points) as score FROM `tbl_roundwinner` WHERE room_id='".$room_Id."' and user_id='".$row['user_id']."'");
            $score = ($scquery->fetch_assoc())['score'];
            $row['score'] = ($score != '') ? $score : '0' ;
            if(in_array($row['user_id'],$winneruserids)){
                $row['winning_status'] ='True';
            }else{
                $row['winning_status'] ='false';
            }

            $query2 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_Id."' AND user_id='".$row['user_id']."'");
            $row['FinalScore'] = ($query2->fetch_assoc())['final_score'];

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
<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_Id =$_REQUEST['room_id'];
@$card_code=$_REQUEST['card_code'];

$data=array();
response(addData($user_id,$card_code,$room_Id));

function response($data){
    echo json_encode($data);
}

function IsSameColourCards($cards){

    $ret = TRUE;
    $cardType = substr($cards[0], -1);

    foreach($cards as $val){
    
    	if(substr($val, -1) != $cardType){
        	$ret = FALSE;
            break;
        }
    }

    return $ret;
}

function GetMaxCard($cards, $cardType){

    $card_value = array('2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6, '7'=>7, '8'=>8, '9'=>9, '0'=>10, 'J'=>11, 'Q'=>12, 'K'=>13, 'A'=>14);

    $max = 0;
    $ret = '-1';

    foreach($cards as $val){

    	if((substr($val, -1) == $cardType) && ($max < $card_value[substr($val, 0, -1)])){
				$max = $card_value[substr($val, 0, -1)];
				$ret = $val;
        }
    }

    return $ret;
}

/*************************************************************************
 * 5 points for '5S' '5D' '5H' '5C' cards.
 * 10 points for '0S' '0D' '0H' '0C' cards.
 * 15 points for 'AS' 'AD' 'AH' 'AC' cards.
 * 30 points for BQ(QS)
*************************************************************************/
function CalculatePoints($cards){

    $card_points = array('2'=>0,'3'=>0,'4'=>0,'5'=>5,'6'=>0,'7'=>0,'8'=>0,'9'=>0, '0'=>10,'J'=>0,'Q'=>0,'K'=>0, 'A'=>15);
    $points = 0;

    if(in_array('QS', $cards, TRUE)){
        $points = 30;
        unset($cards[array_search('QS', $cards)]);
    }

    foreach ($cards as $val) {
        $points += $card_points[substr($val, 0, -1)];
    }

    return $points;
}

function UpdateUserFinalScore($room_id){
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

    $query4 = $conn->query("SELECT SUM(points) as score FROM `tbl_roundwinner` WHERE room_id='".$room_id."' and user_id IN($team1userids)");
    $team1scrore = ($query4->fetch_assoc())['score'];

    if($team1scrore >= $highestBid){

        foreach ($users1Array as $value) {

            $query6 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND user_id='".$value."'");
            $userFinalScore = ($query6->fetch_assoc())['final_score'];

            $userFinalScore += $highestBid;
            $conn->query("UPDATE tbl_joinroom SET final_score='".$userFinalScore."' WHERE room_id='".$room_id."' AND user_id='".$value."'");
        }

    }else{

        foreach ($users1Array as $value) {

            $query6 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND user_id='".$value."'");
            $userFinalScore = ($query6->fetch_assoc())['final_score'];

            $userFinalScore -= $highestBid;
            $conn->query("UPDATE tbl_joinroom SET final_score='".$userFinalScore."' WHERE room_id='".$room_id."' AND user_id='".$value."'");
        }
    }
}

function addData($user_id,$card_code,$room_Id){
    
    global $conn;
    try {
    $card_cnt;
    $round_cnt;

    $rslt = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_Id."' AND cardplay_current=1");

    $currentUserId = ($rslt->fetch_assoc())['user_id'];

    if($currentUserId == $user_id){
        $query = $conn->query("SELECT * FROM tbl_room WHERE room_id='".$room_Id."'");

        $row = $query->fetch_assoc();
        $card_cnt = $row["card_cnt"];
        $round_cnt = $row["round_cnt"];

        $query1 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_Id."'");

        $player_cnt = $query1->num_rows;

        $card_cnt++;
        $round_cnt++;

        if($card_cnt == 1){
            $conn->query("UPDATE tbl_room SET roundfirstcard_type='".substr($card_code, -1)."' WHERE room_id='".$room_Id."'");
        }

        $query2 = $conn->query("INSERT INTO tbl_bqcardplay(round_cnt,room_id,user_id,card_code,created_date) VALUES('".$round_cnt."','".$room_Id."','".$user_id."','".$card_code."','".date ("Y-m-d H:i:s")."')");
        $query3 = $conn->query("UPDATE tbl_room SET card_cnt='".$card_cnt."' WHERE room_id='".$room_Id."'");

        $query8 = $conn->query("SELECT user_id AS next_user FROM tbl_joinroom WHERE room_id='".$room_Id."' AND joinedOn > (SELECT joinedOn FROM tbl_joinroom WHERE room_id='".$room_Id."' AND user_id='".$user_id."' AND cardplay_current=1) ORDER BY joinedOn LIMIT 1");

        if($query8->num_rows == 1){
            $row = $query8->fetch_assoc();
            $query9 = $conn->query("UPDATE tbl_joinroom SET cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$row['next_user']."'");
        }else{
            $query10 = $conn->query("SELECT user_id AS next_user FROM tbl_joinroom WHERE room_id='".$room_Id."' ORDER BY joinedOn ASC LIMIT 1");
            $row = $query10->fetch_assoc();
            $query11 = $conn->query("UPDATE tbl_joinroom SET cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$row['next_user']."'");
        }

        $query12 = $conn->query("UPDATE tbl_joinroom SET cardplay_current=0 WHERE room_id='".$room_Id."' AND user_id='".$user_id."'");

        if($card_cnt == $player_cnt){

            $userCardArray = array();

            $query4 = $conn->query("UPDATE tbl_room SET round_cnt='".$round_cnt."', card_cnt=0, roundfirstcard_type='' WHERE room_id='".$room_Id."'");
            $query5 = $conn->query("SELECT * FROM tbl_bqcardplay WHERE room_id='".$room_Id."' AND round_cnt='".$round_cnt."' ORDER BY cardplay_id");

            while($row = $query5->fetch_assoc()){
                $userCardArray[$row["user_id"]] = $row["card_code"];
            }

            $query6 = $conn->query("SELECT * FROM tbl_bid WHERE room_id='".$room_Id."'");

            $trump = ($query6->fetch_assoc())["trump"];
            $maxCardCode;
            $cardsCode = array_values($userCardArray);

            if(IsSameColourCards($cardsCode) == TRUE){
                $maxCardCode = GetMaxCard($cardsCode, substr($cardsCode[0], -1));
            }else{
                $ret = GetMaxCard($cardsCode, $trump);

                if($ret !== '-1'){
                    $maxCardCode = $ret;
                }else{
                    $maxCardCode = GetMaxCard($cardsCode, substr($cardsCode[0], -1));
                }
            }

            $currRoundPoint = CalculatePoints($cardsCode);
            $roundWinnerUserId = array_search($maxCardCode, $userCardArray);
            $cards = implode(',',$cardsCode);

            $conn->query("INSERT INTO tbl_roundwinner(round_cnt,room_id,user_id,cards_code,points,created_date) VALUES('".$round_cnt."','".$room_Id."','".$roundWinnerUserId."','".$cards."','".$currRoundPoint."','".date ("Y-m-d H:i:s")."')");
            $conn->query("UPDATE tbl_joinroom SET cardplay_current=0 WHERE room_id='".$room_Id."'");
            $conn->query("UPDATE tbl_joinroom SET cardplay_current=1 WHERE room_id='".$room_Id."' and user_id='".$roundWinnerUserId."'");

            if((($player_cnt == 4 || $player_cnt == 5 ) && ($round_cnt == 10)) ||
               ($player_cnt == 6 && ($round_cnt == 8)) ||
               ($player_cnt == 7 && ($round_cnt == 7))){

                UpdateUserFinalScore($room_Id);
            }
        }


        $cardqry = $conn->query("SELECT GROUP_CONCAT(card_code) as cards_code FROM `tbl_bqcardplay` WHERE room_id='".$room_Id."' AND round_cnt='".$round_cnt."' group by room_id,round_cnt"); 
        
        $cardArray = array();
        if($cardqry->num_rows > 0){
            $cardDetail = $cardqry->fetch_assoc();
            $cardArray = explode(',',$cardDetail['cards_code']);
        }
        
        $data['msg']="Card Played";
        $data['droppedCardCode']=$cardArray;
        $data['result']=1;
        return $data;
    }else{
        $data['msg']="Not your turn";
        $data['result']=0;
        return $data;
    }
} catch (\Throwable $th) {
    $data[]=array("Message"=>"Failed");
}
       
}

?>
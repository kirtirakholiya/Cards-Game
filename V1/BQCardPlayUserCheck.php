<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_Id =$_REQUEST['room_id'];


$data=array();
response(addData($user_id,$room_Id));

function response($data){
    echo json_encode($data);
}

function addData($user_id,$room_Id){
    
    global $conn;
    try{
        $sirqry = $conn->query("Select * from tbl_bid Where room_id='".$room_Id."'"); 
        $sirResult = $sirqry->fetch_assoc();

        $query1 = $conn->query("SELECT user_id as NextUserId FROM tbl_joinroom where room_id='".$room_Id."' AND cardplay_current=1  LIMIT 1");   
        $xyz1 = $query1->fetch_assoc();
        $vlId15 = $xyz1['NextUserId'];

        $query2 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_Id."'");
        $query3 = $conn->query("SELECT * FROM tbl_room WHERE room_id='".$room_Id."'");
        $query4 = $conn->query("SELECT * FROM tbl_room WHERE room_id='".$room_Id."'");

        $player_cnt = $query2->num_rows;
        $round_cnt = ($query3->fetch_assoc())["round_cnt"];
        $roundFirstCardType = ($query4->fetch_assoc())["roundfirstcard_type"];

        if(($player_cnt == 4 || $player_cnt == 5 ) && ($round_cnt == 10)){
            $nextuserid = '-';    
        }elseif($player_cnt == 6 && ($round_cnt == 8)){
            $nextuserid = '-';
        }elseif($player_cnt == 7 && ($round_cnt == 7)){
            $nextuserid = '-';
        }else{
            $nextuserid = $vlId15;
        }

        $previousCardCode = '-';

        $query10 = $conn->query("SELECT * FROM tbl_bqcardplay WHERE room_id='".$room_Id."' ORDER BY created_date DESC LIMIT 1");

        if($query10->num_rows == 1){
            $previousCardCode = ($query10->fetch_assoc())["card_code"];
        }

        $roomqry = $conn->query("SELECT * FROM `tbl_room` WHERE room_id='".$room_Id."'"); 
        $roomDetail = $roomqry->fetch_assoc();
        $roundCnt = $roomDetail['round_cnt'] + 1;

        $cardqry = $conn->query("SELECT GROUP_CONCAT(card_code) as cards_code FROM `tbl_bqcardplay` WHERE room_id='".$room_Id."' AND round_cnt='".$roundCnt."' group by room_id,round_cnt");
        $preCardqry = $conn->query("SELECT GROUP_CONCAT(card_code) as cards_code FROM `tbl_bqcardplay` WHERE room_id='".$room_Id."' AND round_cnt='".$roomDetail['round_cnt']."' group by room_id,round_cnt");

        $cardArray = array();
        $preCardArray = array();

        if($cardqry->num_rows > 0){
            $cardDetail = $cardqry->fetch_assoc();
            $cardArray = explode(',',$cardDetail['cards_code']);
        }

        if($preCardqry->num_rows > 0){
            $cardDetail = $preCardqry->fetch_assoc();
            $preCardArray = explode(',',$cardDetail['cards_code']);
        }
       
        
        $data['trump']= ($sirResult != null) ? $sirResult['trump'] : "";
        $data['partner_card1']= ($sirResult != null) ? $sirResult['partner_card1'] : "";
        $data['partner_card2']= ($sirResult != null) ? $sirResult['partner_card2'] : "";
        $data['NextUserId']=$nextuserid;
        $data['previousCardCode']=$previousCardCode;
        $data['RoundFirstCardType']=$roundFirstCardType;
        $data['droppedCardCode']=$cardArray;
        $data['preRoundDroppedCardCode']=$preCardArray;
        $data['msg']="Success";
        $data['result']=1;
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }    
       
}

?>
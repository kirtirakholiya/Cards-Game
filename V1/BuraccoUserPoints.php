<?php
include "Include/Connection.php";
include "Include/BuracoUtilityFun.php";

$room_id = $_REQUEST['room_id'];
$user_id = $_REQUEST['user_id'];
$cards_code = explode(',',$_REQUEST['cards_code']);


$data=array();
response(addData($room_id, $user_id, $cards_code));

function response($data){

    echo json_encode($data);
}


function addData($room_id, $user_id, $cards_code){

    global $conn;

    try {

        $result = mysqli_query($conn,"Select * from tbl_buroom where br_id='".$room_id."' and status=1");
        if(mysqli_num_rows($result)){
        
            $valRet = ValidatePairOrSequence($cards_code);

            $query6 = $conn->query("SELECT * FROM tbl_buracouserpoints WHERE room_id='".$room_id."' AND user_id='".$user_id."'");

            if(($valRet != FALSE) && ($query6->num_rows == 0)) {

                $sts = TRUE;

                $jokerCount = GetCardCount($cards_code, 'X');
                $twoCardCount = GetCardCount($cards_code, '2');

                if(($valRet == PAIR) || ($jokerCount > 0) || ($twoCardCount > 1)){

                    $sts = FALSE;
                } 
                // else {

                //     $cardNum = ConvertCardsIntoNumeric($cards_code);
                //     sort($cardNum);

                //     $cnt = count($cardNum);
                //     $totalSeqCnt = $cardNum[$cnt-1] - $cardNum[0] + 1;
                //     $sum = ($totalSeqCnt * ($cardNum[0] + $cardNum[$cnt-1]))/2;
                //     $arraySum = array_sum($cardNum);

                //     $sts = ($sum == $arraySum) ? TRUE : FALSE;
                // }

                if($sts == FALSE){

                    $data=array('ws_status'=>false,'Message'=>'Input cards should be a sequence without joker','Result'=>3);
                    return $data;
                }
            }
            
            if($valRet == FALSE){
        
                $data=array('ws_status'=>false,'Message'=>'Input cards are not a sequence or pair','Result'=>0);        
            }else{
            
                $cardPoint = CalculatePoints($cards_code);
        
                $query1 = $conn->query("SELECT GROUP_CONCAT(team1) AS team1, GROUP_CONCAT(team2) AS team2 FROM tbl_buracoteam WHERE room_id='".$room_id."' GROUP BY room_id");
                $teamIds = $query1->fetch_assoc();
        
                // $team1_userId = $teamIds['team1'];
                // $team2_userId = $teamIds['team2'];
                
                // $teamScore = 0;
        
                // if(strpos($team1_userId, strval($user_id)) !== false){
        
                //     $query2 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team1_userId)");
                //     $teamScore = ($query2->fetch_assoc())['score'];
        
                // }elseif(strpos($team2_userId, strval($user_id)) !== false){
        
                //     $query2 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team2_userId)");
                //     $teamScore = ($query2->fetch_assoc())['score'];
                // }
        
                // $query3 = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");
                // $bid = ($query3->fetch_assoc())['br_bid'];
        
                // if((($teamScore > $bid/2) && ($cardPoint >=100)) || ($teamScore <= $bid/2)){
        
                    // $query4 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
                    // $currentUserPoints = ($query4->fetch_assoc())['points'];
        
        
                    $hasJokerCard = HasJokerCard($cards_code);
        
                    if(COUNT($cards_code) == 7){
        
                        $hasAll2Card = HasAll2Cards($cards_code);
        
                        if($hasAll2Card == TRUE){
        
                            $cardPoint += ($hasJokerCard == TRUE ? 500 : 1000);
                        }else{
        
                            $cardPoint += ($hasJokerCard == TRUE ? 100 : 200);
                        }
        
                    }elseif(COUNT($cards_code) == 13){
        
                        $cardPoint += ($hasJokerCard == TRUE ? 500 : 1000);
                    }
        
                    // $currentUserPoints += $cardPoint;

                    sort($cards_code);
                    $cards = implode(",",$cards_code);
        
                    // $conn->query("UPDATE tbl_buracojoinroom SET points='".$currentUserPoints."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
                    // $conn->query("INSERT INTO tbl_buracouserpoints (room_id, user_id, cards_code, points, created_date,card_type) VALUES ('".$room_id."', '".$user_id."', '".$cards."', '".$cardPoint."', '".date ("Y-m-d H:i:s")."','".$valRet."')");

                    $conn->query("INSERT INTO tbl_buracosubmittedcards (room_id, user_id, cards_code, points, created_date, card_type) VALUES ('".$room_id."', '".$user_id."', '".$cards."', '".$cardPoint."', '".date ("Y-m-d H:i:s")."','".$valRet."')");
        
                    $maxbidqry = $conn->query("Select * from tbl_buroom Where br_id='".$room_id."'"); 
                    $maxBid = $maxbidqry->fetch_assoc();
        
                    $maxBiddata =  ($maxBid['br_bid'] != '') ? $maxBid['br_bid'] : 0;
        
                    $team1_userId = $teamIds['team1'];
                    $team2_userId = $teamIds['team2'];
                
                    $query4 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team1_userId)");
                    $score_team1 = ($query4->fetch_assoc())['score'];
                
                    $query5 = $conn->query("SELECT SUM(points) as score FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id IN($team2_userId)");
                    $score_team2 = ($query5->fetch_assoc())['score'];
        
                    $data=array('ws_status'=>true,'Message'=>'Success','Result'=>1,'maxBid'=>$maxBiddata,'Score_Team1'=>$score_team1,'Score_Team2'=>$score_team2);        
        
                // }elseif(($teamScore > $bid/2) && ($cardPoint < 100)){
        
                //     $data[]=array('ws_status'=>true,'Message'=>'Points are not added','Result'=>2);        
                // }
            }
          
        }else{
            $data=array('ws_status'=>false,'Message'=>'No room found');        
        }

        return $data;
    } catch (\Throwable $th) {
        $data=array("Message"=>"Failed");
    }
}

?>
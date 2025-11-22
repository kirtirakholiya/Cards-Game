<?php   

include "Include/Connection.php";
include "Include/BuracoUtilityFun.php";

@$room_id=$_REQUEST['room_id'];
@$user_id=$_REQUEST['user_id'];
@$user_data=$_REQUEST['data'];
@$card_code=$_REQUEST['card_code'];

$data=array();
response(addData($room_id, $user_id, $user_data, $card_code));

function response($data){

    echo json_encode($data);
}


function addData($room_id, $user_id, $user_data, $card_code){

    global $conn;
    
    try {

        $query1 = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."' and status=1");

        if($query1->num_rows){

            $query2 = $conn->query("SELECT * FROM tbl_buracouserpoints WHERE room_id='".$room_id."' AND user_id='".$user_id."' AND cards_code='".$user_data."'");

            if($query2->num_rows == 0){
                
                $data[]=array('ws_status'=>false,'Message'=>'Input Sequence or Pair is not found in DB');
                return $data;
            }

            $userPointsTblId = ($query2->fetch_assoc())['id'];

            $updatedCards = explode(",", $user_data);
            $preCardPoint = CalculatePoints($updatedCards);
            array_push($updatedCards, $card_code);

            $valRet = ValidatePairOrSequence($updatedCards);

            sort($updatedCards);
            $cardCodes = implode(",",$updatedCards);

            if($valRet == FALSE){

                $data[]=array('ws_status'=>false,'Message'=>'Updated cards are not a sequence or pair','Result'=>0);
                return $data;
            }

            $cardPoint = CalculatePoints($updatedCards);

            $query4 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
            $currentUserPoints = ($query4->fetch_assoc())['points'];


            $hasJokerCard = HasJokerCard($updatedCards);

            if(COUNT($updatedCards) == 7){

                $hasAll2Card = HasAll2Cards($updatedCards);

                if($hasAll2Card == TRUE){

                    $cardPoint += ($hasJokerCard == TRUE ? 500 : 1000);
                }else{

                    $cardPoint += ($hasJokerCard == TRUE ? 100 : 200);
                }

            }elseif(COUNT($updatedCards) == 13){

                $cardPoint += ($hasJokerCard == TRUE ? 500 : 1000);
            }

            $currentUserPoints = $currentUserPoints + $cardPoint - $preCardPoint;

            $conn->query("UPDATE tbl_buracojoinroom SET points='".$currentUserPoints."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
            $conn->query("UPDATE tbl_buracouserpoints SET points='".$cardPoint."', cards_code='".$cardCodes."' WHERE id='".$userPointsTblId."'");

            $data[]=array('ws_status'=>true,'Message'=>'successfully updated');
            
        }else{
            $data[]=array('ws_status'=>false,'Message'=>'No room found');
        }
         
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
}

?>
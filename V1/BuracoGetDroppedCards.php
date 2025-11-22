<?php   
include "Include/Connection.php";

@$room_id=$_REQUEST['room_id'];
@$user_id=$_REQUEST['user_id'];

$data=array();
response(addData($room_id,$user_id));

function response($data){
    
    echo json_encode($data);
}

function addData($room_id,$user_id){
    
    global $conn;
    
    try {
        $result = $conn->query("Select * from tbl_buroom where br_id='".$room_id."' and status=1");

        if($result->num_rows > 0){
            $query = $conn->query("SELECT * FROM tbl_userdroppedcardcode WHERE room_id='".$room_id."'");

            $firstcardquery = $conn->query("Select * from tbl_buroom Where br_id='".$room_id."'"); 
            $firstResult = $firstcardquery->fetch_assoc();

            if($query->num_rows > 0 || $firstResult['firstpageofdack'] != ''){

                
                $dropped_cards = '';
                $query1 = $conn->query("SELECT GROUP_CONCAT(dropped_card_code) AS dropped_cards FROM tbl_userdroppedcardcode WHERE room_id='".$room_id."'");

                //if($query1){
                    $dropped_cards = ($query1->fetch_assoc())['dropped_cards'];

                    if($firstResult['firstpageofdack'] != '') {
                        $dropped_cards = $firstResult['firstpageofdack'].','.$dropped_cards;
                    }

                    $conn->query("DELETE FROM tbl_userdroppedcardcode WHERE room_id='".$room_id."'");
                    $query = $conn->query("update tbl_buroom set firstpageofdack='' where br_id='".$room_id."'");
                //}

                $data['msg']="Success";
                $data['result']=1;
                $data['dropped_cards']=$dropped_cards;

            }else{

                $data['msg']="No Dropped Card Code";
                $data['result']=1;
            }
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
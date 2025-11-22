<?php   

include "Include/Connection.php";

@$room_id=$_REQUEST['room_id'];

$data=array();
response(addData($room_id));

function response($data){

    echo json_encode($data);
}

function addData($room_id){

    global $conn;
    
    try {

        $query1 = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");

        if($query1){

            $roomCreaterUserId = ($query1->fetch_assoc())['br_user_id'];

            $conn->query("UPDATE tbl_buroom SET buraco_cnt=2 WHERE br_id='".$room_id."' AND br_user_id='".$roomCreaterUserId."'");

            $conn->query("UPDATE tbl_buracojoinroom SET points=0 WHERE room_id='".$room_id."'");
            $conn->query("UPDATE tbl_buracojoinroom SET current=1 WHERE room_id='".$room_id."' AND user_id='".$roomCreaterUserId."'");
            $conn->query("UPDATE tbl_buracoflag SET flag=0, rematchFlag=1 WHERE room_id='".$room_id."'");

            $conn->query("DELETE FROM tbl_userdroppedcardcode WHERE room_id='".$room_id."'");
            $conn->query("DELETE FROM tbl_buracouserpoints WHERE room_id='".$room_id."'");
            
            $data['msg']="Success";
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
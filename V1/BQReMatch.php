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
        $conn->query("UPDATE tbl_room SET card_cnt=0,round_cnt=0, roundfirstcard_type='' WHERE room_id='".$room_id."'");
        $conn->query("UPDATE tbl_joinroom SET current=0, cardplay_current=0,IsPass=0 WHERE room_id='".$room_id."'");

        $query = $conn->query("SELECT * FROM tbl_room where room_id='".$room_id."'");
        $user_id = ($query->fetch_assoc())['user_id'];
        $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_id."' and user_id='".$user_id."'");
        $conn->query("UPDATE tbl_flag SET flag=0, rematchFlag=1 WHERE room_id='".$room_id."' and user_id='".$user_id."'");

        $conn->query("DELETE FROM tbl_bid_user WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_bid WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_bqcardplay WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_roundwinner WHERE room_id='".$room_id."'");

        $query123 = $conn->query("SELECT tbl_registration.user_id,tbl_registration.user_name, tbl_registration.Img FROM tbl_registration INNER JOIN tbl_joinroom ON tbl_registration.user_id=tbl_joinroom.user_id and room_id='".$room_id."'");
               
        $rows = [];
        for ($x = 1; $x <= $conn->affected_rows; $x++) {
            $rows[] = $query123->fetch_assoc();
        }

        $data['users']=$rows;
		$data['msg']="Success";
        $data['result']=1;
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
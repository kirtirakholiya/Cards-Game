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
        $conn->query("DELETE FROM tbl_room WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_joinroom WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_bid_user WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_bid WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_bqcardplay WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_roundwinner WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_flag WHERE room_id='".$room_id."'");
            
        $data['msg']="Success";
        $data['result']=1;
    
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
}



?>
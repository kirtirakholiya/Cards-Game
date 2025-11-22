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
        $conn->query("DELETE FROM tbl_buroom WHERE br_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_buracojoinroom WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_buracoteam WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_userdroppedcardcode WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_buracouserpoints WHERE room_id='".$room_id."'");
        $conn->query("DELETE FROM tbl_buracoflag WHERE room_id='".$room_id."'");
            
        $data['msg']="Success";
        $data['result']=1;
    
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
}

?>
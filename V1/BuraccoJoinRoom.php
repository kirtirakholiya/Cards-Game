<?php
include "Include/Connection.php";

$br_code = $_REQUEST['room_code'];
$user_id = $_REQUEST['user_id'];

$result = mysqli_query($conn,"Select * from tbl_buroom where br_code='".$br_code."' and status=1");
if(mysqli_num_rows($result)){

    $room_id = ($result->fetch_assoc())['br_id'];

    $result = mysqli_query($conn,"Select * from tbl_buracojoinroom where room_id='".$room_id."' and status=1");
    if(mysqli_num_rows($result) > 6){
        $jsonarr=array('ws_status'=>false,'Message'=>'Room is full you can\'t join room');        
        echo json_encode($jsonarr);
    }else{
        $userresult = mysqli_query($conn,"Select * from tbl_buracojoinroom where room_id='".$room_id."' and user_id='".$user_id."' and status=1");
        if(mysqli_num_rows($userresult) > 0){
            $jsonarr=array('ws_status'=>false,'Message'=>'User has already joined this room');        
            echo json_encode($jsonarr);
        }else{
            $query = mysqli_query($conn,"INSERT INTO tbl_buracojoinroom(room_id,user_id,joined_date) values('".$room_id."','".$user_id."','".date ("Y-m-d H:i:s")."')");
            $lastid = mysqli_insert_id($conn);
                
            $jsonarr=array('ws_status'=>true,'Message'=>'Success', 'room_id'=>$room_id);        
            echo json_encode($jsonarr);
        }
    }
}else{
    $jsonarr=array('ws_status'=>false,'Message'=>'No room found');        
    echo json_encode($jsonarr);
}
?>
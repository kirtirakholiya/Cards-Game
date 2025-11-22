<?php   


include "Include/Connection.php";

@$room_id=$_REQUEST['room_id'];
@$user_id=$_REQUEST['user_id'];
@$flag=$_REQUEST['flag'];
@$deck = $_REQUEST['deck'];

$data=array();
response(addData($room_id,$user_id,$flag,$deck));

function response($data){
    
    echo json_encode($data);
}


function addData($room_id,$user_id,$flag,$deck){
    
    global $conn;
    
    try {
        // $conn->query("UPDATE tbl_flag SET flag='".$flag."',deck_id='".$deck."', rematchFlag=0 WHERE room_id='".$room_id."' and user_id='".$user_id."'");
        $conn->query("UPDATE tbl_flag SET flag='".$flag."',deck_id='".$deck."' WHERE room_id='".$room_id."' and user_id='".$user_id."'");
        
        $data['msg']="Flag Added";
        $data['result']=1;
        $data['flag']=$flag;

        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
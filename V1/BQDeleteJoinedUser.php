<?php   

include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_id=$_REQUEST['room_id'];

$data=array();
response(addData($user_id, $room_id));

function response($data){
    
    echo json_encode($data);
}


function addData($user_id, $room_id){

    global $conn;
    
    try {
        $query = $conn->query("DELETE FROM tbl_joinroom WHERE user_id='".$user_id."' and room_id='".$room_id."'");
        if ($query==true) {
            $data['msg']="Success";
            $data['result']=1;
        }
        else
        {
            $data['msg']="Failed";
            $data['result']=0;
        }
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
}

?>
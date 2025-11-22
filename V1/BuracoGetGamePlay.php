<?php   

include "Include/Connection.php";
include "Include/BuracoUtilityFun.php";

@$room_id=$_REQUEST['room_id'];
@$user_id=$_REQUEST['user_id'];

$data=array();
response(addData($room_id, $user_id));

function response($data){
    
    echo json_encode($data);
}


function addData($room_id, $user_id){

    global $conn;
    
    try {
        $resultFlag = $conn->query("Select * from tbl_buracoflag where room_id='".$room_id."'");

        if ($resultFlag==true) {
            $rowFlag=$resultFlag->fetch_assoc();

            $finalarray = GetJoinedUsersId($conn, $room_id, $user_id);

            $data['msg']="Success";
            $data['result']=1;
            $data['data']=$rowFlag;
            $data['usersequencedata'] = ($rowFlag['flag'] == 1) ? $finalarray : 'NULL';
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
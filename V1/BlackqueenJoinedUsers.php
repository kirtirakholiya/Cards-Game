<?php

include "Include/Connection.php";

@$room_id = $_REQUEST['room_id'];
@$user_id = $_REQUEST['user_id'];
$data=array();
response(Verify($room_id, $user_id));

function response($data){
    
     echo json_encode($data);
}


function Verify($room_id, $user_id){

    global $conn;
    
    try {

        $joinedUserIds = array();
        $rows = [];
        
        $query1 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND user_id='".$user_id."'");

        $currentUserTblId = ($query1->fetch_assoc())['id'];

        $query2 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND id>='".$currentUserTblId."'");

        while ($row = $query2->fetch_assoc()){

            $joinedUserIds[]=$row['user_id'];
        }
        
        $query3 = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_id."' AND id<'".$currentUserTblId."'");

        while ($row = $query3->fetch_assoc()){

            $joinedUserIds[]=$row['user_id'];
        }
        
        foreach ($joinedUserIds as $value) {

            $query = $conn->query("SELECT tbl_registration.user_id,tbl_registration.user_name, tbl_registration.Img FROM tbl_registration WHERE user_id='".$value."'");
            $rowcount = $query->fetch_assoc();
            $rowcount['Img']=($rowcount['Img']) ? IMG_URL.$rowcount['Img'] : 'null';
            $rows[] = $rowcount;
        }
        
        if (count($rows) > 0) {
        
            $data['msg']="Success";        
            $data['result']=1;
            $data['data']=$rows;
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
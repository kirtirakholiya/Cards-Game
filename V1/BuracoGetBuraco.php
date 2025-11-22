<?php   
include "Include/Connection.php";

@$room_id=$_REQUEST['room_id'];
@$user_id=$_REQUEST['user_id'];
@$card_cnt=$_REQUEST['card_cnt'];

$data=array();
response(addData($room_id,$user_id,$card_cnt));

function response($data){
    
    echo json_encode($data);
}

function addData($room_id,$user_id,$card_cnt){
    
    global $conn;
    
    try {
        $query = $conn->query("SELECT * FROM tbl_buroom WHERE br_id='".$room_id."'");

        if($query->num_rows > 0){
            $resbu = $query->fetch_assoc();
            $buraco_cnt = $resbu['buraco_cnt'];

            --$buraco_cnt;
            $conn->query("UPDATE tbl_buroom SET buraco_cnt='".$buraco_cnt."' WHERE br_id='".$room_id."'");

            if($buraco_cnt >= 0){

                $query2 = $conn->query("SELECT * FROM tbl_buracojoinroom WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
                $currentUserPoints = ($query2->fetch_assoc())['points'];

                $currentUserPoints += ($card_cnt == 1 ? 50 : 100);

                $conn->query("UPDATE tbl_buracojoinroom SET points='".$currentUserPoints."' WHERE room_id='".$room_id."' AND user_id='".$user_id."'");
            }            

            $data['msg']="Success";
            $data['result']=1;
            $data['buraco_cnt']=$buraco_cnt;
            $data['buraco1']=$resbu['buraco1'];
            $data['buraco2']=$resbu['buraco2'];

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
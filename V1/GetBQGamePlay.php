<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_id=$_REQUEST['room_id'];

$data=array();
response(addData($user_id,$room_id));

function response($data){
    
    echo json_encode($data);
}


function addData($user_id,$room_id){

    global $conn;
    
    try {
        $result = $conn->query("Select * from tbl_flag where room_id='".$room_id."'");
        $row = $result->num_rows;
        if ($result==true) {
            $rowcount=$result->fetch_object();
            $data['msg']="Success";
            $data['result']=1;
            $data['data']=$rowcount;
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
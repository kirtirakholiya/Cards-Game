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
        $rows = [];
        $query = $conn->query("SELECT * FROM tbl_room where room_id='".$room_id."'");

        if($query->num_rows){

            $query123 = $conn->query("SELECT tbl_registration.user_id,tbl_registration.user_name, tbl_registration.Img FROM tbl_registration INNER JOIN tbl_joinroom ON tbl_registration.user_id=tbl_joinroom.user_id and room_id='".$room_id."'");

            while ($row = $query123->fetch_assoc()) { 
                $rows[] = $row;
            }

            $query1 = $conn->query("SELECT * FROM tbl_flag where room_id='".$room_id."'");

            $data['RematchFlag']=($query1->fetch_assoc())['rematchFlag'];
        }else{

            $data['RematchFlag']='2';
        }

        $data['users']=$rows;
		$data['msg']="Success";
        $data['result']='1';

        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
}



?>
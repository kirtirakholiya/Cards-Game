<?php

function generateRandomString($length = 25) {

// **************************************************************************************

//  Date                     Developer                       Comments    
//  09-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

// **************************************************************************************

    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
//usage 
$myRandomString = generateRandomString(4);

//echo $myRandomString;


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
$data=array();
response(addData($myRandomString,$user_id));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($myRandomString,$user_id){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {
        $query = $conn->query("INSERT INTO tbl_room(code,user_id) values('".$myRandomString."','".$user_id."')");
        $result = $conn->query("Select * from tbl_room where code='".$myRandomString."'");
        $row = $result->num_rows;
        if ($query==true) {
            $result1 = $conn->query("Select room_id,code from tbl_room where code='".$myRandomString."'");
            $rw = $result1->fetch_assoc();
            $roomId = $rw["room_id"];
            $qy = $conn->query("Insert Into tbl_joinroom(room_id,user_id,current,cardplay_current) Values('".$roomId."','".$user_id."',1,1)");
            $conn->query("INSERT INTO tbl_flag(room_id,user_id,flag,rematchFlag) values('".$roomId."','".$user_id."','0','0')");
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
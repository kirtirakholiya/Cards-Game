<?php


include "Include/Connection.php";

@$Code = $_REQUEST['code'];
@$user_id = $_REQUEST['user_id'];
$data=array();
response(Verify($Code,$user_id));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
     echo json_encode($data);
}


function Verify($Code,$user_id){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  08-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {
        $query = $conn->query("SELECT room_id FROM tbl_room WHERE code='".$Code."'");
        $row = $query->num_rows;
        if ($row==1) {
            // $data["room_id"]=$roomId;
            $rw = $query->fetch_assoc();
            $roomId = $rw["room_id"];
            $result = $conn->query("select count(room_id) as Number from tbl_joinroom WHERE room_id ='".$roomId."'");
            $roomValid = $result->fetch_assoc();
            $vlId = $roomValid["Number"];
            if ($vlId  == 7) {
                $data['msg']="full";    
            }
            else
            {
                $rs = $conn->query("Select Count(*) as useCount from tbl_joinroom Where user_id='".$user_id."' AND room_id='".$roomId."'"); 
                $roomValid = $rs->fetch_assoc();
                $vlId = $roomValid["useCount"];
                if ($vlId == 0)
                {
                    $qy = $conn->query("Insert Into tbl_joinroom(room_id,user_id) Values('".$roomId."','".$user_id."')");
                
                    if($qy == true){
                    $data['msg']="Verified Success";        
                    $data['result']=1;
                    $data['Room_id'] = $roomId;
                    }
                    else
                    {
                        $data['msg']="Something went Wrong";    
                        $data['result']=0;
                    }
                }
                else
                {
                    $data['msg']="Already joined";    
                    $data['result']=0;
                }
                
            }
        }
        else
        {
            $data['msg']="Invalide room code.!!";
            $data['result']=0;
        }
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}

?>
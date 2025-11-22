<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$password = $_REQUEST['password'];
$data=array();
response(addData($user_id,$password));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($user_id,$password){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {
        $newPass=password_hash($password,PASSWORD_BCRYPT);
        $query = $conn->query("UPDATE tbl_registration SET password='".$newPass."' WHERE user_id='".$user_id."'");
        if ($query==true) {
            $data['msg']="Password changed successfully.";
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
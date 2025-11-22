<?php   


include "Include/Connection.php";

@$user_name=$_REQUEST['user_name'];
@$mobile=$_REQUEST['mobile'];
@$password = $_REQUEST['password'];
$data=array();
response(addData($user_name,$mobile,$password));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($user_name,$mobile,$password){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {
        $newPass=password_hash($password,PASSWORD_BCRYPT);
        $resultcnt = $conn->query("Select * from tbl_registration where mobile_no='".$mobile."'");
        if($resultcnt->num_rows == 0){
            $query = $conn->query("INSERT INTO tbl_registration(user_name,mobile_no,password) values('".$user_name."','".$mobile."','".$newPass."')");
            $result = $conn->query("Select * from tbl_registration where mobile_no='".$mobile."'");
            $row = $result->num_rows;
            if ($query==true) {
                $rowcount=$result->fetch_object();
                $data['msg']="Register Success";
                $data['result']=1;
                $data['data']=$rowcount;
            }
            else
            {
                $data['msg']="Failed";
                $data['result']=0;
            }
        }else{
            $data['msg']="Number is already registered";
            $data['result']=0;
        }
        
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
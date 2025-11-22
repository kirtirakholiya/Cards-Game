<?php   


include "Include/Connection.php";


@$mobile_no = $_REQUEST['mobile_no'];
@$password=$_REQUEST['password'];
$data=array();
response(Authenticate($mobile_no,$password));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function Authenticate($mobile_no,$password){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  08-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {
        // $pass = $conn->query("SELECT password FROM tbl_registration WHERE mobile_no='".$mb."'");
        // $row1 = $pass->num_rows;
        // $newpass=password_verify($row1['password'],$password);

        // echo $newpass;
        // $query = $conn->query("SELECT user_name,mobile_no FROM tbl_registration WHERE user_name='".$username."'");
        // $row = $query->num_rows;
        // if (password_verify($row1['password'],$password)) {
        //     $rowcount=$query->fetch_object();
        //     $data['msg']="Log In Success";
        //     $data['result']=1;
        //     $data['user']=$newpass;
        //     $data['user']=$rowcount;
        // }
        // else
        // {
        //     $data['msg']="Failed";
        //     $data['result']=1;
        //     $data['newpss']=$newpass;
        // }
        // return $data;
        $result = $conn->query("SELECT * FROM tbl_registration where mobile_no='".$mobile_no."'");
        $result1 = $conn->query("SELECT * FROM tbl_registration where mobile_no='".$mobile_no."'");
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $pass=  $row["password"];
                $check = password_verify($password,$pass);
                if($check==true)
                {
                    
                    $rowcount=$result1->fetch_assoc();
                    $rowcount['Img']=($rowcount['Img']) ? IMG_URL.$rowcount['Img'] : 'null';
                    $data['msg']="Log In Success";
                    $data['result']=1;
                    $data['data']=$rowcount;
                }
                else{
                    $data['msg']="Failed";
                    $data['result']=0;
                }
        }
        } else {
            $data['msg']="Failed";
            $data['result']=0;
        }
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
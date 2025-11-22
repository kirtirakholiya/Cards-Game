<?php   


include "Include/Connection.php";

@$otp = $_REQUEST['otp'];
$data=array();
response(Verify($otp));

function response($data){
    
     echo json_encode($data);
}


function Verify($otp){

    global $conn;
    
    try {
        $query = $conn->query("SELECT user_name,mobile_no FROM tbl_temp WHERE otp='".$otp."' ORDER BY temp_id desc");
        $row = $query->num_rows;
        
        if ($row>=1) {
            $rowcount=$query->fetch_object();
            $data['msg']="Verification Successfully.";
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
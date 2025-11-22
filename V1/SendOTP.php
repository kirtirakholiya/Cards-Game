<?php   

include "Include/Connection.php";
include "Include/SMSUtilityFun.php";


@$username = $_REQUEST['user_name'];
@$mobile = $_REQUEST['mobile_no'];
$data=array();
response(addData($username,$mobile));

function response($data){

    echo json_encode($data);
}

function addData($username,$mobile){

    global $conn;
    
    try {
        $random = rand(1000,9999);
        if(validate_phone_number($mobile))
        {
            $countryCode = substr($mobile, 0, 3);

            if($countryCode == "+91") {
                SendOtp($mobile, $random);
            }else{
                SendOtpViaTwilio($mobile, $random);
            }
            
            $query = $conn->query("INSERT INTO tbl_temp(user_name,mobile_no,otp) Values('".$username."','".$mobile."','".$random."')");
            $result = $conn->query("Select temp_id,mobile_no,otp from tbl_temp where mobile_no='".$mobile."' ORDER BY temp_id desc");
            $row = $result->num_rows;
        
            if ($query==true ) {
                $rowcount=$result->fetch_object();
                $data['msg']="Insert Success";
                $data['result']=1;
                $data['data']=$rowcount;
            }
            else
            {
                $data['msg']="Failed";
                $data['result']=0;
            }
        }
        else
        {
            $data['msg']="Failed";
            $data['result']=0;
        }
        return $data;   
    } catch (Exception $th) {
        $data[]=array("Message"=>$th);
    }
}

function validate_phone_number($phone)
{   
     // Allow +, - and . in phone number
     $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
     // Remove "-" from number
     $phone_to_check = str_replace("-", "", $filtered_phone_number);
     // Check the lenght of number
     // This can be customized if you want phone number from a specific country
     if (strlen($phone_to_check) < 10 || strlen($phone_to_check) > 14) {
        return false;
     } else {
       return true;
     }
}

?>
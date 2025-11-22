<?php   
include "Include/Connection.php";
include "Include/SMSUtilityFun.php";


@$mobile = $_REQUEST['mobile_no'];
$data=array();
response(addData($mobile));

function response($data){
    echo json_encode($data);
}

function addData($mobile){
    
    global $conn;
    
    try {
        $random = rand(1000,9999);
        if(validate_phone_number($mobile))
        {
            $result = $conn->query("SELECT user_id,mobile_no FROM tbl_registration where mobile_no='".$mobile."'");
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    $countryCode = substr($mobile, 0, 3);

                    if($countryCode == "+91") {
                        SendOtp($mobile, $random);
                    }else{
                        SendOtpViaTwilio($mobile, $random);
                    }

                    $query = $conn->query("UPDATE tbl_temp SET otp='".$random."' WHERE mobile_no='".$mobile."'");
                    $result1 = $conn->query("SELECT mobile_no,otp FROM tbl_temp where mobile_no='".$mobile."'");
                    if ($query==true) {
                        $rowcount=$result1->fetch_object();
                        $data['msg']="Success";
                        $data['result']=1;
                        $data['data']=$rowcount;
                    }
                    else
                    {
                        $data['msg']="Failed";
                        $data['result']=0;
                    }
                 }
                    
            }
             else {
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
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
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
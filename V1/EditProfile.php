<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$username = $_REQUEST['username'];

$data=array();
response(addData($user_id,$username));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($user_id,$username){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  09-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn,$uploadpath;
    
    try {
        if($username=="")
        {
            $data['msg']="Failed";
            $data['result']=0;
        }
        else{
            if (($_FILES['filename']['name']!="")){
                // Where the file is going to be stored
                //$target_dir = UPLOAD_URL;
                $file = date('ymdhis').$_FILES['filename']['name'];
            
                $temp_name = $_FILES['filename']['tmp_name'];
                $path_filename_ext = $uploadpath.$file;
                
                move_uploaded_file($temp_name,$path_filename_ext);
            }
                      
            $query = $conn->query("UPDATE tbl_registration SET user_name='".$username."',Img='".$file."' WHERE user_id='".$user_id."'");
            $result1 = $conn->query("SELECT user_id,user_name,mobile_no,Img FROM tbl_registration where user_id='".$user_id."'");
            if ($query==true) {
                $rowcount=$result1->fetch_assoc();
                $rowcount['Img']=($rowcount['Img']) ? IMG_URL.$rowcount['Img'] : 'null';
                $data['msg']="Profile Updated";
                $data['result']=1;
                $data['data']=$rowcount;
            }
            else
            {
                $data['msg']="Failed";
                $data['result']=0;
            }
        }
        
        return $data;
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_id=$_REQUEST['room_id'];

@$sir=$_REQUEST['sir'];
@$partner1 = $_REQUEST['partner1'];
@$partner2 = $_REQUEST['partner2'];
$data=array();
response(addData($room_id,$user_id,$sir,$partner1,$partner2));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($room_id,$user_id,$sir=NULL,$partner1=NULL,$partner2=NULL){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {

        $conn->query("UPDATE tbl_flag SET rematchFlag=0 WHERE room_id='".$room_id."'");

        $query1 = $conn->query("SELECT * FROM `tbl_joinroom` where room_id='".$room_id."'");   
        $count = $query1->num_rows;

       
        if($partner1 == '' && $partner2 == ''){
            $data['msg']="You need to select partners";
            $data['result']=0;
        }else{
            if($count >=4 && $count <=5 && $partner1 != ''){
                $query = $conn->query("INSERT INTO tbl_bid(room_id,trump,partner_card1,partner_card2,user_id) values('".$room_id."','".$sir."','".$partner1."','".$partner2."',".$user_id.")");
                if ($query==true) {
                    $data['msg']="Partner Added";
                    $data['result']=1;
                }
                else
                {
                    $data['msg']="Failed";
                    $data['result']=0;
                }
            }else if($count >=6 && $count <=7 && $partner1 != '' && $partner2 != ''){
                $query = $conn->query("INSERT INTO tbl_bid(room_id,trump,partner_card1,partner_card2,user_id) values('".$room_id."','".$sir."','".$partner1."','".$partner2."',".$user_id.")");
                if ($query==true) {
                    $data['msg']="Partner Added";
                    $data['result']=1;
                }
                else
                {
                    $data['msg']="Failed";
                    $data['result']=0;
                }
            }else{
                $data['msg']="You need to select partner";
                $data['result']=0;
            }
        }
        return $data;
        
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
<?php
include "Include/Connection.php";

function generateRandomString($length = 25) {
    
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

$user_id=$_REQUEST['user_id'];
$bid=$_REQUEST['bid'];

if($user_id != '' && $bid != ''){

    $query = mysqli_query($conn,"INSERT INTO tbl_buroom(br_code,br_user_id,br_bid,created_date) values('".$myRandomString."','".$user_id."','".$bid."','".date ("Y-m-d H:i:s")."')");
    $lastid = mysqli_insert_id($conn);
   
    $result = mysqli_query($conn,"Select * from tbl_buroom where br_id='".$lastid."' and status=1");
    if(mysqli_num_rows($result)){

        mysqli_query($conn,"INSERT INTO tbl_buracojoinroom(room_id,user_id,current) values('".$lastid."','".$user_id."',1)");
        mysqli_query($conn,"INSERT INTO tbl_buracoflag(room_id,user_id,flag,rematchFlag) values('".$lastid."','".$user_id."',0,0)");
       
        $rowcount = mysqli_fetch_assoc($result);
        $jsonarr=array('ws_status'=>true,'Message'=>'Success','data'=>$rowcount);        
        echo json_encode($jsonarr);
        
    }
    else
    {
        $jsonarr=array('ws_status'=>false,'Message'=>'Failed');        
        echo json_encode($jsonarr);
    }
}else{
    $jsonarr=array('ws_status'=>false,'Message'=>'Failed');        
    echo json_encode($jsonarr);
}
?>
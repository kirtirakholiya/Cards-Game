<?php
include "Include/Connection.php";
$room_id = $_REQUEST['room_id'];

$result = mysqli_query($conn,"Select * from tbl_buroom where br_id='".$room_id."' and status=1");
if(mysqli_num_rows($result)){

    $result = mysqli_query($conn,"Select j.*,u.user_name,u.Img from tbl_buracojoinroom j left join tbl_registration u on u.user_id=j.user_id where j.room_id='".$room_id."' and j.status=1");
    if(mysqli_num_rows($result) > 0){

        $finalarray = array();

        while($rowcount = mysqli_fetch_assoc($result)){
            $rowcount['Img']=($rowcount['Img']) ? IMG_URL.$rowcount['Img'] : 'null';
            $finalarray[] = $rowcount;
        }
                
        $jsonarr=array('ws_status'=>true,'Message'=>'Success','data'=>$finalarray);        
        echo json_encode($jsonarr);
   
    }else{
        $jsonarr=array('ws_status'=>false,'Message'=>'Users not found');        
        echo json_encode($jsonarr);
    }
}else{
    $jsonarr=array('ws_status'=>false,'Message'=>'No room found');        
    echo json_encode($jsonarr);
}
?>

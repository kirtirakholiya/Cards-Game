<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_Id =$_REQUEST['room_id'];
$data=array();
response(addData($user_id,$room_Id));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($user_id,$room_Id){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
     try {
    
       
        $query = $conn->query("SELECT user_id,user_name,Img FROM tbl_registration where user_id='".$user_id."'");
        $xyz134 = $query->fetch_assoc();
        $xyz134['Img']=($xyz134['Img']) ? IMG_URL.$xyz134['Img'] : 'null';
       
        $query1 = $conn->query("SELECT user_id as NextUserId FROM tbl_joinroom where room_id='".$room_Id."' AND current=1 ORDER BY joinedOn ASC LIMIT 1");   
        $xyz1 = $query1->fetch_assoc();
        $vlId15 = $xyz1['NextUserId'];

        $querypass = $conn->query("SELECT * FROM tbl_joinroom where room_id='".$room_Id."' and IsPass = 0");
        $passrow = $querypass->num_rows;

        if($passrow == 1){
            $nextuserid = '-';
        }else{
            $nextuserid = $vlId15;
        }
        
        $maxbidqry = $conn->query("Select MAX(cast(bid as unsigned)) as mbid from tbl_bid_user Where room_id='".$room_Id."'"); 
        $maxBid = $maxbidqry->fetch_assoc();

        $userqry = $conn->query("Select user_id from tbl_bid_user Where room_id='".$room_Id."' and bid='".$maxBid['mbid']."'"); 
        $maxBiduserid = $userqry->fetch_assoc();

         
             $data['msg']="Success";
                $data['maxBid']=  ($maxBid['mbid'] != '') ? $maxBid['mbid'] : 0;
                $data['maxBidUserId']= ($userqry->num_rows != 0) ? $maxBiduserid['user_id'] : 0;
                $data['result']=1;
                $data['data']=$xyz134;
                $r =  array_slice($data['data'], 0, 3, true) +
                array("NextUserId"=>$nextuserid) +
                array_slice($data['data'], 3, count($data['data']) - 1, true) ;
                $data['data']=$r;
         
        
        return $data;
    } catch (\Throwable $th) {
       $data[]=array("Message"=>"Failed");
       
    }
}


?>
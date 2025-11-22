<?php   


include "Include/Connection.php";

@$user_id=$_REQUEST['user_id'];
@$room_Id =$_REQUEST['room_id'];
@$bid=$_REQUEST['bid'];
@$IsPass = $_REQUEST['IsPass'];
$data=array();
response(addData($user_id,$bid,$room_Id,$IsPass));

function response($data){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    
    echo json_encode($data);
}


function addData($user_id,$bid,$room_Id,$IsPass){
    // **************************************************************************************

    //  Date                     Developer                       Comments    
    //  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

    // **************************************************************************************
    global $conn;
    
    try {


            if ($IsPass == 'false')
            {
                 $rs = $conn->query("Select Count(*) as useCount from tbl_bid_user Where room_id='".$room_Id."' and user_id='".$user_id."'"); 
                $roomValid = $rs->fetch_assoc();
                $vlId = $roomValid["useCount"];
                
                if ($vlId == 0)
                {
                    $rs34 = $conn->query("SELECT user_id from tbl_joinroom where room_id='".$room_Id."' and current =1"); 
                    $rw1 = $rs34->fetch_assoc();
                    $curr = $rw1["user_id"];
                    if($curr == $user_id)
                    {

                        $query = $conn->query("INSERT INTO tbl_bid_user(bid,room_id,user_id) values('".$bid."','".$room_Id."','".$user_id."')");
                        $rs1 = $conn->query("SELECT joinedOn FROM `tbl_joinroom` WHERE user_id='".$user_id."'  And room_id='".$room_Id."'"); 
                        $roomValid1 = $rs1->fetch_assoc();
                        $vlId1 = $roomValid1["joinedOn"];
                        $query1 = $conn->query("SELECT user_id as NextUserId FROM tbl_joinroom where joinedOn > '".$vlId1."' AND room_id='".$room_Id."' AND IsPass=0 ORDER BY joinedOn ASC LIMIT 1");   
                        $xyz1 = $query1->fetch_assoc();
                        if($xyz1!=null && $xyz1!=' ' && $xyz1["NextUserId"]!=null && $xyz1["NextUserId"]!=' '){
                            $vlId1 = $xyz1["NextUserId"];
                            $query2 = $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$vlId1."'");
                            
                        }else{
                            
                            $query1 = $conn->query("SELECT user_id FROM `tbl_joinroom` where room_id='".$room_Id."' and IsPass=0 order BY joinedOn asc limit 1");   
                            $xyz43 = $query1->fetch_assoc();
                            $vlId3 = $xyz43["user_id"];
                        
                            $query5 = $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$vlId3."'");    
                            
                        }
                        
                        $query3 = $conn->query("UPDATE tbl_joinroom SET current=0, cardplay_current=0 WHERE room_id='".$room_Id."' AND user_id='".$user_id."'");

                        $maxbidqry = $conn->query("Select MAX(cast(bid as unsigned)) as mbid from tbl_bid_user Where room_id='".$room_Id."'"); 
                        $maxBid = $maxbidqry->fetch_assoc();
                        
                        if ($query==true) { 
                            $data['maxBid']= $maxBid['mbid'];
                            $data['msg']="Bid Added";
                            $data['result']=1;
                        }
                        else
                        {
                            $data['msg']="Failed";
                            $data['result']=0;
                        }
                    }
                    else{
                        $data['msg']="Not your turn";
                        $data['result']=0;
                    }
                }
                else{
                    $rs34 = $conn->query("SELECT user_id from tbl_joinroom where room_id='".$room_Id."' and current =1"); 
                    $rw1 = $rs34->fetch_assoc();
                    $curr = $rw1["user_id"];
                    if($curr == $user_id){ 
                        $rs = $conn->query("Select bid_id from tbl_bid_user Where user_id='".$user_id."' AND room_id='".$room_Id."'"); 
                        $rw = $rs->fetch_assoc();
                        $bid_id = $rw["bid_id"];
                        $query = $conn->query("UPDATE tbl_bid_user SET bid='".$bid."' Where bid_id='".$bid_id."'");
                        $rs1 = $conn->query("SELECT joinedOn FROM `tbl_joinroom` WHERE user_id='".$user_id."' And room_id='".$room_Id."'"); 
                        $roomValid1 = $rs1->fetch_assoc();
                        $vlId1 = $roomValid1["joinedOn"];
                        $query1 = $conn->query("SELECT user_id as NextUserId FROM tbl_joinroom where joinedOn > '".$vlId1."' AND room_id='".$room_Id."' AND IsPass=0 ORDER BY joinedOn ASC LIMIT 1");   
                        $xyz1 = $query1->fetch_assoc();
                        if($xyz1!=null && $xyz1!=' ' && $xyz1["NextUserId"]!=null && $xyz1["NextUserId"]!=' '){
                            $vlId1 = $xyz1["NextUserId"];
                            $query4 = $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$vlId1."'");
                        }else{
                           
                                $query1 = $conn->query("SELECT user_id as NextUserId FROM `tbl_joinroom` where room_id='".$room_Id."' and IsPass=0 order BY joinedOn asc limit 1");   
                                $xyz43 = $query1->fetch_assoc();
                                $vlId3 = $xyz43["NextUserId"];
                                $query4 = $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$vlId3."'");
                         
                        } 
                        $query2 = $conn->query("UPDATE tbl_joinroom SET current=0, cardplay_current=0 WHERE room_id='".$room_Id."' AND user_id='".$user_id."'");
                        
                        $maxbidqry = $conn->query("Select MAX(cast(bid as unsigned)) as mbid from tbl_bid_user Where room_id='".$room_Id."'"); 
                        $maxBid = $maxbidqry->fetch_assoc();
                        
                        if ($query==true) { 
                            $data['maxBid']= $maxBid['mbid'];
                            $data['msg']="Bid Updated";
                            $data['result']=1;
                        }
                        else
                        {
                            $data['msg']="Failed";
                            $data['result']=0;
                        }
                    }
                    else{
                        $data['msg']="Not your turn";
                        $data['result']=0;
                    }
                    
                }
                return $data;
            } 
            else{
                
                $query9 = $conn->query("UPDATE tbl_joinroom SET current=0,IsPass=1,cardplay_current=0 WHERE room_id='".$room_Id."' AND user_id='".$user_id."'");
                    
                $rs1 = $conn->query("SELECT joinedOn FROM `tbl_joinroom` WHERE user_id='".$user_id."' And room_id='".$room_Id."'"); 
                $roomValid1 = $rs1->fetch_assoc();
                $vlId1 = $roomValid1["joinedOn"];
                $query1 = $conn->query("SELECT user_id as NextUserId FROM tbl_joinroom where joinedOn > '".$vlId1."' AND room_id='".$room_Id."' AND IsPass=0 ORDER BY joinedOn ASC LIMIT 1");   
                $xyz1 = $query1->fetch_assoc();
                if($xyz1!=null && $xyz1!=' ' && $xyz1["NextUserId"]!=null && $xyz1["NextUserId"]!=' '){
                    $vlId1 = $xyz1["NextUserId"];
                    $query4 = $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$vlId1."'");
                }else{
                   
                    $query1 = $conn->query("SELECT user_id as NextUserId FROM `tbl_joinroom` where room_id='".$room_Id."' and IsPass=0  order BY joinedOn asc limit 1");   
                    if($query1->num_rows > 0){
                        $xyz43 = $query1->fetch_assoc();
                        $vlId3 = $xyz43["NextUserId"];
                        $query4 = $conn->query("UPDATE tbl_joinroom SET current=1, cardplay_current=1 WHERE room_id='".$room_Id."' AND user_id='".$vlId3."'");
                    }   
                } 

                $queryPlayerCnt = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_Id."'");
                $player_cnt = $queryPlayerCnt->num_rows;

                $queryBidPassPlyCnt = $conn->query("SELECT * FROM tbl_joinroom WHERE room_id='".$room_Id."' AND IsPass=1");
                $bidPassPlayerCnt = $queryBidPassPlyCnt->num_rows;
               
                if($player_cnt == $bidPassPlayerCnt){
                    $query = $conn->query("SELECT * FROM tbl_room WHERE room_id='".$room_Id."'");
                    $roomCreatedUserId = ($query->fetch_assoc())['user_id'];
                    $conn->query("INSERT INTO tbl_bid_user(bid,room_id,user_id) values('75','".$room_Id."','".$roomCreatedUserId."')");

                    $conn->query("UPDATE tbl_joinroom SET current=0, cardplay_current=0 WHERE room_id='".$room_Id."'");

                    $conn->query("UPDATE tbl_joinroom SET current=1,cardplay_current=1,IsPass=0 WHERE room_id='".$room_Id."' and user_id='".$roomCreatedUserId."'");
            
                    $data['maxBid']= 75;
                    $data['msg']="Bid Completed";
                    $data['result']=0;
                }else{
                    $maxbidqry = $conn->query("Select MAX(cast(bid as unsigned)) as mbid from tbl_bid_user Where room_id='".$room_Id."'"); 
                    $maxBid = $maxbidqry->fetch_assoc();
                    $data['maxBid']= ($maxBid['mbid'] != '') ? $maxBid['mbid'] : 0;
                    $data['msg']="Skip";
                    $data['result']=1;
                }

               
            }
            


            return $data;
          
    } catch (\Throwable $th) {
        $data[]=array("Message"=>"Failed");
    }
    
    

}



?>
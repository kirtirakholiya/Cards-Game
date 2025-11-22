<?php

define('SEQUENCE', 'sequence');
define('PAIR', 'pair');

//Previous Player at 0 index and Next Player at 1 index.
function getNextPreviousPlayer($conn, $room_id, $user_id){

    $players = array();
    $retData = array();

    $result = $conn->query("SELECT team1,team2 FROM `tbl_buracoteam` WHERE room_id='".$room_id."' ORDER BY `id` ASC");

    while( $row = $result->fetch_assoc() ) {
        array_push($players, $row["team1"]);
        array_push($players, $row["team2"]);
    }

    $index = array_search($user_id, $players);
    $size = count($players);

    //For Previous Player.
    if($index == 0){
        array_push($retData, $players[$size-1]);
    }else{
        array_push($retData, $players[$index-1]);
    }

    //For Next Player.
    if( $size-1 != $index ){
        array_push($retData, $players[$index+1]);
    }else if( $size-1 == $index ){
        array_push($retData, $players[0]);
    }

    return $retData;
}

//Get a list of all joined users info. (according to sequence)
function GetJoinedUsersId($conn ,$room_id, $user_id){

    $allUserId = array();
    $finalarray = array();
    $result = mysqli_query($conn,"SELECT * FROM tbl_buracoteam WHERE room_id='".$room_id."' AND status=1");

    while($row = mysqli_fetch_assoc($result)){

        array_push($allUserId, $row['team1']);
        array_push($allUserId, $row['team2']);
    }

    $userIndex = array_search($user_id, $allUserId);
    $rearrUserId = array_unique(array_merge(array_slice($allUserId, $userIndex), $allUserId));
    
    foreach($rearrUserId as $value){

        $query = $conn->query("SELECT tbl_registration.user_id,tbl_registration.user_name, tbl_registration.Img FROM tbl_registration WHERE user_id='".$value."'");
        $rowcount = $query->fetch_assoc();
        $rowcount['Img']=($rowcount['Img']) ? IMG_URL.$rowcount['Img'] : 'null';
        $finalarray[] = $rowcount;
    }

    return $finalarray;
}

function CalculatePoints($cards){

    $card_points = array('2'=>10,'3'=>5,'4'=>5,'5'=>5,'6'=>5,'7'=>5,'8'=>10,'9'=>10, '0'=>10,'J'=>10,'Q'=>10,'K'=>10,'A'=>15,'X'=>10);
    $points = 0;

    foreach ($cards as $val) {
        $points += $card_points[substr(trim($val), 0, -1)];
    }

    return $points;
}

function HasJokerCard($cards){

    $ret = FALSE;
    $cardType = 'X';

    foreach($cards as $val){
    
    	if(substr($val, 0, -1) == $cardType){
        	$ret = TRUE;
            break;
        }
    }

    return $ret;
}

function HasAll2Cards($cards){

    $ret = TRUE;
    $cardType = '2';

    foreach($cards as $val){
    
    	if((substr($val, 0, -1) != 'X') && (substr($val, 0, -1) != $cardType)){
            $ret = FALSE;
            break;
        }
    }

    return $ret;
}

function ConvertCardsIntoNumeric($cards){

    $cardValueMap = array('A'=>1,'2'=>2,'3'=>3,'4' =>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,'0'=>10,'J'=>11,'Q'=>12,'K'=>13);
    $cardNumber = array();

    foreach($cards as $val){
        array_push($cardNumber, $cardValueMap[ substr($val, 0, -1)]);
    }

    return $cardNumber;
}

function GetCardCount($cards, $cardType){

    $count = 0;
    foreach($cards as $val){
        if(substr($val, 0, -1) == $cardType) {
            ++$count;
        }
    }
    return $count;
}

function RemoveCard(&$cards, $cardType){

    foreach($cards as $key => $val) {

        if(substr($val, 0, -1) == $cardType) {
            unset($cards[$key]);
        }
    }
}

function IsSameColourCards($cards){

    $ret = TRUE;
    $cardType = substr(current($cards), -1);

    foreach($cards as $val){
    
    	if(substr($val, -1) != $cardType){
        	$ret = FALSE;
            break;
        }
    }
    return $ret;
}
function checkSequence($cardNum, $jokerCount){

    $ret = TRUE;
    $missingCardcount = 0;

    for($index=0; $index < count($cardNum)-1; ++$index) {
        
        $diff = $cardNum[$index+1] - $cardNum[$index];
        if($diff > 1){
            $missingCardcount += ($diff-1);
        }
    }

    if($missingCardcount > $jokerCount){
        $ret = FALSE;
    }

    return $ret;
}

function IsSequence($cards){

    $jokerCount = GetCardCount($cards, 'X');
    $aceCardCount = GetCardCount($cards, 'A');
    $twoCardCount = GetCardCount($cards, '2');

    if($jokerCount > 0){
        RemoveCard($cards, 'X');
    }

    if($twoCardCount > 0){
        RemoveCard($cards, '2');
    }

    //we are using all '2' cards as joker.
    $jokerCount += $twoCardCount;

    if(IsSameColourCards($cards) == FALSE){
        return FALSE;
    }
    $cardNum = ConvertCardsIntoNumeric($cards);

    if(count($cardNum) != count(array_unique($cardNum))){
        return FALSE;
    }

    sort($cardNum);
    
    if(checkSequence($cardNum, $jokerCount) == TRUE){
        return TRUE;
    }else if($aceCardCount == 1){

        $cardNum[0] = 14;
        sort($cardNum);

        return checkSequence($cardNum, $jokerCount);
    }else{
        return FALSE;
    }
}

function IsPair($cards){

    $ret = FALSE;
    $jokerCount = GetCardCount($cards, 'X');
    $twoCardCount = GetCardCount($cards, '2');

    if($jokerCount > 0){
        RemoveCard($cards, 'X');
    }

    if($twoCardCount > 0){
        RemoveCard($cards, '2');
    }

    $cardNum = ConvertCardsIntoNumeric($cards);

    if(count(array_unique($cardNum)) == 1){
        $ret = TRUE;
    }
    return $ret;
}

function ValidatePairOrSequence($cards){

    $ret = FALSE;

    if(IsPair($cards) == TRUE){
        $ret = PAIR;
    }else if(IsSequence($cards) == TRUE){
        $ret = SEQUENCE;
    }
    return $ret;
}

?>
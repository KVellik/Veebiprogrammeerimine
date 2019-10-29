<?php
function storeMessage($myMessage){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpmsg (userid, message) VALUES(?,?)");
	echo $conn->error;
	$stmt -> bind_param("is", $_SESSION["userId"], $myMessage);
	if($stmt->execute()){
		$notice = "Sõnum salvestati!";
	} else {
		$notice = "Sõnumi salvestamisel tekkis tõrge: " .$stmt->error;
	}
	
	
	$stmt -> close();
	$conn -> close();
	return $notice;
}

function readAllMessages(){
	$messagesHTML = "";
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn -> prepare("SELECT message, created FROM vpmsg WHERE deleted IS NULL");
	echo $conn -> error;
	$stmt -> bind_result($messageFromDb, $createdFromDb);
	$stmt -> execute();
	while($stmt -> fetch()){
		$messagesHTML .= "<li>" .$messageFromDb ." Lisatud: " .$createdFromDb ."</li> \n";
	}
	if(!empty($messagesHTML)){
		$messagesHTML = "<ul> \n" .$messagesHTML ."</ul> \n";
	} else {
		$messagesHTML = "<p>Sõnumeid pole!</p> \n";
    }
	$stmt -> close();
	$conn -> close();
	return $messagesHTML;
}



function readAllMessages(){
	$messagesHTML = "";
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	//$stmt = $conn -> prepare("SELECT message, created FROM vpmsg");
	//$stmt = $conn -> prepare("SELECT message, created FROM vpmsg WHERE deleted IS NULL");
	$stmt = $conn -> prepare("SELECT message, created FROM vpmsg WHERE userid = ? AND deleted IS NULL ORDER BY created DESC LIMIT ?");
	echo $conn -> error;
	$stmt -> bind_param("i", $_SESSION["userId"]);
	$stmt -> bind_result($messageFromDb, $createdFromDb);
	$stmt -> execute();
	while($stmt -> fetch()){
		$messagesHTML .= "<li>" .$messageFromDb ." Lisatud: " .$createdFromDb ."</li> \n";
	}
	if(!empty($messagesHTML)){
		$messagesHTML = "<ul> \n" .$messagesHTML ."</ul> \n";
	} else {
		$messagesHTML = "<p>Sõnumeid pole!</p> \n";
    }
	$stmt -> close();
	$conn -> close();
	return $messagesHTML;
}
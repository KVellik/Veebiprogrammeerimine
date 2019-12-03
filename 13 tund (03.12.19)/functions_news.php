<?php
function storeNews($myNews){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpnews (userid, title, content, expire) VALUES(?,?,?,?)");
	echo $conn->error;
	$stmt -> bind_param("isss", $_SESSION["userId"], $newsTitle, $content, $expireDate);
	if($stmt->execute()){
		$notice = "Uudis salvestati";
	} else {
		$notice = "Uudist ei laetud üles " .$stmt->error;
	}
		
	$stmt -> close();
	$conn -> close();
	return $notice;
}

function readAllNews(){
	$newsHTML = "";
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn -> prepare("SELECT title, content, expire, added FROM vpnews WHERE deleted IS NULL");
	echo $conn -> error;
	$stmt -> bind_result($newsTitleFromDb, $contentFromDb, $expireFromDb, $addedFromDb);
	$stmt -> execute();
	while($stmt -> fetch()){
		$newsHTML .= "<li>" .$newsFromDb ." Lisatud: " .$createdFromDb ."Aegub: " .$expireFromDb ."</li> \n";
	}
	if(!empty($newsHTML)){
		$newsHTML = "<ul> \n" .$newsHTML ."</ul> \n";
	} else {
		$newsHTML = "<p>Uudiseid pole!</p> \n";
	}

	$stmt -> close();
	$conn -> close();
	return $newsHTML;
}
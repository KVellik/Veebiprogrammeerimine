<?php
function storeNews($newsTitle, $news, $expireDate){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpnews (userid, title, content, expire) VALUES(?,?,?,?)");
	echo $conn->error;
	$stmt -> bind_param("isss", $_SESSION["userId"], $newsTitle, $news, $expireDate);
	if($stmt->execute()){
		$notice = "Uudis salvestati";
		
	} else {
		$notice = "Uudist ei laetud üles ";		
	}			
	$stmt -> close();
	$conn -> close();
	return $notice;
}

function readNews(){
	$newsHTML = null;
	$today = date("Y-m-d");
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("SELECT title, content, added FROM vpnews WHERE expire >=? AND deleted IS NULL ORDER BY id DESC LIMIT");
	echo $conn->error;
	$stmt->bind_param("s", $today);
	$stmt->bind_result($titleFromDb, $contentFromDb, $addedFromDb);
	$stmt->execute();
	while ($stmt->fetch()){
		$newsHTML .= "<div> \n";
		$newsHTML .= "\t <h3>" .$titleFromDb ."</h3> \n";
		$addedTime = new DateTime($addedFromDb);
		//$newsHTML .= "\t <p>(Lisatud: " .$addedFromDb .")</p> \n";
		$newsHTML .= "\t <p>(Lisatud: " .$addedTime->format("d.m.Y H:i:s") .")</p> \n";
		$newsHTML .= "\t <div>" .htmlspecialchars_decode($contentFromDb) ."</div> \n";
		$newsHTML .= "</div> \n";		
	}
	if($newsHTML == null){
			$newsHTML = "<p>Kahjuks uudiseid pole!</p>";
	}

	$stmt -> close();
	$conn -> close();
	return $newsHTML;
}
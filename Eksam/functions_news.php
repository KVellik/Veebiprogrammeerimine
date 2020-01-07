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

function storeNewsPics($fileName){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpnewsphotos (userid, filename) VALUES(?,?)");
	echo $conn->error;
	$stmt -> bind_param("is", $_SESSION["userId"], $fileName);
	if($stmt->execute()){
		$notice = "Pilt salvestati";
		
	} else {
		$notice = "Pilti ei salvestatud ";		
	}			
	$stmt -> close();
	$conn -> close();
	return $notice;
}	

function readNews(){
	$newsHTML = null;
	$today = date("Y-m-d");
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("SELECT title, content, added FROM vpnews WHERE expire >=? AND deleted IS NULL");
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
		$newsHTML .= '<img class="thumbs" src="' .$GLOBALS["pic_upload_dir_thumb"];
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

function showNewsPics(){
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("SELECT filename FROM vpnewsphotos WHERE expire >=? AND deleted IS NULL");
	echo $conn->error;
	$stmt->bind_param("s", $filename);
	$stmt->bind_result($fileNameFromDb);
	$stmt->execute();
	$stmt->fetch();
	$stmt -> close();
	$conn -> close();
}
<?php
	//saame saadetud väärtuse(d)
	$rating = $_REQUEST["rating"];
	
	echo $rating * 2;
	
	function storePhotoRating($rating){
		$notice = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("INSERT INTO vpphotorating (userid, photoid, rating) VALUES (?, ?, ?)");
		echo $conn->error;
		$stmt->bind_param("iii", $_SESSION["userId"], $photoid, $rating);
		if($stmt->execute()){
			if($stmt->execute()){
			$notice = " Pildi hinnang salvestati!";
		} else {
			$notice = " Pildi hinnangu salvestamisel tekkis probleem! " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		return $notice;
		}	

	}
	
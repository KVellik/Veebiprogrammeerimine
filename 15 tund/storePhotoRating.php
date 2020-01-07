<?php
	//saame saadetud väärtuse(d)
	$rating = $_REQUEST["rating"];
	$photoId = $_REQUEST["photoid"];
	
	//sessioonihaldus
  require("Classes/Session.class.php");
  SessionManager::sessionStart("vp", 0, "/~karlvel/", "greeny.cs.tlu.ee");
	
	require("../../../config_vp2019.php");
	require("functions_user.php");
	$database = "if19_karl_ve_1";
	
	

	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpphotorating (userid, photoid, rating) VALUES (?, ?, ?)");
	$stmt->bind_param("iii", $_SESSION["userId"], $photoid, $rating);
	$stmt->execute();
	$stmt->close();
	//küsime uue keskmise hinde
	$stmt=$conn->prepare("SELECT AVG(rating)FROM vpphotoratings WHERE photoid=?");
	$stmt->bind_param("i", $photoId);
	$stmt->bind_result($score);
	$stmt->execute();
	$stmt->fetch();
	$stmt->close();
	$conn->close();
	//ümardan keskmise hinde kaks kohta pärast koma ja tagastan
	echo round($score, 2);

	
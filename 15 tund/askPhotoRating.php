<?php
	//võtame vastu saadetud info
	$photoId = $_REQUEST["photoid"];
	require("../../../config_vp2019.php");
	//require("functions_user.php");
	//sessioonihaldus
	require("classes/Session.class.php");
	SessionManager::sessionStart("vp", 0, "/~karlvel/", "greeny.cs.tlu.ee");
	
	$database = "if19_karl_ve_1";
	
	$conn = new mysqli($serverHost, $serverUsername, $serverPassword, $database);
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
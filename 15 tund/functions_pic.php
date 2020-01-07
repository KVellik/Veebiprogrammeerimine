<?php
	function addPicData($fileName, $altText, $privacy){
		$notice = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("INSERT INTO vpphotos (userid, filename, alttext, privacy) VALUES (?, ?, ?, ?)");
		echo $conn->error;
		$stmt->bind_param("issi", $_SESSION["userId"], $fileName, $altText, $privacy);
		if($stmt->execute()){
			$notice = " Pildi andmed salvestati andmebaasi!";
		} else {
			$notice = " Pildi andmete salvestamine ebaõnnestus tehnilistel põhjustel! " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		return $notice;
	}
	
	function readgalleryImages($privacy, $page, $limit){
		$html = null;
		$skip = ($page - 1) * $limit;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		//$stmt = $conn->prepare("SELECT filename, alttext FROM vpphotos WHERE privacy <=? AND deleted IS NULL ORDER BY id DESC LIMIT ?, ?");
		$stmt = $conn->prepare("SELECT vpphotos.id, vpusers.firstname, vpusers.lastname, vpphotos.filename, vpphotos.alttext, AVG(vpphotoratings.rating) as AvgValue FROM vpphotos JOIN vpusers ON vpphotos.userid = vpusers.id LEFT JOIN vpphotoratings ON vpphotoratings.photoid = vpphotos.id WHERE vpphotos.privacy <= ? AND deleted IS NULL GROUP BY vpphotos.id DESC LIMIT ?, ?");
		echo $conn->error;
		$stmt->bind_param("iii", $privacy, $skip, $limit);
		$stmt->bind_result($idFromDb, $firstNameFromDb, $lastNameFromDb, $fileNameFromDb, $altTextFromDb, $avgFromDb);
		$stmt->execute();
		while($stmt->fetch()){
			//<img src="kataloog/pildifail" alt="tekst" data-fn="failinimi">
		$html .= '<div class="thumbGallery">' ."\n";
			$html .= '<img class="thumbs" src="' .$GLOBALS["pic_upload_dir_thumb"] .$fileNameFromDb .'" alt="';
			if(empty($altTextFromDb)){
				$html .= "Illustreeriv foto";
			} else {
				$html .= $altTextFromDb;
			}
			$html .= '" data-fn="' .$fileNameFromDb .'"';
			$html .= ' data-id="' .$idFromDb .'"';
			$html .= '>' ."\n";
			$html .= "<p>" .$firstNameFromDb ." " .$lastNameFromDb ."</p> \n";
			$html .= '<p id="score' .$idFromDb .'">';
			if($avgFromDb == 0){
				$html .="Pole hinnatud";
			} else {
				$html .= "Hinne: " .round($avgFromDb, 2);
			}
			$html .= "</p> \n";
			$html .= "</div>";
		}
		if($html == null){
			$html = "<p>Kahjuks avalikke pilte pole!</p>";
		}
		$stmt->close();
		$conn->close();
		return $html;
		
	}
	
	function countPics($privacy){
		$notice = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("SELECT COUNT(id) FROM vpphotos WHERE privacy <=? AND deleted IS NULL");
		echo $conn->error;
		$stmt->bind_param("i", $privacy);
		$stmt->bind_result($count);
		$stmt->execute();
		$stmt->fetch();
		$notice = $count;
		
		$stmt->close();
		$conn->close();
		return $notice;
	}
	
	function countMyImages(){
		$notice = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("SELECT COUNT(id) FROM vpphotos WHERE userid <= ? AND deleted IS NULL");
		echo $conn->error;
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result($imageCountFromDb);
		$stmt->execute();
		if($stmt->fetch()){
			$notice = $imageCountFromDb;
		} else {
			$notice = 0;
		}
		
		$stmt->close();
		$conn->close();
		return $notice;
	}
	
	function readuserPicsPage($page, $limit){
		$picHTML = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("SELECT id, filename, alttext FROM vpphotos WHERE userid=? AND deleted IS NULL ORDER BY id DESC LIMIT ?,?");
		echo $conn->error;
		$skip = ($page - 1) * $limit;
		$stmt->bind_param("iii", $_SESSION["userId"], $skip, $limit);
		$stmt->bind_result($idFromDb, $fileNameFromDb, $altTextFromDb);
		$stmt->execute();
		while($stmt->fetch()){
			//<img src="thumbs_kataloog/pilt" alt=""> \n
			//<img src="thumbs_kataloog/pilt" alt="" data-fn="failinimi"> \n
			$picHTML .= '<div class="thumbGallery">' ."\n";
			$picHTML .= '<img class="thumbs" src="' .$GLOBALS["pic_upload_dir_thumb"] .$fileNameFromDb .'" alt="';
			if(empty($altTextFromDb)){
				$picHTML .= "Illustreeriv foto";
			} else {
				$picHTML .= $altTextFromDb;
			}
			$picHTML .= '" data-fn="' .$fileNameFromDb .'"';
			$picHTML .= ' data-id="' .$idFromDb .'"';
			$picHTML .= '>' ."\n";
			$picHTML .= '<a href="edituserpic.php?photoid=' .$idFromDb .'&return=' .$page .'">Muuda/Kustuta</a>' ."\n";
			$picHTML .= "</div>";
		}
		if($picHTML == null){
			$picHTML = "<p>Kahjuks Sinu üleslaetud pilte ei leitud!</p>";
		}
		
		$stmt->close();
		$conn->close();
		return $picHTML;
	}
	
	function readuserPicToEdit($photoid){
		$picHTML = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("SELECT filename, alttext FROM vpphotos WHERE id=? AND userid=?");
		echo $conn->error;
		$stmt->bind_param("ii", $photoid, $_SESSION["userId"]);
		$stmt->bind_result($fileNameFromDb, $altTextFromDb);
		$stmt->execute();
		if($stmt->fetch()){
			$picHTML .= '<img src="' . $GLOBALS["pic_upload_dir_w600"] .$fileNameFromDb .'" alt="' .$altTextFromDb .'">' ."\n";
			$picHTML .= "<br> \n";
			$picHTML .= '<textarea name="altText">' .$altTextFromDb .'</textarea>' ."\n";
		}
		$stmt->close();
		$conn->close();
		return $picHTML;
	}
	
	//"UPDATE vpuserprofiles3 SET description = ?, bgcolor = ?, txtcolor = ? WHERE userid = ?"
	function changePicInfo($picid, $altText){
		$notice = null;
		//echo "Muuda: " .$altText;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("UPDATE vpphotos SET alttext = ? WHERE id = ?");
		$stmt->bind_param("si", $altText, $picid);
		echo $conn->error;
		if($stmt->execute()){
			$notice = "Muudetud!";
		} else {
			$notice = "Muutmisel tekkis tehniline viga: " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		return $notice;
	}
	
	function deletePic($picid, $return){
		//echo "Kustuta: " .$picid;
		$notice = null;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("UPDATE vpphotos SET deleted = NOW() WHERE id = ?");
		$stmt->bind_param("i", $picid);
		echo $conn->error;
		if($stmt->execute()){
			$notice = "Kustutatud!";
		} else {
			$notice = "Kustutamisel tekkis tehniline viga: " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		if($notice == "Kustutatud!"){
			header("Location: userpics.php?page=" .$return);
			exit();
		}
		return $notice;
	}
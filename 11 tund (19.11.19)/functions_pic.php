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
			$notice = " Pildi andmete salvestamine ebaönnestus tehnilistel põhjustel! " .$stmt->error;
		}
		$stmt->close();
		$conn->close();
		return $notice;
	}
	
	function readgalleryImages($privacy, $page, $limit){
		$html = null;
		$skip = ($page - 1) * $limit;
		$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $conn->prepare("SELECT filename, alttext FROM vpphotos WHERE privacy <=? AND deleted IS NULL ORDER BY id DESC LIMIT ?, ?");
		echo $conn->error;
		$stmt->bind_param("iii", $privacy, $skip, $limit);
		$stmt->bind_result ($fileNameFromDb, $altTextFromDb);
		$stmt->execute();
		while($stmt->fetch()){
			//<img src="kataloog/pildifail" alt="tekst" data-fn="failinimi">
		$html .= '<img src="' .$GLOBALS["pic_upload_dir_thumb"] .$fileNameFromDb .'" alt="';
			
			if($altTextFromDb == null){
				$html .= "Foto";
			} else {
				$html .= $altTextFromDb;
			}
			$html .= '" data-fn="' .$fileNameFromDb .'">' ."\n";
		}
		if($html == null){
			$html = "<p>Kahjuks avalikke pilte ei leitud!</p>";
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
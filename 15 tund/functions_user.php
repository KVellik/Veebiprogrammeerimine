<?php

//Võtan kasutusele sessiooni
//session_start();
//var_dump($_SESSION);

function signUp($name, $surname, $email, $gender, $birthDate, $password){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("INSERT INTO vpusers (firstname, lastname, birthdate, gender, email, password) VALUES(?, ?, ?, ?, ?, ?)");
	echo $conn->error;
	$options = ["cost" => 12, "salt" => substr(sha1(rand()), 0, 22)];
	$pwdhash = password_hash($password, PASSWORD_BCRYPT, $options);
	$stmt->bind_param("sssiss", $name, $surname, $birthDate, $gender, $email, $pwdhash);
	if($stmt->execute()){
		$notice = "Kasutaja loomine õnnestus!";
	
	} else {
		$notice = "Kasutaja loomisel tekkis tehniline viga: " .$stmt->error;
	
	}
	$stmt -> close();
	$conn -> close();
	return $notice;
}

  function signIn($email, $password){
	$notice = "";
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("SELECT password FROM vpusers WHERE email=?");
	echo $conn->error;
	$stmt->bind_param("s", $email);
	$stmt->bind_result($passwordFromDb);
	if($stmt->execute()){
		//kui päring õnnestus
	  if($stmt->fetch()){
		//kasutaja on olemas
		if(password_verify($password, $passwordFromDb)){
		  //kui salasõna klapib
		  $stmt->close();
		  $stmt = $conn->prepare("SELECT id, firstname, lastname FROM vpusers WHERE email=?");
		  echo $conn->error;
		  $stmt->bind_param("s", $email);
		  $stmt->bind_result($idFromDb, $firstnameFromDb, $lastnameFromDb);
		  $stmt->execute();
		  $stmt->fetch();
		  $notice = "Sisse logis " .$firstnameFromDb ." " .$lastnameFromDb ."!";
		  
		  //annan sessioonimuutujatele väärtused
		  $_SESSION["userId"] = $idFromDb;
		  $_SESSION["userFirstname"] = $firstnameFromDb;
		  $_SESSION["userLastname"] = $lastnameFromDb;
		  
		  //loeme kasutajaprofiili
		  $stmt->close();
		  $stmt = $conn->prepare("SELECT bgcolor, txtcolor FROM vpuserprofiles WHERE userid=?");
		  echo $conn->error;
		  $stmt->bind_param("i", $_SESSION["userId"]);
		  $stmt->bind_result($bgColorFromDb, $txtColorFromDb);
		  $stmt->execute();
		  if($stmt->fetch()){
			$_SESSION["bgColor"] = $bgColorFromDb;
	        $_SESSION["txtColor"] = $txtColorFromDb;
		  } else {
		    $_SESSION["bgColor"] = "#FFFFFF";
	        $_SESSION["txtColor"] = "#000000";
		  }
		  
		  //kuna siirdume teisele lehele, sulgeme andmebaasi ühendused
		  $stmt->close();
		  $conn->close();
		  //siirdume teisele lehele
		  header("Location: home.php");
		  //katkestame edasise tegevuse siin
		  exit();
		  		  
		} else {
		  $notice = "Vale salasõna!";
		}
	  } else {
		$notice = "Sellist kasutajat (" .$email .") ei leitud!";  
	  }
	} else {
	  $notice = "Sisselogimisel tekkis tehniline viga!" .$stmt->error;
	}
	
	$stmt->close();
	$conn->close();
	return $notice;
  }//sisselogimine lõppeb
  
  //kasutajaprofiili salvestamine
  function storeuserprofile($description, $bgColor, $txtColor){
	$notice = "";
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
    $stmt = $conn->prepare("SELECT id FROM vpuserprofiles WHERE userid=?");
	echo $conn->error;
	$stmt->bind_param("i", $_SESSION["userId"]);
	$stmt->bind_result($idFromDb);
	$stmt->execute();
	if($stmt->fetch()){
		//profiil juba olemas, uuendame
		$stmt->close();
		$stmt = $conn -> prepare("UPDATE vpuserprofiles SET description = ?, bgColor = ?, txtColor = ? WHERE userid = ?");
		echo $conn -> error;
		$stmt -> bind_param("sssi", $description, $bgColor, $txtColor, $_SESSION["userId"]);
		if($stmt -> execute()){
			$notice = "Profiil on eduaklt uuendatud!";
			$_SESSION["bgColor"] = $bgColor;
	        $_SESSION["txtColor"] = $txtColor;
		} else {
			$notice = "Profiili uuendamisel tekkis tõrge! " .$stmt -> error;
		}
	} else {
		//profiili pole, salvestame
		$stmt->close();
		$stmt = $conn->prepare("INSERT INTO vpuserprofiles (userid, description, bgcolor, txtcolor) VALUES(?,?,?,?)");
		echo $conn->error;
		$stmt->bind_param("isss", $_SESSION["userId"], $description, $bgColor, $txtColor);
		if($stmt->execute()){
			$notice = "Profiil edukalt salvestatud!";
		} else {
			$notice = "Profiili salvestamisel tekkis tõrge! " .$stmt->error;
		}
	}
	$stmt->close();
	$conn->close();
	return $notice;
  }
  
  function showMyDesc(){
	$notice = null;
	$conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	$stmt = $conn->prepare("SELECT description FROM vpuserprofiles WHERE userid=?");
	echo $conn->error;
	$stmt->bind_param("i", $_SESSION["userId"]);
	$stmt->bind_result($descriptionFromDb);
	$stmt->execute();
    if($stmt->fetch()){
	  $notice = $descriptionFromDb;
	}
	$stmt->close();
	$conn->close();
	return $notice;
  }
  
  function updatePassword($oldPassword, $newPassword){
	  //kas vana parool on õige
	  $notice = "";
	  $conn = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	  $stmt = $conn->prepare("SELECT password FROM vpusers WHERE id=?");
	  echo $conn->error;
	  $stmt->bind_param("i", $_SESSION["userId"]);
	  $stmt->bind_result($passwordFromDb);
	  if($stmt->execute()){
		//kui päring õnnestus
	    if($stmt->fetch()){
		  //kasutaja on olemas
		  if(password_verify($oldPassword, $passwordFromDb)){
			  $stmt->close();
			  $stmt = $conn->prepare("UPDATE vpusers SET password = ? WHERE id = ?");
			  //valmistame parooli salvestamiseks ette
			  $options = ["cost" => 12, "salt" => substr(sha1(rand()), 0, 22)];
			  $pwdhash = password_hash($newPassword,PASSWORD_BCRYPT, $options );
			  $stmt->bind_param("si", $pwdhash, $_SESSION["userId"]);
			  if($stmt->execute()){
				$notice = "Uue parooli salvestamine õnnestus!";
			  } else {
				$notice = "Parooli salvestamisel tekkis tehniline viga: " .$stmt->error;
			  }
		  } else {
			  $notice = "Sisestasite vale parooli! Proovige uuesti!";
		  }
			  
		} else {
			$notice = "Tekkis viga (valke kasutaja id)!";
		}
	  }
		$stmt->close();
		$conn->close();
	  return $notice;
  }
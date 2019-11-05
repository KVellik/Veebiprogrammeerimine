<?php
  require("../../../config_vp2019.php");
  require("functions_main.php");
  require("functions_user.php");
  require("functions_pic.php");
  //require("Classes/Test.class.php");
  require("Classes/Picupload.class.php");
  $database = "if19_karl_ve_1";
  
  //kui pole sisseloginud
  if(!isset($_SESSION["userId"])){
	  //siis jõuga sisselogimise lehele
	  header("Location: page.php");
	  exit();
  }
  
  //väljalogimine
  if(isset($_GET["logout"])){
	  session_destroy();
	  header("Location: page.php");
	  exit();
  }
  
  //$myTest = new Test(20);
  //echo $myTest->privateNumber;
  //echo $myTest->tellPublicSecret();
  //unset($myTest);
  //echo $myTest->tellPublicSecret();
  
  $userName = $_SESSION["userFirstname"] ." " .$_SESSION["userLastname"];
  
  $notice = null;
  $maxPicW = 600;
  $maxPicH = 400;
  
  //var_dump($_POST);
  //var_dump($_FILES);
  //________________________________________________________________________________________
  
  //pildi üleslaadimise osa
	//$target_dir = "uploads/";
	//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	
	$uploadOk = 1;
	
	// Check if image file is a actual image or fake image
	if(isset($_POST["submitPic"])) {
		
		//$target_file = $pic_upload_dir_orig . basename($_FILES["fileToUpload"]["name"]);
		//$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
		$filename = "vp_";
		$timeStamp = microtime(1) * 10000;
		$filename .= $timeStamp ."." .$imageFileType;
		$target_file = $pic_upload_dir_orig .$filename;
		
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			//echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
	
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "Sorry, file already exists.";
			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 2500000) {
			echo "Sorry, your file is too large.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
			echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			
			//kasutame klassi
			$myPic = new Picupload($_FILES["fileToUpload"]["tmp_name"], $imageFileType);
			
			//teeme pildi väiksemaks
			$myPic->resizeImage($maxPicW, $maxPicH);
			//lisame vesimärgi
			$myPic->addWatermark("../vp_pics/vp_logo_w100_overlay.png");
			//kirjutame vähendadud pildi faili
			$notice .= $myPic->savePicFile($pic_upload_dir_w600 .$filename);
			unset($myPic);
			
			
				
				
			
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				echo "Sorry, there was an error uploading your file.";
			}
		}
	
	}//kas nuppu klikiti
  
  //________________________________________________________________________________________
  
  require("header.php");
?>

  <?php
    echo "<h1>" .$userName ." koolitöö leht</h1>";
  ?>
  <p>See leht on loodud koolis õppetöö raames
  ja ei sisalda tõsiseltvõetavat sisu!</p>
  <hr>
  <p><a href="?logout=1">Logi välja!</a></p>
  <p>Tagasi <a href="home.php">avalehele</a></p>
  
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
	  <label>Vali üleslaetav pildifail!</label><br>
	  <input type="file" name="fileToUpload" id="fileToUpload">
	  <br>
	  <label>Alt tekst: </label><input type="text" name="altText">
	  <br>
	  <label>Privaatsus</label>
	  <br>
	  <input type="radio" name="privacy" value="1"><label>Avalik</label>&nbsp;
	  <input type="radio" name="privacy" value="2"><label>Sisseloginud kasutajatele</label>&nbsp;
	  <input type="radio" name="privacy" value="3" checked><label>Isiklik</label>
      <br>
	  <input name="submitPic" type="submit" value="Lae pilt üles!"><span><?php echo $notice; ?></span>
	</form>
	<hr>
	  
</body>
</html>
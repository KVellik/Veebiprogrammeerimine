<?php
  require("../../../config_vp2019.php");
  require("functions_main.php");
  require("functions_user.php");
  require("functions_news.php");
  require("functions_pic.php");
  require("Classes/Picupload.class.php");
  $database = "if19_karl_ve_1";
  
  $error = "";
  $newsTitle = "";
  $news = "";
  $expireDate = date("Y-m-d");
  $notice = "";
  $filename = "";
  
  $fileSizeLimit = 2500000;
  $maxPicW = 600;
  $maxPicH = 400;
  $fileNamePrefix = "vp_";
  $thumbW = 100;
  $thumbH = 100;
  $notice = null;
  
  //sessioonihaldus
  require("Classes/Session.class.php");
  SessionManager::sessionStart("vp", 0, "/~karlvel/", "greeny.cs.tlu.ee");
  
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
 
  $userName = $_SESSION["userFirstname"] ." " .$_SESSION["userLastname"];

  $toScript = "\t" .'<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>' ."\n";
  $toScript .= "\t" .'<script>tinymce.init({selector:"textarea#newsEditor", plugins: "link", menubar: "edit",});</script>' ."\n";
  
  //kui vajutatakse "Salvesta" nuppu
  if(isset($_POST["submitNews"])){
		//var_dump($_POST);
		if(strlen($_POST["newsTitle"]) == 0){
			$error .= "Uudise pealkiri on puudu!";
		}
		if(strlen($_POST["newsEditor"]) == 0){
			$error .= "Uudise sisu on puudu! ";
		}
		if($_POST["expiredate"] >= $expireDate){
			//echo "TULEVIKUS";
			$expireDate = $_POST["expiredate"];
		}
		
		$newsTitle = test_input($_POST["newsTitle"]);
		$news = test_input($_POST["newsEditor"]);
		if($error == ""){
			/*$notice = "Uudis salvestatud!";
			$error = $notice;
			echo $_POST["expiredate"];*/
			$result = storeNews($newsTitle, $news, $expireDate);
			$result2 = storeNewsPics($filename);
			if($result == 1){
				$notice = "Uudis salvestatud!";
				$error = "";
				$newsTitle = "";
				$news = "";
				$expireDate = date("Y-m-d");
			}
		}
	}

  if(isset($_POST["submitNews"])){
	  //kasutame klassi (saadame kogu info üleslaetava faili kohta ja faili mahu piiri
	  $newsPic = new Picupload($_FILES["fileToUpload"], $fileSizeLimit);
	  if($newsPic->error == null){
		//loome failinime
		$newsPic->createFileName($fileNamePrefix);
		//teeme pildi väiksemaks
		$newsPic->resizeImage($maxPicW, $maxPicH);
		//kirjutame vähendatud pildi faili
		$notice .= $newsPic->savePicFile($news_picture_dir_w600 .$newsPic->fileName);
		//thumbnail
		$newsPic->resizeImage($thumbW, $thumbH);
		$newsPic->savePicFile($news_picture_dir_thumb .$newsPic->fileName);
		//salvestan originaali
		$notice .= " " .$newsPic->saveOriginal($news_picture_dir_orig .$newsPic->fileName);
		//salvestan info andmebaasi
		$notice .= storeNewsPics($newsPic->fileName);
	} else {
		//1 - pole pildifail, 2 - liiga suur, pole lubatud tüüp
				if($myPic->error == 1){
					$notice = "Üleslaadimiseks valitud fail pole pilt!";
				}
				if($myPic->error == 2){
					$notice = "Üleslaadimiseks valitud fail on liiga suure failimahuga!";
				}
				if($myPic->error == 3){
					$notice = "Üleslaadimiseks valitud fail pole lubatud tüüpi (lubatakse vaid jpg, png ja gif)!";
				}
	}
} 
    //if(isset($_POST["submitNews"])){
	//$news = test_input($_POST["submitNews"]);
	//if(!empty($news)){
		//$notice = storeNews($news, $newsTitle, $expireDate);
	//} else {
		//$notice = "Tühja uudist ei salvestata!";
	//}
  //}

  require("header.php");
?>

  <?php
    echo "<h1>" .$userName ." koolitöö leht</h1>";
  ?>
  <p>See leht on loodud koolis õppetöö raames
  ja ei sisalda tõsiseltvõetavat sisu!</p>
  <hr>
  <p><a href="?logout=1">Logi välja!</a>
  <p>Tagasi <a href="home.php">avalehele</a></p>

<h2>Lisa uudis</h2>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<label>Uudise pealkiri:</label><br><input type="text" name="newsTitle" id="newsTitle" style="width: 100%;" value="<?php echo $newsTitle; ?>"><br>
		<label>Uudise sisu:</label><br>
		<textarea name="newsEditor" id="newsEditor"><?php echo $news; ?></textarea>
		<br>
		<label>Uudis nähtav kuni (kaasaarvatud)</label>
		<input type="date" name="expiredate" required pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value="<?php echo $expireDate; ?>">
		<label>Vali üleslaetav pildifail!</label><br>
	    <input type="file" name="fileToUpload" id="fileToUpload">
	    <br>
		<br>
		<input name="submitNews" id="submitNews" type="submit" value="Salvesta uudis!"> <span>&nbsp;</span><span><?php echo $error; ?></span>
	</form>	
</body>
</html>
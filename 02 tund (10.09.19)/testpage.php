<?php
  $userName = "Karl Vellik";
  $fullTimeNow = date("d.m.Y H:i:s");
  $hourNow = date("H");
  $partOfDay = "hägune aeg";
  
  if($hourNow < 8){
	  $partOfDay = "hommik";

  }	  
?>

<!DOCTYPE html>
<html lang="et">
<head>
  <meta charset="UTF-8"/>
  <title>
  <?php
  echo $userName; 
  ?>  
  programeerib veebi</title>

</head>
<body>
  <?php
  echo "<h1>" .$userName .", veebiprogrammeerimine</h1>";
  ?>
  <p>See veebileht on loodud õppetöö käigus ning ei oma mingit tõsist sisu!</p>
  <?php
  echo "<p>Lehe avamise hektel oli aeg: " .$fullTimeNow .", " .$partOfDay .".</p>";
  ?>
</body>
</html>
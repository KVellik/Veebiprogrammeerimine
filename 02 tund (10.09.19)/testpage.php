<?php
  $userName = "Karl Vellik";
  $fullTimeNow = date("d.m.Y H:i:s");
  $hourNow = date("H");
  $partOfDay = "h�gune aeg";
  
  if($hourNow < 8){
	  $partOfDay = "hommik";
  if($hourNow > 8 and $hourNow < 12)
	  $partOfDay = "hiline hommik";
  if($hourNow > 12 and $hourNow < 18)
	  $partOfDay = "p�ev";
  if($hourNow > 18 and $hourNow < 23.59)
	  $partOfDay = "�htu";
  if($hourNow > 23.59 and $hourNow < 8)
	  $partOfDay = "��";
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
  <p>See veebileht on loodud �ppet�� k�igus ning ei oma mingit t�sist sisu!</p>
  <?php
  echo "<p>Lehe avamise hektel oli aeg: " .$fullTimeNow .", " .$partOfDay .".</p>";
  ?>
</body>
</html>
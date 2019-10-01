<?php
  require("../../../config_vp2019.php");
  require("functions_film.php");
  $userName = "Karl Vellik";
  $database = "if19_karl_ve_1";
  
  
  $filmInfoHTML = readAllFilms();
  
  require("header.php");
  echo "<h1>" .$userName .", veebiprogrammeerimine</h1>";
  ?>
  <p>See veebileht on loodud õppetöö käigus ning ei oma mingit tõsist sisu!</p>
  <hr>
  <h2>Eesti filmid</h2>
  <p>Praegu meie andmebaasis on järgmised filmid:</p>
  <?php
    echo $filmInfoHTML;
  ?>
  
</body>
</html>
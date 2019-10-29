<?php
 function setPicSize($myTempImage, $imageW, $imageH, $imageNewW, $imageNewH){
	$myNewImage = imagecreatetruecolor($imageNewW, $imageNewH);
	imagecopyresampled($myNewImage, $myTempImage, 0, 0, 0, 0, $imageNewW, $imageNewH, $imageW, $imageH);
	return $myNewImage;
 }
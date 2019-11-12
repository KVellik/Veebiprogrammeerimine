<?php
class Test {
	//muutujad ehk properties
	private $privateNumber;
	public $publicNumber;
	//funktsioonid ehk methods
	//constructor, see on funktsioon, mis käivitub üks kord, klassi kasutusele võtmisel
	function __construct($sentNumber){
		$this->privateNumber = 72;
		$this->publicNumber = $sentNumber;
		echo "Salajase ja avaliku arvu korrutis on: " .$this->privateNumber * $this->publicNumber;
		$this->tellSecret();
	}	
	
	//destructor, käivitatakse, kui klass eemaldatakse, enam ei kasutata
	function __destruct(){
		echo "Klass lõpetas tegevuse!";
	}	
	
	private function tellSecret(){
		echo "Salajane number on: " .$this->privateNumber;
	}
	
	public function tellPublicSecret(){
		echo "Salajane number on tõesti: " .$this->privateNumber;
	}	

}//class lõppeb
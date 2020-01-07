let modal;
let modalImg;
let captionText;
let photoDir = "../picuploadw600h400/";
let photoId;

window.onload = function(){
	modal = document.getElementById("myModal");
	modalImg = document.getElementById("modalImg");
	captionText = document.getElementById("caption");
	let allThumbs = document.getElementById("gallery").getElementsByTagName("img");
	let thumbCount = allThumbs.length;
	for(let i = 0; i < thumbCount; i ++){
		allThumbs[i].addEventListener("click", openModal);
	}
	document.getElementById("close").addEventListener("click", closeModal);
	modalImg.addEventListener("click",closeModal);
	}

function askRating(){
	//AJAX
	let webRequest = new XMLHttpRequest();
	webRequest.onreadystatechange = function(){
		if(this.readyState == 4 && this.status == 200){
			//console.log("Edu: " + this.responseText);
			//document.getElementById("avgRating").innerHTML = "Keskmine hinne: " + this.responseText;
			if (this.responseText == 0){
			document.getElementById("avgRating").innerHTML = "Seda pilti pole hinnatud!";
			} else {
				document.getElementById("avgRating").innerHTML = "Selle pildi keskmine hinne on " + this.responseText;
			}
		}
	}
	webRequest.open("GET", "askPhotoRating.php?photoid=" + photoId, true);
	webRequest.send();
}

function openModal(e){
	//console.log(e);
	modalImg.src = photoDir + e.target.dataset.fn;
	photoId = e.target.dataset.id;
	captionText.innerHTML = "<p>" + e.target.alt + "</p>";
	askRating();
	modal.style.display = "block";
}

function closeModal(){
	modal.style.display = "none";
}

let modal;
let modalImg;
let captionText;
let photoId;
let photoDir = "../picuploadw600h400/";

window.onload = function (){
	modal = document.getElementById("myModal");
	modalImg = document.getElementById("modalImg");
	captionText = document.getElementById("caption");
	let allThumbs = document.getElementById("gallery").getElementsByTagName("img");
	let thumbCount = allThumbs.length;
	for(let i = 0; i < thumbCount; i ++){
		allThumbs[i].addEventListener("click", openModal);
	}
	document.getElementById("close").addEventListener("click", closeModal);
	document.getElementById("storeRating").addEventListener("click", storeRating);
}

function storeRating(){
	let rating = 0; 
	for(let i = 1; i < 6; i ++){
		if(document.getElementById("rate" + i).checked){
			rating = document.getElementById("rate" + i).value;
		}
	}
	if(raiting > 0){
		//AJAX
		let webRequest = new XMLHttpRequest();
		webRequest.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				console.log(this.response.Text);
			}
		}
		webReguest.open("GET", "storePhotoRating.php?rating=" + rating, true);
		webRequest.send();
	}
}

function openModal(e){
	console.log(e)
	for(let i = 1; i < 6; i ++){
		document.getElementById("rate" + i).checked = false;
	}
	document.getElementById("avgRating"),innerHTML = "";
	modalImg.src = photoDir + e.target.dataset.fn;
	photoId = e.target.dataset.id;
	modalImg.alt = e.target.alt;
	captionText.innerHTML = "<p>" + e.target.alt + "</p>";
	modal.style.display = "block";
}

function closeModal(){
	modal.style.display = "none";
}
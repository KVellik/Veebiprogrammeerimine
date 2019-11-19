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
}

function openModal(e){
	console.log(e)
	modalImg.src = photoDir + e.target.dataset.fn;
	modalImg.alt = e.target.alt;
	modal.style.display = "block";
}

function closeModal(){
	modal.style.display = "none";
}
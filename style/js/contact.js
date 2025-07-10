document.getElementById("contactForm").addEventListener("submit", function(e) {
	e.preventDefault();
	alert("Mesajınızı aldık! Mümkün olan en kısa sürede size dönüş yapacağız.");
	this.reset();
});
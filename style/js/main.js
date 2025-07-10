AOS.init({
	duration: 800,
	once: true
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
	anchor.addEventListener('click', function (e) {
		e.preventDefault();

		document.querySelector(this.getAttribute('href')).scrollIntoView({
			behavior: 'smooth'
		});
	});
});

document.addEventListener('DOMContentLoaded', function() {
	window.addEventListener('scroll', function() {
		var navbar = document.querySelector('.navbar');
		if (window.scrollY > 50) {
			navbar.classList.add('scrolled');
		} else {
			navbar.classList.remove('scrolled');
		}
	});
});
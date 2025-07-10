document.querySelector('.search-faq input').addEventListener('keyup', function() {
	const searchValue = this.value.toLowerCase();
	const accordionItems = document.querySelectorAll('.accordion-item');
	
	accordionItems.forEach(item => {
		const title = item.querySelector('.accordion-button').textContent.toLowerCase();
		const content = item.querySelector('.accordion-body').textContent.toLowerCase();
		
		if (title.includes(searchValue) || content.includes(searchValue)) {
			item.style.display = 'block';
		} else {
			item.style.display = 'none';
		}
	});
});
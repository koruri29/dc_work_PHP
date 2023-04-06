function showError(error, elem) {
	for (let i = 0; i < error.length; i++) {
		p = document.createElement('p');
		p.classList.add('error');
		p.textContent = error[i];
		elem.appendChild(p);
	}
}
function showError(error, elem) {
	if (! error.length == 0) {
		for (let i = 0; i < error.length; i++) {
			p = document.createElement('p');
			p.classList.add('error');
			p.textContent = error[i];
			elem.appendChild(p);
		}
	}
}
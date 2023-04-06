const searchBtn = document.getElementById('search');

searchBtn.addEventListener('click', e => {
	e.preventDefault();
	searchBtn.disabled = true;
	document.form.submit();
});
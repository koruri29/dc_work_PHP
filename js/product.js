const cartInBtn = document.getElementsByClassName('cart-in-btn');
const cartInForms = document.getElementsByClassName('cart-in-form');


for (let i = 0; i < cartInBtn.length; i++) {
	cartInForms[i].addEventListener('click', e => {
		e.preventDefault();
		e.target.disabled = true;
		cartInForms[i].submit();
	});
}

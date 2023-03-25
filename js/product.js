const cartInBtn = document.getElementsByClassName('cart-in-btn');
const cartInForms = document.getElementsByClassName('cart-in-form');
// for (let i = 0; i < cartInForms.length; i++) {
// 	cartInForms[i].method = 'post';
// }
for (let i = 0; i < cartInBtn.length; i++) {
	cartInForms[i].addEventListener('click', e => {
		e.preventDefault();
		e.target.disabled = true;
		
		//inputの値が送られてないようなのでここで後付け
		// const send = document.createElement('input');
		// send.type = 'submit';
		// send.name = 'send';
		// send.value = 'カートに入れる';
		// cartInForms[i].appendChild(send);

		cartInForms[i].submit();
	});
}

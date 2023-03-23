const registerForm = document.getElementById('register');
const updateForm = document.getElementById('update');
const productName = document.getElementById('product-name');
const price = document.getElementById('price');
const qty = document.getElementById('qty');
const publicFlag = document.getElementsByClassName('public-flag');
const qtys = document.getElementsByClassName('qty');
const registerBtn = document.getElementById('register-product');
const updateBtn = document.getElementById('update-product');
const divRegister = document.getElementById('register-error');
const divUpdate = document.getElementById('update-error');
console.log(registerForm);

function validateProduct(error) {
	if (productName.value === '') {
		error.push('商品名が入力されていません。');
	}
	if (price.value === '') {
		error.push('価格が入力されていません。');
	}
	if (isNaN(price.value)) {
		error.push('価格は半角数字で入力してください。');
	}
	if (price.value < 0) {
		error.push('価格は正の整数を入力してください。');
	}
	if (qty.value === '') {
		error.push('在庫数が入力されていません。');
	}
	if (isNaN(qty.value)) {
		error.push('在庫数は半角数字で入力してください。');
	}
	if (qty.value < 0) {
		error.push('在庫数は正の整数を入力してください。');
	}
	// if (publicFlag.value === '') {
	// 	error.push('公開ステータスを選択してください');
	// }
	return error;
}


function validateUpdatedProduct(error) {
	let issetQty = true;
	let isQtyNum = true;
	let isQtyPlus = true;

	for (let i = 0; i < qty.length; i++) {
		if (qty[i].value === '') {
			issetQty = false;
		}
	}
	if (! issetQty) {
		error.push('在庫数が入力されていません。');
	}

	for (let i = 0; i < qty.length; i++) {
		if (isNaN(qty.value)) {
			isQtyNum = false;
		}
	}
	if (! isQtyNum) {
		error.push('在庫数は半角数字で入力してください。');
	}

	for (let i = 0; i < qty.length; i++) {
		if (qty.value < 0) {
			isQtyPlus = false;
		}
	}
	if (! isQtyPlus) {
		error.push('在庫数は正の整数を入力してください。');
	}
	return error;
}


function showRegisterError(error) {
	if (! error.length == 0) {
		for (let i = 0; i < error.length; i++) {
			p = document.createElement('p');
			p.classList.add('error');
			p.textContent = error[i];
			registerBtn.before(p);
		}
	}
}


registerBtn.addEventListener('click', e => {
	e.preventDefault();
	while (divRegister.firstChild) {
		divRegister.removeChild(divRegister.firstChild);
	}
	let error = new Array;
	error = validateProduct(error);
	if (! error.length == 0) {
		showError(error, divRegister);
	} else {
		registerForm.submit();
	}
});


updateBtn.addEventListener('click', e => {
	e.preventDefault();
	while (divUpdate.firstChild) {
		divUpdate.removeChild(divUpdate.firstChild);
	}
	let error = new Array;
	error = validateUpdatedProduct(error);
	if (! error.length == 0) {
		showError(error, divUpdate);
	} else {
		updateForm.submit();
	}
});
const productName = document.getElementById('product-name');
const price = document.getElementById('price');
const qty = document.getElementById('qty');
const publicFlag = document.getElementsByClassName('public-flag');
const qtys = document.getElementsByClassName('qty');
const registerBtn = document.getElementById('register-product');
const updateBtn = document.getElementById('update-product');
const divRegister = document.getElementById('register-error');
const divUpdate = document.getElementById('update-error');

function validateProduct(error) {
	const pattern = /^[0-9]*[.][0-9]*/;

	if (productName.value === '') {
		error.push('商品名が入力されていません。');
	}
	if (price.value === '') {
		error.push('価格が入力されていません。');
	}
	if (isNaN(price.value)) {
		error.push('価格は半角数字で入力してください。');
	}
	if (price.value < 0 || pattern.test(price.value)) {
		error.push('価格は正の整数を入力してください。');
	}
	if (qty.value === '') {
		error.push('在庫数が入力されていません。');
	}
	if (isNaN(qty.value)) {
		error.push('在庫数は半角数字で入力してください。');
	}
	if (qty.value < 0 || pattern.test(qty.value)) {
		error.push('在庫数は正の整数を入力してください。');
	}
	return error;
}


function validateUpdatedProduct(error) {
	const pattern = /^[0-9]*[.][0-9]*/;
	let issetQty = true;
	let isQtyNum = true;
	let isQtyPlus = true;

	for (let i = 0; i < qtys.length; i++) {
		if (qtys[i].value === '') {
			issetQty = false;
		}
		if (isNaN(qtys[i].value)) {
			isQtyNum = false;
		}
		if (qtys[i].value < 0 || pattern.test(qtys[i].value)) {
			isQtyPlus = false;
		}
	}

	if (! issetQty) error.push('在庫数が入力されていません。');
	if (! isQtyNum) error.push('在庫数は半角数字で入力してください。');
	if (! isQtyPlus) error.push('在庫数は正の整数を入力してください。');

	return error;
}


registerBtn.addEventListener('click', e => {
	e.preventDefault();
	registerBtn.disabled = true;
	while (divRegister.firstChild) {
		divRegister.removeChild(divRegister.firstChild);
	}
	let error = new Array;
	error = validateProduct(error);
	if (error.length > 0) {
		showError(error, divRegister);
		registerBtn.disabled = false;
	} else {
		document.register.submit();
	}
});


updateBtn.addEventListener('click', e => {
	e.preventDefault();
	updateBtn.disabled = true;
	while (divUpdate.firstChild) {
		divUpdate.removeChild(divUpdate.firstChild);
	}
	let error = new Array;
	error = validateUpdatedProduct(error);
	if (error.length > 0) {
		showError(error, divUpdate);
		updateBtn.disabled = false;
	} else {
		document.update.submit();
	}
});
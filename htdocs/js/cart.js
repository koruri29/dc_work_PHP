const qtyInput = document.getElementsByClassName('qty');
const deleteInput = document.getElementsByClassName('delete');
const div = document.getElementById('show-error');
const qtyChangeBtn = document.getElementById('qty-change');
const purchaseBtn = document.getElementById('purchase');

function validateQty(error) {
	const pattern = /^[0-9]*[.][0-9]*/;
	let issetFlag = true;
	let numFlag = true;
	let plusFlag = true;
	for (let i = 0; i < qtyInput.length; i++) {
		if (! deleteInput[i].checked) {
			if (qtyInput[i].value == '') {
				issetFlag = false;
			}
			if (isNaN(qtyInput[i].value)) {
				numFlag = false;
			}
			if (qtyInput[i].value < 1 || pattern.test(qtyInput[i].value)) {
				plusFlag = false;
			}
		}
	}
	if (! issetFlag) error.push('数量が入力されていません。')
	if (! numFlag) error.push('数量は半角数字で入力してください。')
	if (! plusFlag) error.push('数量は正の整数で入力してください。')
	return error;
}


qtyChangeBtn.addEventListener('click', e => {
	e.preventDefault();
	while (div.firstChild) {
		div.removeChild(div.firstChild);
	}
	let error = new Array();
	error = validateQty(error);
	if (error.length > 0) {
		showError(error, div);
	} else {
		document.form1.submit();
	}
});

if (document.getElementById('purchase') != null) {
	purchaseBtn.addEventListener('click', e => {
		e.preventDefault();
		purchaseBtn.disabled = true;
		document.form2.submit();
	});
}

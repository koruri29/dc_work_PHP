const qtyChangeBtn = document.getElementById('qty-change');
const purchaseBtn = document.getElementById('purchase');

qtyChangeBtn.addEventListener('click', e => {
	e.preventDefault();
	qtyChangeBtn.disabled = true;
	document.form1.submit();
});

purchaseBtn.addEventListener('click', e => {
	e.preventDefault();
	purchaseBtn.disabled = true;
	document.form2.submit();
});
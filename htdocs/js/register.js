const userName = document.getElementById('user-name');
const password = document.getElementById('password');
const registerBtn = document.getElementById('register-user');
const h2 = document.getElementsByClassName('h2')[0];
const div = document.getElementById('show-msg');





function validateUser(error) {
	const namePattern = /^[a-zA-Z0-9]{5,}$/
	const passwordPattern = /^[a-zA-Z0-9]{8,}$/

	if (! namePattern.test(userName.value)) {
		error.push('ユーザー名は半角英数字5文字以上で入力してください。');
	}
	if (! passwordPattern.test(password.value)) {
		error.push('パスワードは半角英数字8文字以上で入力してください。');
	}
	return error;
}


registerBtn.addEventListener('click', e => {
	e.preventDefault();
	while (div.firstChild) {
		div.removeChild(div.firstChild);
	}
	let error = new Array;
	error = validateUser(error);
	if (error.length > 0) {
		showError(error, div);
	} else {
		document.register.submit();
	}
});
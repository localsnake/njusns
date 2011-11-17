var changePwdOldValid = false;
var changePwdNewValid = false;
var changePwdConfirmValid = false;
function changePwdInitPage() {
	/* 这边设置初始界面的内容 */
	/* 这边注册各种事件 */
	$('#changePwdOld').bind('blur', changePwdCheckPwdOld);
	//$('#changePwdNew').bind('keyup', changePwdCheckPwdNew);
	//$('#changePwdConfirm').bind('blur', changePwdCheckPwdConfirm);
	//$('#changePwdConfirm').keydown(changePwdCheckPwdConfirm);
	$('#changePwdNew').keyup(changePwdCheckPwdNew);
	$('#changePwdConfirm').keyup(changePwdCheckPwdConfirm);
	$('#changePwdOk').bind('click', changePwdOkClick);
	$('#changePwdReset').bind('click', changePwdResetClick);
	$('#changePwdOld').focus();
}

/* 检查pwdOld字段 */
function changePwdCheckPwdOld() {
	if ($('#changePwdOld').val() == '') {
		$('.errorOld').show();
		changePwdOldValid = false;
	}
	else {
		$('.errorOld').hide();
		changePwdOldValid = true;
	}
}

/* 检查pwdNew字段 */
function changePwdCheckPwdNew() {
	if ($('#changePwdNew').val() == '' || $('#changePwdNew').val().length < 6 || $('#changePwdNew').val().length > 20) {
		$('.errorNew').show();
		changePwdNewValid = false;
	}
	else {
		$('.errorNew').hide();
		changePwdNewValid = true;
	}
	changePwdCheckPwdConfirm();
}

/* 检查pwdConfirm字段 */
function changePwdCheckPwdConfirm() {
	if ($('#changePwdConfirm').val() == $('#changePwdNew').val()) {
		$('.errorConfirm').hide();
		changePwdConfirmValid = true;
	}
	else {
		$('.errorConfirm').show();
		changePwdConfirmValid = false;
	}
	changePwdCheckOkBtn();
}

/* 检查修改密码按钮是否可用 */
function changePwdCheckOkBtn() {
	if (changePwdOldValid && changePwdNewValid && changePwdConfirmValid) {
		$('#changePwdOk').removeAttr('disabled');
	}
	else {
		$('#changePwdOk').attr('disabled', 'disabled');
	}
}

/* 单击ok按钮 */
function changePwdOkClick() {
	changePwdCheckPwdOld();
	changePwdCheckPwdNew();
	changePwdCheckPwdConfirm();
	if ($('#changePwdOk').attr('disabled') != 'disabled') {
		$.post(
			Root + 'change_password.php', {
				old_password: encodeURI($('#changePwdOld').val()), 
				new_password: encodeURI($('#changePwdNew').val()),
				new_password2: encodeURI($('#changePwdConfirm').val())
			}, 
			changePwdClickCallback
		);
	}
}
function changePwdClickCallback(data) {
	if (data == '1') {
		//$('#tipInfo').show();
		$('#changePwdOld, #changePwdNew, #changePwdConfirm').val('');
		$('.err').hide();
		alert('密码修改成功');
		changeNav(false, false, "", "", "");
		changeContentPane("login.html", "");
		globalUserId = -2;
	}else {
		$('#tipInfo').show();
		$('#tipInfo').html(data);
	}
}
/* 单击重置按钮 */
function changePwdResetClick() {
	$('#changePwdOld, #changePwdNew, #changePwdConfirm').val('');
	$('.err').hide();
	$('#tipInfo').hide();
}

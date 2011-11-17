/* 下面是 Register 的js */
var registrationEmailValid = false;
var registrationPwdValid = false;
var registrationPwd2Valid = false;
var registrationNameValid = false;
var registrationBirthValid = false;
var registrationHometownValid = false;
var registrationValidationValid = false;
var registrationAgreementValid = false;
function registrationInitPage() {
	/* 这边设置初始界面的内容 */
	getValidationImg();
	/* 这边注册各种事件 */
	//$('#radio').buttonset();
	$('#regEmail').bind('keyup blur', registrationCheckEmail);
	$('#regPwd').bind('keyup blur', registrationCheckPwd);
	$('#regPwd2').bind('keyup blur', registrationCheckPwd2);
	$('#regName').bind('keyup blur', registrationCheckName);
	
	
	$('#regBirthDay').bind('blur', registrationCheckBirth);
	$('#regBirthMonth, #regBirthYear').bind('change', registrationSetBirthDay);
	
	$('#regHometown').bind('keyup blur', registrationCheckHometown);
	$('#regValidation').bind('keyup blur', registrationCheckValidation);
	$('#regAgreement').bind('blur', registrationCheckAgreement);
	$('#regAgreement').bind('click', registrationCheckAgreement);
	$('#regBtn').bind('click', registrationRegister);
	$('#regAnother').bind('click', getValidationImg);
	$('#bckBtn').bind('click',getBackToLogin);
	setAgreementFancybox();
}

function getBackToLogin(){
	changeNav(false, false, '', '', '');
	changeContentPane('login.html', '');
	globalUserId = -2;
}
/* 得到验证码图片 */
function getValidationImg() {
	$('#regValidationImg').remove();
	$('#errorValidation').after($('<img id="regValidationImg" alt="读码失败"></img>'));
	$('#regValidationImg').attr('src', Root+'generate_vcode.php?random='+Math.random());
}
/* 检查email字段 */
function registrationCheckEmail() {
	if (registrationIsEmail( $('#regEmail').val()+'@'+$('#regEmailType').val() ) == true) {	// 符合
		$('#errorEmail').hide();
		$('#errorEmail label').html('');
		registrationEmailValid = true;
	}
	else {		// 不符合
		$('#errorEmail label').html('邮箱格式不正确');
		$('#errorEmail').show();
		registrationEmailValid = false;
	}
	registrationCheckRegBtn();
}
/* 检查pwd字段 */
function registrationCheckPwd() {
	if ($('#regPwd').val() == '' || $('#regPwd').val().length<6 || $('#regPwd').val().length>20 ) {
		$('#errorPwd label').html('密码必须在6-20位');
		$('#errorPwd').show();
		registrationPwdValid = false;
	}
	else {
		$('#errorPwd').hide();
		$('#errorPwd label').html('');
		registrationPwdValid = true;
	}
	registrationCheckPwd2();
}
/* 检查pwd2字段 */
function registrationCheckPwd2() {
	if ($('#regPwd2').val() == $('#regPwd').val()) {
		$('#errorPwd2').hide();
		$('#errorPwd2 label').html('');
		registrationPwd2Valid = true;
	}
	else {
		$('#errorPwd2 label').html('密码重复有误');
		$('#errorPwd2').show();
		registrationPwd2Valid = false;
	}
	registrationCheckRegBtn();
}


/* 检查name字段，不能为空 */
function registrationCheckName() {
	if ($('#regName').val() != '') {
		$('#errorName').hide();
		$('#errorName label').html('');
		registrationNameValid = true;
	}
	else {
		$('#errorName label').html('请填写姓名');
		$('#errorName').show();
		registrationNameValid = false;
	}
	registrationCheckRegBtn();
}


/* 检查birth字段 */
function registrationCheckBirth() {
	if ($('#regBirthYear').val() != '选择年' 
		&& $('#regBirthMonth').val() != '选择月' 
		&& $('#regBirthDay').val() != '选择日' ) {
			$('#errorBirth').hide();
			$('#errorBirth label').html('');
			registrationBirthValid = true;
	}
	else {
		$('#errorBirth label').html('请填写正确的生日');
		$('#errorBirth').show();
		registrationBirthValid = false;
	}
	registrationCheckRegBtn();
}
/* 根据year和month设置birth中day的字段 */
function registrationSetBirthDay() {
	// 得到年和月
	var birthYear = $('#regBirthYear').val();
	var birthMonth = $('#regBirthMonth').val();
	$('#regBirthDay option').remove('option[value!=选择日]');
	if (birthYear != '选择年' && birthMonth != '选择月') {
		var dayNum = getDayNumber(birthYear, birthMonth);
		for (var i=1; i<=dayNum; i++) {
			$('#regBirthDay').append($('<option value="' + i + '">' + i + '</option>'));
		}
	}
}
/* 检查hometown字段 */
function registrationCheckHometown() {
	if ($('#regHometown').val() != '') {
		$('#errorHometown').hide();
		$('#errorHometown label').html('');
		registrationHometownValid = true;
	}
	else {
		$('#errorHometown label').html('请填写家乡');
		$('#errorHometown').show();
		registrationHometownValid = false;
	}
	registrationCheckRegBtn();
}
/* 检查validation字段 */
function registrationCheckValidation() {
	if ($('#regValidation').val() != '') {
		$('#errorValidation').hide();
		$('#errorValidation label').html('');
		registrationValidationValid = true;
	}
	else {
		$('#errorValidation label').html('验证码不能为空');
		$('#errorValidation').show();
		registrationValidationValid = false;
	}
	registrationCheckRegBtn();
}
/* 检查agreement字段 by Jiacunxin*/
function registrationCheckAgreement() {
	if ($('#regAgreement').attr('checked') == 'checked') {
		$('#errorAgreement').hide();
		$('#errorAgreement label').html('');
		registrationAgreementValid = true;
	}
	else {
		$('#errorAgreement label').html('请阅读使用协议！');
		$('#errorAgreement').show();
		registrationAgreementValid = false;
	}
	registrationCheckRegBtn();
}
/* 检查注册按钮是否可用 */
function registrationCheckRegBtn() {
	if (registrationEmailValid && registrationPwd2Valid &&registrationPwd2Valid 
		&& registrationNameValid && registrationBirthValid
		&& registrationHometownValid && registrationValidationValid
		&& registrationAgreementValid) {	// 可用
			$('#regBtn').removeAttr('disabled');
	}
	else {									// 不可用
		$('#regBtn').attr('disabled', 'disabled');
	}
}
/* 与服务器交互：发送注册信息 */
function registrationRegister() {
	if ($('#regBtn').attr('disabled', false)) {
		var regGender = 'F';
		if ($('#regGender').val() == '男') {
			regGender = 'M';
		}
		$.post(Root + 'register_new.php', {
			email: ( $('#regEmail').val()+'@'+$('#regEmailType').val()), 
			password: ($('#regPwd').val()), 
			password2: ($('#regPwd2').val()), 
			username: ($('#regName').val()), 
			gender: (regGender), 
			birthday: ($('#regBirthYear').val() + '-' + $('#regBirthMonth').val() + '-' + $('#regBirthDay').val()), 
			hometown: ($('#regHometown').val()), 
			vcode: ($('#regValidation').val())
		}, function(data) {
			if(data == '1'){
				alert('恭喜您，注册成功，请到邮箱查看激活信息，如没有收到请在登录界面点击重新发送确认信。');
				changeContentPane('login.html', '');
			}	else {
				alert(data);
				getValidationImg();
				$('#errorEcho label').html(data);
				$('#errorEcho').show();
				$('#regPwd2, #regPwd, #regValidation').val('');
				registrationPwdValid = false;
				registrationPwd2Valid = false;
				registrationValidationValid = false;
				registrationCheckRegBtn();
			}
		});
	}
}
/**
 * 以下是逻辑辅助函数们 
 */
/* 检查是否为合法的nju的email邮箱 */
function registrationIsEmail(str) {
	var reg = /^([a-zA-Z0-9_-])+@(smail\.)?(nju\.edu\.cn)$/;
    return reg.test(str);
}
/* 根据年月得到天数 */
function getDayNumber(birthYear, birthMonth) {
	switch (birthMonth) {
	case '1': case '3': case '5': case '7': case '8': case '10': case '12':
		return 31;
	case '4': case '6': case '9': case '11':
		return 30;
	}
	if (birthYear%400==0 || (birthYear%100!=0 && birthYear%4==0)) {
		return 29;
	}
	else {
		return 28;
	}
}
function setAgreementFancybox() {
	generalSetIFrame('#agreementBtn','85%','100%');
	$('#agreementBtn').attr('href', 'frmAgreement.php');
}
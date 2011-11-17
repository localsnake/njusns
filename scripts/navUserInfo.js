var curPage = 1;
function userInfoInitPage() {
	$('#userUploadPhoto').attr('href','frmUploadPhoto.php?type=user');
	generalSetIFrame('#userUploadPhoto',600,500);
	userInfoGetInfo();
	$('#userInfoEdit').bind('click', userInfoEdit);		// 根据所在页数进行编辑
	$('#pagePrev').bind('click',showFirstPage);
	$('#pageNext').bind('click',showLastPage);
	$('.editBtn').bind('click',editInfo);
	$('.cancelBtn').bind('click',cancelEdit);
	$('.saveBtn').bind('click',saveInfo);
	$('#userInfoBirthEditYear, #userInfoBirthEditMonth').bind('change', navUserInfoSetBirthDay);	// 动态改变生日'日'属性的option
	$('#userInfoEditDept').bind('change', navUserInfoSetMajor);									// 动态改变major属性
	showFirstPage();
}

/* 得到用户资料 */
function userInfoGetInfo() {
	$.getJSON(
		Root + 'view_info.php?type=all&user_id=' + globalUserId,
		userInfoGetInfoCallback
	);
}
/* 得到用户资料的返回信息 */
function userInfoGetInfoCallback(data) {
	if (data == '0') {
		alert('尚未登录或者没有权限查看该页面');
	}else {
		$('#userInfoUsername').text(data['user_name']);
		if(data['user_gender']=='M')	{
			$('#userInfoGender').text('男');
		} else{
			$('#userInfoGender').text('女');
		}
		$('#userInfoBirth').text(data['user_birthday']);
		$('#userInfoHometown').text(data['user_hometown']);
		$('#imgLarge').attr('src', data['user_photo_large']);
		$('#userInfoDept').text(data['user_department']);
		$('#userInfoMajor').text(data['user_major']);
		$('#userInfoDorm').text(data['user_dorm_no']);
		$('#userInfoHobby').text(data['user_hobby']);
		$('#userInfoMusic').text(data['user_music']);
		$('#userInfoFilm').text(data['user_films']);
		$('#userInfoSport').text(data['user_sports']);
		$('#userInfoBook').text(data['user_books']);
		$('#userInfoEmail').text(data['user_contact_email']);
		$('#userInfoQq').text(data['user_qq']);
		$('#userInfoMsn').text(data['user_msn']);
		$('#userInfoPhone').text(data['user_phone']);
		
		$('#userUploadPhoto').show();
		$('#userInfoSpecificShowAll').show();	
		if(globalLanguage == 'zh-cn') T_(globalLanguage);
	}
}
/* 进行编辑 */
function userInfoEdit() {
	/* 设置Edit面板的内容 */
	if (curPage == 1) {
		editEnable(1);
		editEnable(2);
	} else {
		editEnable(3);
		editEnable(4);
	}
}
function saveInfo() {
	var paneId = $(this).attr('name');
	switch(paneId) {
		case '1':
			$.post(Root + 'edit_info.php', {
			type: 'base',
			username: ($('#userInfoUsernameEdit').val()),
			gender: ($('#userInfoGenderEdit').val()),
			birthday: ($('#userInfoBirthEditYear').val()+'-'+$('#userInfoBirthEditMonth').val()+'-'+$('#userInfoBirthEditDay').val()),
			hometown: ($('#userInfoHometownEdit').val())
			},
			userInfoSaveAfterPost1
			);
			break;
		case '2':
			// 由于val是value的值，而value中的空格都被转成了_，所以在保存时，需要将_改回为空格
			var deptVal = $('#userInfoEditDept').val().replace(/_/g, ' ');
			var majorVal = $('#deptAndMajorTd select[class="active"]').val().replace(/_/g, ' ');
			$('#userInfoMajor').attr('msgid',majorVal);
			$('#userInfoDept').attr('msgid',deptVal);
			
			$.post(Root + 'edit_info.php', {
				type: 'school',
				department: (deptVal),
				major: (majorVal),
				dorm_no: ($('#userInfoDormEdit').val())
			},
			userInfoSaveAfterPost2
			);
			break;
		case '3':
			$.post(Root + 'edit_info.php', {
			type: 'hobby',
			hobby: ($('#userInfoHobbyEdit').val()),
			music: ($('#userInfoMusicEdit').val()),
			films: ($('#userInfoFilmEdit').val()),
			sports: ($('#userInfoSportEdit').val()),
			books: ($('#userInfoBookEdit').val())
			},
			userInfoSaveAfterPost3
			);
			break;
		case '4':
			$.post(Root + 'edit_info.php', {
			type: 'contact',
			contact_email: ($('#userInfoEmailEdit').val()),
			qq: ($('#userInfoQqEdit').val()),
			msn: ($('#userInfoMsnEdit').val()),
			phone: ($('#userInfoPhoneEdit').val())
			},
			userInfoSaveAfterPost4
			);
			break;
	}
}

function userInfoSaveAfterPost1(data) {
	userInfoSaveAfterPost(data, 1);
}
function userInfoSaveAfterPost2(data) {
	userInfoSaveAfterPost(data, 2);
}
function userInfoSaveAfterPost3(data) {
	userInfoSaveAfterPost(data, 3);
}
function userInfoSaveAfterPost4(data) {
	userInfoSaveAfterPost(data, 4);
}
function userInfoSaveAfterPost(data, paneId) {
	if (data == '1') {
		userInfoGetInfo();
		if (paneId == 1) {
			$('#userInfoSpecificEdit1').hide();
			$('#userInfoSpecificShow1').show();	
		} else if (paneId == 2) {
			$('#userInfoSpecificEdit2').hide();
			$('#userInfoSpecificShow2').show();	
		} else if (paneId == 3) {
			$('#userInfoSpecificEdit3').hide();
			$('#userInfoSpecificShow3').show();	
		} else {		// paneId == 4
			$('#userInfoSpecificEdit4').hide();
			$('#userInfoSpecificShow4').show();	
		}
	}
	else {
		alert('修改失败:' + data);
	}
}


function refreshPageNum() {
	$('#userInfoPage').html(curPage + ' / 2');
}

function showLastPage() {
	curPage = 2;
	$('#pagePrev').css('visibility', 'visible');
	$('#pageNext').css('visibility', 'hidden');
	/*
	$('#userInfoSpecificShowAll').hide();
	$('#userInfoSpecificShowAllPage2').show();
	$('#userInfoSpecificEditAll').hide();
	*/
	$('#userInfoSpecificEdit1').hide();
	$('#userInfoSpecificShow1').hide();
	$('#userInfoSpecificEdit2').hide();
	$('#userInfoSpecificShow2').hide();
	$('#userInfoSpecificEdit3').hide();
	$('#userInfoSpecificShow3').show();		
	$('#userInfoSpecificEdit4').hide();
	$('#userInfoSpecificShow4').show();	
	refreshPageNum();
}

function showFirstPage() {
	curPage = 1;
	$('#pageNext').css('visibility', 'visible');
	$('#pagePrev').css('visibility', 'hidden');
	/*
	$('#userInfoSpecificShowAllPage2').hide();
	$('#userInfoSpecificShowAll').show();
	$('#userInfoSpecificEditAll').hide();
	*/
	$('#userInfoSpecificEdit1').hide();
	$('#userInfoSpecificShow1').show();	
	$('#userInfoSpecificEdit2').show();
	$('#userInfoSpecificShow2').hide();
	$('#userInfoSpecificEdit2').hide();
	$('#userInfoSpecificShow2').show();
	$('#userInfoSpecificEdit3').hide();
	$('#userInfoSpecificShow3').hide();
	$('#userInfoSpecificEdit4').hide();
	$('#userInfoSpecificShow4').hide();
	refreshPageNum();
}

function editToShow() {
	var list = $('#userInfoSpecificShow label');
	$.each(list, function(index, obj) {
		$(obj).text($('#' + $(obj).attr('id') + 'Edit').val());
	});
}

function editInfo() {
	var paneId = $(this).attr('name');
	editEnable(paneId);
}

function cancelEdit() {
	var paneId = $(this).attr('name');
	editDisable(paneId);
}

function editEnable(paneId) {		
	var showPaneID = '#userInfoSpecificShow' + paneId;
	var editPaneID = '#userInfoSpecificEdit' + paneId;
	if(globalLanguage == 'zh-cn') T_('en');
	
	if(paneId == 1) {	// 基本信息
		// 真实姓名
		$('#userInfoUsernameEdit').val($('#userInfoUsername').text());
		// 性别
		if ($('#userInfoGender').text() == '男') {
			$('#userInfoGenderEdit').val('M');
		} else {
			$('#userInfoGenderEdit').val('F');
		}
		// 生日
		var birthVar = $('#userInfoBirth').text().split('-'); // 得到生日的年月日参数
		$('#userInfoBirthEditYear').val(birthVar[0]);	
		$('#userInfoBirthEditMonth').val(birthVar[1]);	
		navUserInfoSetBirthDay();
		$('#userInfoBirthEditDay').val(birthVar[2]);
		// 家乡
		$('#userInfoHometownEdit').val($('#userInfoHometown').text());
	} else if (paneId == 2) {	// 学校信息
		// 院系
		var deptVar = $('#userInfoDept').text().replace(/ /g, '_');
		$('#userInfoEditDept').val(deptVar);
		// 专业
		navUserInfoSetMajor();
		var majorVar = $('#userInfoMajor').text().replace(/ /g, '_');
		$('#deptAndMajorTd select[class="active"]').val(majorVar);
		// 宿舍号
		$('#userInfoDormEdit').val($('#userInfoDorm').text());
	} else if (paneId == 3) {
		var inputs = $(editPaneID + ' input');

		for (var i=0; i<inputs.length; i++) {
			$('#' + inputs[i].id).val($('#' + inputs[i].id.substring(0, inputs[i].id.length-4)).text());
		} 
	} else {	// paneId == 4
		var inputs = $(editPaneID + ' input');

		for (var i=0; i<inputs.length; i++) {
			$('#' + inputs[i].id).val($('#' + inputs[i].id.substring(0, inputs[i].id.length-4)).text());
		} 
	}
	$(showPaneID).hide();
	$(editPaneID).show();
	if(globalLanguage == 'zh-cn') T_(globalLanguage);
}

function editDisable(paneId) {
	var showPaneID = '#userInfoSpecificShow' + paneId;
	var editPaneID = '#userInfoSpecificEdit' + paneId;
	$(showPaneID).show();
	$(editPaneID).hide();
}

/* 进行取消 */
function userInfoCancel() {
	/* 显示与隐藏 */
	$('#userInfoSpecificEditAll').hide();
	$('#userInfoSpecificShowAll').show();
}

/* 根据year和month设置birth中day的字段 */
function navUserInfoSetBirthDay() {
	// 得到年和月
	var birthYear = $('#userInfoBirthEditYear').val();
	var birthMonth = $('#userInfoBirthEditMonth').val();
	$('#userInfoBirthEditDay option').remove('option[value!=选择日]');
	if (birthYear != '选择年' && birthMonth != '选择月') {
		var dayNum = getDayNumber(birthYear, birthMonth);
		for (var i=1; i<=dayNum; i++) {
			if (i < 10) {
				$('#userInfoBirthEditDay').append($('<option value="0' + i + '">' + i + '</option>'));
			} else {
				$('#userInfoBirthEditDay').append($('<option value="' + i + '">' + i + '</option>'));
			}
		}
	}
}
/* 根据年月得到天数 */
function getDayNumber(birthYear, birthMonth) {
	switch (birthMonth) {
	case '01': case '03': case '05': case '07': case '08': case '10': case '12':
		return 31;
	case '04': case '06': case '09': case '11':
		return 30;
	}
	if (birthYear%400==0 || (birthYear%100!=0 && birthYear%4==0)) {
		return 29;
	}
	else {
		return 28;
	}
}

/* 根据dept设置major字段 */
function navUserInfoSetMajor() {
	// 得到dept
	var deptId = $('#userInfoEditDept option[value='+$('#userInfoEditDept').val()+']').attr('name');
	$('#deptAndMajorTd select').attr('class', 'inactive');
	$('#deptAndMajor'+deptId).attr('class', 'active');
}

course_time_string = '';	/*课程时间*/
function courseIntroInitPage(id, relation) {
	courseIntroGetIntro(id, relation);
	$('.short_separator').hide();
	/* 这边注册各种事件 */
	if (courseRelation == 'M' || courseRelation == 'T') {
		$('.short_separator').show();
		$('#coursePhotoEdit').css('display', 'inline-block');
		$('#courseIntroEdit').css('display', 'inline-block');
		$('#courseIntroEdit').bind('click', courseIntroEditClick);		// 点击编辑
		$('#courseIntroSave').bind('click', courseIntroSaveClick);		// 点击保存
		$('#courseIntroCancel').bind('click', courseIntroCancelClick);	// 点击取消
					    						// set course upload photo fancybox
		$('#timeBegin1').bind('change', timeEndSet1);
		$('#timeBegin2').bind('change', timeEndSet2);
		$('#timeBegin3').bind('change', timeEndSet3);
		$('#courseVerifyEdit').bind('click change',function(){	//修改课程验证方式
			var verifyType = $('#courseVerifyEdit').val();
			if(verifyType != 'C') {	//密码认证时跳出验证密码输入框
				$('#codeTr').hide();
			} else {
				$('#codeTr').show();
			}
		});
		setPhotoEditFancybox();	
		// only teacher can set ta
		if(courseRelation == 'M') {	setTaSettingFancybox();	}
	}
}

function courseIntroGetIntro(id, relation) {
	// 根据状态得到信息，这里，所有状态都可以看到全部信息
	$.getJSON(
		Root + 'get_courseinfo.php', {
			course_id: courseId
		}, 
		courseIntroCreateInfo
	);
	// 设置关系图标
	setCourseIntroSysInfo(relation);
}

function setCourseIntroSysInfo(relation) {
	$('#courseIntroSysInfo a').removeClass();
	$('#courseIntroSysInfo a').unbind();
	$('#courseIntroSysInfo a').removeAttr('href');
	if (relation == 'W') {
		$('#courseIntroSysInfo a').addClass('waitForAnswer');
		$('#courseIntroSysInfo a').attr('title', '申请中...');
		$('#courseIntroSysInfoLabel').html('申请中');
	} else if (relation == 'N'){		// N
		$('#courseIntroSysInfo a').addClass('sendUserApply');
		$('#courseIntroSysInfo a').attr('title', '加入课程');
		$('#courseIntroSysInfoLabel').html('加入课程');
		$('#courseIntroSysInfo a').attr('href', '#');
	}
}

function sendCourseApplyCallback(data, courseId) {
	if (data == 1) {
		alert('申请发送成功');
	}
	else {
		alert('申请发送失败了: '+data);
	}
	// 刷新用户关系
	$.get(	// course
		Root + 'get_relation.php', {
			type: 'course', 
			id: courseId
		},
		function(relation) {
			var $var = $('#courseIntroSysInfo a');
			if (relation == 'N') {
				// do nothing
			} else if (relation == 'A') {
				sendCourseApplyCallbackAid($var, 'hasBeenFriend', courseId, '已为好友');
			} else if (relation == 'W') {
				sendCourseApplyCallbackAid($var, 'waitForAnswer', courseId, '申请中...');
			} else {	// M or T
				sendCourseApplyCallbackAid($var, 'manageCourse', courseId, '管理者...');
			}
		}
	);
}
function sendCourseApplyCallbackAid($var, addClassName, courseId, titleName) {
	$var.removeClass('sendUserApply');
	$var.removeClass('hasBeenFriend');
	$var.removeClass('waitForAnswer');
	$var.removeClass('manageCourse');
	$var.addClass(addClassName);
	$var.removeAttr('href');
	$var.removeAttr('id');
	$var.unbind();
	$var.attr('name', 'course' + courseId);
	$var.attr('title', titleName);
}

function courseIntroCreateInfo(data) {
	$('#imgLarge').attr('src', data['course_photo_large']);
	$('#courseIntroName').text(data['course_name']);
	$('#courseIntroTerm').text(data['course_term']);
	$('#courseIntroType').text(data['course_type']);
	$('#courseIntroIntro').text(data['course_introduction']);
	
	course_time_string = data['course_time'];
	var course_time_display = '';
	if(course_time_string != 'NULL') {
		do {
			var split_index = course_time_string.indexOf(':');
			var splited;
			if(split_index == -1)
				splited = course_time_string; 
			else {
				splited = course_time_string.substring(0,split_index);
				course_time_string = course_time_string.substring(split_index + 1);
			}
			var weekday;
			switch(splited.charAt(0)) {
				case '1':weekday = '周一';break;
				case '2':weekday = '周二';break;
				case '3':weekday = '周三';break;
				case '4':weekday = '周四';break;
				case '5':weekday = '周五';break;
				case '6':weekday = '周六';break;
				case '7':weekday = '周日';break;
				default:break;
			}
			course_time_display += weekday + ' 第' + splited.substring(2) + '节 ';							
		}while(split_index != -1);
	} else {
		course_time_display = 'NULL';
	}
	$('#courseIntroTime').text(course_time_display);
	$('#courseIntroPlace').text(data['course_place']);
	$('#courseIntroNumber').text(data['course_stu_number']);
	$('#courseIntroBook').text(data['course_book']);
	$('#courseVerify').text(data['verify']);

	$('#courseIntroSysInfo a').bind('click', function() {	// 添加申请状态图标的绑定
			$.post(
				Root + 'send_course_apply.php', {
					from_id: globalUserId, 
					course_id: courseId,
					apply_content: '无'
				},
				function(retData) {
					sendCourseApplyCallback(retData, courseId);
				}
			);
	});
		
	if(data['verify'] == 'C') {
		$('#coursePassword').text(data['password']);
		$('#courseVerifyNotes').text('验证需要老师提供的密码');
		$('#courseIntroSysInfo a').unbind();
		
		var link = 'frmVerifyCourse.php?course_id='+courseId+'&from_id='+globalUserId;
		$('#courseIntroSysInfo a').attr('href', link);
		generalSetIFrame('#courseIntroSysInfo a',450,350);		//设置编辑链接FancyBox
		
	} else if(data['verify'] == 'Y'){
		$('#courseVerifyNotes').text('加入课程需要老师手动确认');
	} else if(data['verify'] == 'N') {
		$('#courseVerifyNotes').text('无需验证即可加入课程');
	} else {
		$('#courseVerifyNotes').text('出错了，未知验证方式');
	}
	if(data['course_url']) {
		var url = data['course_url'];
		if(url.indexOf('http:') == -1) 	url = "http://" + url;
		$('#courseUrlLink').attr('href',url);
		$('#courseUrlLink').text(url);
	}
	
	//show teacher and ta info 
	var teacherInfo = '<a  name ="' +  data['teacher_id'] + '" class = "userName">' + data['teacher_name'] + '</a>';
	$('#courseTeacherName').html(teacherInfo);
	$('#courseTAName').html('');
	if(data['ta0_id'] != undefined) {
		var ta0Info = '<a  name ="' +  data['ta0_id'] + '" class = "userName">' + data['ta0_name'] + '</a>';
		$('#courseTAName').append(ta0Info);
	} else {
		$('#courseTAName').text('暂未指定');
	}
	if(data['ta1_id'] != undefined) {
		var ta1Info = '<a  name ="' +  data['ta1_id'] + '" class = "userName">' + data['ta1_name'] + '</a>';
		$('#courseTAName').append(ta1Info);
	} 
	if(data['ta2_id'] != undefined) {
		var ta2Info = '<a  name ="' +  data['ta2_id'] + '" class = "userName">' + data['ta2_name'] + '</a>';
		$('#courseTAName').append(ta2Info);
	} 
	if(data['ta3_id'] != undefined) {
		var ta3Info = '<a  name ="' +  data['ta3_id'] + '" class = "userName">' + data['ta3_name'] + '</a>';
		$('#courseTAName').append(ta3Info);
	} 
	if(data['ta4_id'] != undefined) {
		var ta4Info = '<a  name ="' +  data['ta4_id'] + '" class = "userName">' + data['ta4_name'] + '</a>';
		$('#courseTAName').append(ta4Info);
	}
	
	$('.userName').unbind();
	$('.userName').bind('click', courseIntroTeacherClick);
	$('#courseIntroSpecificShowAll').show();
}



function courseIntroTeacherClick() {
	var friendId = $(this).attr('name');
	if (friendId == globalUserId) {	// 查看自己
		changeNav(true, true, 'navUser', 'navUser', 'navUserInfo');
		changeAccount(true);
		changeSearch(true);
		changeContentPane('navUserInfo.html');
	} else {						// 根据关系判断权限
		$.get(
			Root + 'get_relation.php', {
				type: 'user',
				id: friendId
			}, 
			function (data) {
				jumpToFriend(friendId, data);
			}
		);
	}
}

function courseIntroEditClick() {
	$('#courseIntroIntroEdit').val($('#courseIntroIntro').text());
	$('#courseIntroNameEdit').val($('#courseIntroName').text());
	$('#courseIntroTermEdit').val($('#courseIntroTerm').text());
	$('#courseIntroTypeEdit').val($('#courseIntroType').text());
	emptyCourseIntroTime();
	setCourseIntroTime();
	$('#courseIntroPlaceEdit').val($('#courseIntroPlace').text());
	$('#courseIntroNumberEdit').val($('#courseIntroNumber').text());
	$('#courseIntroBookEdit').val($('#courseIntroBook').text());
	$('#courseVerifyEdit').val($('#courseVerify').text());
	if($('#courseVerify').text() == 'C') {
		$('#codeTr').show();
		$('#coursePasswordEdit').val($('#coursePassword').text());
	} else {
		$('#codeTr').hide();
	}
	$('#courseIntroUrlEdit').val($('#courseUrlLink').text());
	$('#courseIntroSpecificShowAll').hide();
	$('#courseIntroSpecificEditAll').show();
}

function courseIntroSaveClick() {
	// 上传修改信息
	var courseTime = '';
	for (var i=1; i<=3; i++) {
		var timeWeek = $('#timeWeek'+i).val();
		var timeBegin = $('#timeBegin'+i).val();
		var timeEnd = $('#timeEnd'+i).val();
		if(timeWeek!='--' && timeBegin!='--'){
			if(i>1) courseTime += ':';
			courseTime += (timeWeek + '-' + timeBegin + '-' + timeEnd);
		}
	}
	if (isNaN($('#courseIntroNumberEdit').val())) {
		alert('上课人数必须是数字或者留空');
		return ;
	}
	$.post(
		Root + 'edit_courseinfo.php', {
			course_id: courseId,
			course_introduction: $('#courseIntroIntroEdit').val(),
			course_name: $('#courseIntroNameEdit').val(),
			course_term: $('#courseIntroTermEdit').val(),
			course_type:$('#courseIntroTypeEdit').val(),
			course_time:courseTime,
			course_place:$('#courseIntroPlaceEdit').val(),
			course_stu_num:$('#courseIntroNumberEdit').val(),
			course_book:$('#courseIntroBookEdit').val(),
			course_url:$('#courseIntroUrlEdit').val(),
			verify: $('#courseVerifyEdit').val(),
			password:$('#coursePasswordEdit').val()
		},
		function(data) {
			if( data != '1') {
				alert(data);
				return;
			}
			courseIntroGetIntro(courseId, courseRelation);
			$('#courseIntroSpecificEditAll').hide();
			$('#courseIntroSpecificShowAll').show();
		}
	);
}

function courseIntroCancelClick() {
	$('#courseIntroSpecificEditAll').hide();
	$('#courseIntroSpecificShowAll').show();
}

function timeEndSet1() {
	timeEndSet(1);
}
function timeEndSet2() {
	timeEndSet(2);
}
function timeEndSet3() {
	timeEndSet(3);
}
function timeEndSet(index) {
	var timeBegin = $('#timeBegin' + index).val();
	$('#timeEnd' + index + ' option').remove('option[value!="--"]');
	for (var i=timeBegin; i<=10; i++) {
		$('#timeEnd' + index).append($('<option value="' + i + '">' + i + '</option>'));
	}
}
function setPhotoEditFancybox() {
	generalSetIFrame('#coursePhotoEdit',430,300);
	$('#coursePhotoEdit').attr('href','frmUploadPhoto.php?type=course&course_id=' + courseId);
	$('#coursePhotoEdit').show();
}
function setTaSettingFancybox() {
	generalSetIFrame('#setTALink',430,430);
	$('#setTALink').attr('href','frmSetTa.php?course_id=' + courseId);
	$('#setTALink').show();
}
function setCourseIntroTime() {
	var timeSlice = $('#courseIntroTime').text().split(' ');
	var i = 1;
	while (i < timeSlice.length / 2) {
		// get value of day
		$('#timeWeek'+i).val(getDayFromChar(timeSlice[2*i-2].charAt(1)));
		var time = timeSlice[2*i-1].split('-');
		$('#timeBegin'+i).val(time[0].substr(1));
		timeEndSet(i);
		$('#timeEnd'+i).val(time[1].substring(0, time[1].length-1));
		i++;
	}
}
function getDayFromChar(i) {
	if (i == '一') {
		return '1';
	} else if (i == '二') {
		return '2';
	} else if (i == '三') {
		return '3';
	} else if (i == '四') {
		return '4';
	} else if (i == '五') {
		return '5';
	} else if (i == '六') {
		return '6';
	} else {
		return '7';
	}
}
function emptyCourseIntroTime() {
	for (var i=1; i<=3; i++) {
		$('#timeWeek'+i).val('--');
		$('#timeBegin'+i).val('--');
		timeEndSet(i);
		$('#timeEnd'+i).val('--');
	}
}

var curPage = 1;
var sumPage = 2;
function friendHomepageInitPage(userId, status) {
	/* 这边设置初始界面的内容 */
	friendHomepageGetInfo(userId, status);
	/* 这边注册各种事件 */

	/*-------added by qdx----------*/
	$('#pagePrev').bind('click',showFirstPage);
	$('#pageNext').bind('click',showLastPage);
	/*---------------end------------*/
}

/* 根据不同状态得到不同效果 */
function friendHomepageGetInfo(userId, status) {
	// 根据状态得到信息
	if (status == 'Y') {
		$.getJSON(
			Root + 'view_info.php?type=all&user_id=' + userId,
			friendHomepageGetInfoCallback
		);
	}
	else {
		$.getJSON(
			Root + 'view_info.php?user_id=' + userId, 
			friendHomepageGetBaseInfoCallback
		);
		$('.advanced').hide();
	}
	// 设置关系图标
	friendHomepageSetPage(status);
	setFriendHomepageSysInfo(status);
}

/* 得到用户资料的返回信息 */
function friendHomepageGetInfoCallback(data) {
	if (data == '0') {
		alert('尚未登录或者没有权限查看该页面');
	} 	else {
		$('#friendHomepageUsername').text(data['user_name']);
		if(data['user_gender'] == 'F')
			$('#friendHomepageGender').text('女');
		else if(data['user_gender'] == 'M')
			$('#friendHomepageGender').text('男');
		$('#friendHomepageBirth').text(data['user_birthday']);
	//?	$('#').text(data['user_type']);
		$('#friendHomepageHometown').text(data['user_hometown']);
	//?	$('#').text(data['user_level']);
		$('#imgLarge').attr('src', data['user_photo_large']);
		$('#imgSmall').attr('src', data['user_photo']);
	//?	$('#').text(data['user_status']);
		$('#friendHomepageDept').text(data['user_department']);
		$('#friendHomepageMajor').text(data['user_major']);
		$('#friendHomepageDorm').text(data['user_dorm_no']);
		$('#friendHomepageHobby').text(data['user_hobby']);
		$('#friendHomepageMusic').text(data['user_music']);
		$('#friendHomepageFilm').text(data['user_films']);
		$('#friendHomepageSport').text(data['user_sports']);
		$('#friendHomepageBook').text(data['user_books']);
		$('#friendHomepageEmail').text(data['user_contact_email']);
		$('#friendHomepageQq').text(data['user_qq']);
		$('#friendHomepageMsn').text(data['user_msn']);
		$('#friendHomepagePhone').text(data['user_phone']);
		
		if(globalLanguage == 'zh-cn') T_(globalLanguage);
		$('#friendHomepageSpecificShowAll').show();	
	}
}

function friendHomepageGetBaseInfoCallback(data) {
	if (data == '0') {
		alert('尚未登录或者没有权限查看该页面');
	}
	else {
		$('#friendHomepageUsername').text(data['user_name']);
		$('#friendHomepageGender').text(data['user_gender']);
		$('#imgLarge').attr('src', data['user_photo_large']);
		$('#imgSmall').attr('src', data['user_photo']);
		$('#friendHomepageBirth').text('不告诉你');
		$('#friendHomepageHometown').text(data['user_hometown']);

		$('#friendHomepageSpecificShowAll').show();	
		//$('#friendHomepageSpecificEdit').accordion({autoHeight:false});	
	}
}

function friendHomepageSetPage(status) {
	if (status == 'Y') {
		$('#pageControl').show();
	} else {	// N, W
		$('#pageControl').hide();
	}
}


function setFriendHomepageSysInfo(status) {
	$('#friendHomepageSysInfo a').removeClass();
	$('#friendHomepageSysInfo a').unbind();
	$('#friendHomepageSysInfo a').removeAttr('href');
	if (status == 'Y') {
//		$('#friendHomepageSysInfo a').addClass('hasBeenFriend');
//		$('#friendHomepageSysInfo a').attr('title', '已为好友');
	} else if (status == 'N') {
		$('#friendHomepageSysInfo a').addClass('sendUserApply');
		$('#friendHomepageSysInfo a').attr('title', '加为好友');
		$('#friendHomepageSysInfo label').html('加为好友');
		$('#friendHomepageSysInfo a').bind('click', function() {	// 添加申请状态图标的绑定
			$.post(
				Root + 'send_user_apply.php', {			// 发送申请
					from_id: globalUserId,
					to_id: userId,
					apply_content: '无'
				},
				function(retData) {						// 回调函数
					sendUserApplyCallback(retData, userId);
				}
			);
		});
	} else {
		$('#friendHomepageSysInfo a').addClass('waitForAnswer');
		$('#friendHomepageSysInfo a').attr('title', '申请已发送');
		$('#friendHomepageSysInfo label').html('申请已发送');
	}
}

function sendUserApplyCallback(data, userId) {
	if (data == 1) {
		alert('申请发送成功');
	} else {
		alert('发送失败:'+data);
	}
	// 刷新用户关系
	$.get(	// user
		Root + 'get_relation.php', {
			type: 'user',
			id: userId
		}, 
		function(relation) {
			var $var = $('#friendHomepageSysInfo a');
			if (relation == 'N') {
				// do nothing
			} else if (relation == 'Y') {
				sendUserApplyCallbackAid($var, 'hasBeenFriend', userId, '已为好友');
			} else {	// W
				sendUserApplyCallbackAid($var, 'waitForAnswer', userId, '申请中...');
			}
		}
	);
}
function sendUserApplyCallbackAid($var, addClassName, userId, titleName) {
	$var.removeClass('sendUserApply');
	$var.removeClass('hasBeenFriend');
	$var.removeClass('waitForAnswer');
	$var.addClass(addClassName);
	$var.removeAttr('href');
	$var.removeAttr('id');
	$var.unbind();
	$var.attr('name', 'user' + userId);
	$var.attr('title', titleName);
}

/* The following code block was edited by qdx.
 * They were originally written by jcx in 'navUserInfo.js'*/
function refreshPageNum() {
	$('#friendMainPage').html(curPage + ' / ' + sumPage);
}


function showLastPage() {
	curPage = 2;
	$('#pagePrev').css('visibility', 'visible');
	$('#pageNext').css('visibility', 'hidden');
	$('#friendHomepageSpecificShowAll').hide();
	$('#friendHomepageSpecificShowAllPage2').show();
	refreshPageNum();
}

function showFirstPage() {
	curPage = 1;
	$('#pageNext').css('visibility', 'visible');
	$('#pagePrev').css('visibility', 'hidden');
	$('#friendHomepageSpecificShowAllPage2').hide();
	$('#friendHomepageSpecificShowAll').show();
	refreshPageNum();
}


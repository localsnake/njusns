function mainAnnounceInitPage() {
	/* 这边设置初始界面的内容 */
	refreshAnnounceCount();
	mainAnnounceGetAnnounce();
}	

/* 得到申请数据 */
function mainAnnounceGetAnnounce() {
	var data;
	// 得到好友申请
	$.getJSON(
		Root + 'view_apply.php?type=user',
		mainAnnounceFriend
	);
	// 得到课程申请
	$.getJSON(
		Root + 'view_apply.php?type=course',
		mainAnnounceCourse
	);
}


/* 处理申请返回 */
function mainAnnounceFriend(data) {
	if (data == '0') {		// 未登录
		alert('尚未登录');
		generalGotoLoginPage();
	} else if (data == '') {
		alert('没有这部分的申请通知');
	} else {
		if (data == null) {
			$('#mainAnnounceFriend .mainAnnounceChoose').css('display', 'none');
			$('#mainAnnounceNone').show();
		} else {
			$('#mainAnnounceFriend .mainAnnounceChoose').css('display', 'inline-block');
			$('#mainAnnounceNone').hide();
			$.each(data, function(index, dataSingle) {
				$('#mainAnnounceFriend ul').append(createAnnounce(dataSingle, dataSingle['user_id'], '你', true));
			});
		}
	}
	/* 接受/拒绝一个 */ 
	$('#mainAnnounceFriend a[title=接受]').bind('click', mainAnnounceAgreeFriend);
	$('#mainAnnounceFriend a[title=拒绝]').bind('click', mainAnnounceDenyFriend);
	/* 接受/拒绝选中 */
	$('.mainAnnounceChooseOk[name=friend]').bind('click', mainAnnounceAgreeFriendSelected);
	$('.mainAnnounceChooseDeny[name=friend]').bind('click', mainAnnounceDenyFriendSelected);
	mainAnnounceFriendAfterCreate();
}

function mainAnnounceCourse(data) {
	if (data == '0') {		// 未登录
		alert('尚未登录');
		changeNav(false, false, '', '', '');
		changeAccount(false);
		changeSearch(false);
		changeContentPane('login.html');
	} else if (data == '') {	// 无申请通知
		alert('没有这部分的申请通知');
	} else {
		var lastId = '';
		var thisId;
		$.each(data, function(index, dataSingle) {
			thisId = dataSingle['course_id'];
			if (thisId != lastId) {	// 另一个课程的申请通知
				lastId = thisId;
				createCourseDiv(dataSingle['course_id'], dataSingle['course_name']);
			}
			$('#mainAnnounce' + thisId + ' ul').append(createAnnounce(dataSingle, dataSingle['course_id'], dataSingle['course_name'], false));
			mainAnnounceCourseAfterCreate('mainAnnounce' + dataSingle['course_id']);
		});
	}
	/* 接受/拒绝一个 */
	$('#wrapper>div:not(#mainAnnounceFriend) a[title=接受]').bind('click', mainAnnounceAgreeCourse);
	$('#wrapper>div:not(#mainAnnounceFriend) a[title=拒绝]').bind('click', mainAnnounceDenyCourse);
	/* 接受/拒绝选中 */
	$('.mainAnnounceChooseOk[name!=friend]').bind('click', mainAnnounceAgreeCourseSelected);
	$('.mainAnnounceChooseDeny[name!=friend]').bind('click', mainAnnounceDenyCourseSelected);
}


/* 添加一条申请信息 */
/* 若是课程申请,则id为课程id,who为课程名 
   若是好友申请,则id为用户id,who为“你”,并且在li标签中加上class为'friend'
*/
function createAnnounce(data, id, who, isFriend) {	
	var $newAnnounce;
	if	(isFriend) {
		$newAnnounce = $('<li id="mainAnnounce' + data['apply_id'] + '" class="mainAnnounce">'
				+ '<div class="checkboxDiv">'
				+ '<input class="checkbox" type="checkbox" name=' + data['apply_id'] + '></input></div>'
				+ '<div class="picDiv" style="margin-left:30px;">'
				+ '<img class = "userPhoto" name ="' + data['from_user_id'] + '" src="' + data['from_user_photo'] + '"></img></div>'
				+ '<div class = "feedDiv">'
				+ '<label id="' + data['from_user_id'] + '" name ="' + data['from_user_id'] + '" class="user_name">' + data['from_user_name'] + '</label>'
				+ '<label>向</label>'
				+ '<label id="' + id + '" class="who">' + who + '</label>'
				+ '<label>发出了好友申请</label>'
				+ '<br/>'
				+ '<label class="time">(' + data['apply_time'] + ')</label></div>'
				+ '<div class="opDiv">'
				+ '<a name="' + data['apply_id'] + '" class="approveDiv approveIcon" title="接受"></a>'
				+ '<a name="' + data['apply_id'] + '" class="ignoreDiv courseDelete" title="拒绝"></a></div></li>');
	} else {
		$newAnnounce = $('<li id="mainAnnounce' + data['apply_id'] + '" class="mainAnnounce">'
				+ '<div class="checkboxDiv">'
				+ '<input class = "checkbox" type="checkbox" name=' + data['apply_id'] + '></input></div>'
				+ '<div class="picDiv" style="margin-left:30px;">'
				+ '<img class = "userPhoto" name ="' + data['from_user_id'] + '" src="' + data['from_user_photo'] + '"></img></div>'
				+ '<div class = "feedDiv">'
				+ '<label id="' + data['from_user_id'] + '" name ="' + data['from_user_id'] + '" class="user_name">' + data['from_user_name'] + '</label>'
				+ '<label>向</label>'
				+ '<label id="' + id + '" class="who">' + who + '</label>'
				+ '<label>发出了课程申请</label>'
				+ '<br/>'
				+ '<label class="time">(' + data['apply_time'] + ')</label></div>'
				+ '<div class="opDiv">'
				+ '<a name="' + data['apply_id'] + '" class="approveDiv approveIcon" title="接受"></a>'
				+ '<a name="' + data['apply_id'] + '" class="ignoreDiv courseDelete" title="拒绝"></a></div></li>');
	}
	return $newAnnounce;
}
/* 添加课程申请的div */
/* id为课程id
   name为课程名
*/
function createCourseDiv(id, name) {
	$('#wrapper').append(
		$('<div id="mainAnnounce' + id + '">'
			+ '<p class="announceTitle">' + name + '</p>'
			+ '<ul></ul>'
			+ '<div class="seperatorHr"></div>'
			+ '<div class="mainAnnounceChoose">'
			+ '<a name="' + id + '" class="mainAnnounceChooseAll">全选</a>'
			+ '<a name="' + id + '" class="mainAnnounceChooseNone">全不选</a>'
			+ '<a name="' + id + '" class="mainAnnounceChooseOpp">反选</a>'
			+ '<button name="' + id + '" class="mainAnnounceChooseOk button">接受</button>'
			+ '<button name="' + id + '" class="mainAnnounceChooseDeny button">拒绝</button>'
			+ '</div>'
			+ '</div>')
	);
}

/* 处理接受事件 */
function mainAnnounceAgreeFriend() {
	mainAnnounceAgreeFriendOne('mainAnnounce' + $(this).attr('name'));
}
function mainAnnounceAgreeFriendOne(liId) {
	var applyId = $('#' + liId + ' input').attr('name');
	var fromId = $('#' + liId + ' label:eq(0)').attr('id');
	var toId = $('#' + liId + ' label:eq(2)').attr('id');
	$.get(
		Root + 'approve_user_apply.php?apply_id=' + applyId
			+ '&from_id=' + fromId + '&apply_content=""', 
		function(data) {
			if (data == '1') {
				$('li#' + liId).remove();
				refreshAnnounceCount();
			} else {
				alert('操作失败:' + data);
			}
		}
	);
	
}

function mainAnnounceAgreeCourse() {
	mainAnnounceAgreeCourseOne('mainAnnounce' + $(this).attr('name'));
}
function mainAnnounceAgreeCourseOne(liId) {
	var applyId = $('#' + liId + ' input').attr('name');
	var fromId = $('#' + liId + ' label:eq(0)').attr('id');
	var toId = $('#' + liId + ' label:eq(2)').attr('id');
	$.get(
		Root + 'approve_course_apply.php?apply_id=' + applyId 
			+ '&from_id=' + fromId
			+ '&course_id=' + toId, 
		function(data) {
			if (data == '1') {
				$('li#' + liId).remove();
				refreshAnnounceCount();
			} else {
				alert('操作失败:' + data);
			}
		}
	);
}

/* 处理拒绝事件 */
function mainAnnounceDenyFriend() {
	mainAnnounceDenyFriendOne('mainAnnounce' + $(this).attr('name'));
}
function mainAnnounceDenyFriendOne(liId) {
	var applyId = $('#' + liId + ' input').attr('name');
	var fromId = $('#' + liId + ' label:eq(0)').attr('id');
	var toId = $('#' + liId + ' label:eq(2)').attr('id');
	$.get(
		Root + 'ignore_user_apply.php?apply_id=' + applyId, 
		function(data) {
			if (data == '1') {
				$('li#' + liId).remove();
				refreshAnnounceCount();
			} else {
				alert('操作失败,错误码:'+data);
			}
		}
	);
}

function mainAnnounceDenyCourse() {
	mainAnnounceDenyCourseOne('mainAnnounce' + $(this).attr('name'));
}
function mainAnnounceDenyCourseOne(liId) {
	var applyId = $('#' + liId + ' input').attr('name');
	var fromId = $('#' + liId + ' label:eq(0)').attr('id');
	var toId = $('#' + liId + ' label:eq(2)').attr('id');
	$.get(
		Root + 'ignore_course_apply.php?apply_id=' + applyId 
			+ '&course_id=' + toId, 
		function(data) {
			if (data == '1') {
				$('li#' + liId).remove();
				refreshAnnounceCount();
			} else {
				alert('操作失败,错误码:' + data);
			}
		}
	);
}

/* 接受选中好友 */
function mainAnnounceAgreeFriendSelected() {
	var list = $('#mainAnnounceFriend li');
	$.each(list, function(i, obj){
		if ($('#' + $(obj).attr('id') + ' input').attr('checked') == 'checked') {
			mainAnnounceAgreeFriendOne($(obj).attr('id'));
		}
	});
}
/* 拒绝选中好友 */
function mainAnnounceDenyFriendSelected() {
	var list = $('#mainAnnounceFriend li');
	$.each(list, function(i, obj){
		if ($('#' + $(obj).attr('id') + ' input').attr('checked') == 'checked') {
			mainAnnounceDenyFriendOne($(obj).attr('id'));
		}
	});
}
/* 接受选中课程 */
function mainAnnounceAgreeCourseSelected() {
	var list = $('#mainAnnounce' + $(this).attr('name') + ' li');
	$.each(list, function(i, obj){
		if ($('#' + $(obj).attr('id') + ' input').attr('checked') == 'checked') {
			mainAnnounceAgreeCourseOne($(obj).attr('id'));
		}
	});
}
/* 拒绝选中课程 */
function mainAnnounceDenyCourseSelected() {
	var list = $('#mainAnnounce' + $(this).attr('name') + ' li');
	$.each(list, function(i, obj){
		if ($('#' + $(obj).attr('id') + ' input').attr('checked') == 'checked') {
			mainAnnounceDenyCourseOne($(obj).attr('id'));
		}
	});
}

/* 全选 */
function mainAnnounceSelectAll() {
	if ($(this).attr('name') == 'friend') {
		$('#mainAnnounceFriend li :checkbox').attr('checked', true);
	} else {
		$('#mainAnnounce' + $(this).attr('name') + ' li :checkbox').attr('checked', true);
	}
}
/* 全不选 */
function mainAnnounceSelectNone() {
	if ($(this).attr('name') == 'friend') {
		$('#mainAnnounceFriend li :checkbox').attr('checked', false);
	} else {
		$('#mainAnnounce' + $(this).attr('name') + ' li :checkbox').attr('checked', false);
	}
}
/* 反选 */
function mainAnnounceSelectOpp() {
	var list;
	if ($(this).attr('name') == 'friend') {
		list = $('#mainAnnounceFriend :checkbox');
	} else {
		list = $('#mainAnnounce' + $(this).attr('name') + ' :checkbox');
	}
	$.each(list, function(i, obj) {
		if ($(obj).attr('checked') == 'checked') {
			$(obj).attr('checked', false);
		} else {
			$(obj).attr('checked', true);
		}
	});
}

/*进入好友个人主页*/
function enterFriendHomepage() {
	var friendId = $(this).attr('name');	
	$.get(
		Root + 'get_relation.php', {
			type: 'user',
			id: friendId
		}, 
		function (data) {
			if (data == 'Y') {
				jumpToFriendY(friendId, 'Y');
			} else if (data == 'N') {
				jumpToFriendN(friendId, 'N');
			} else {	// W
				jumpToFriendW(friendId, 'W');
			}
		}
	);
}

function enterCourseHomepage() {
	// 得到和此课程的关系
	var courseId = $(this).attr('name');
	// 根据关系判断权限
	$.get(
		Root + 'get_relation.php', {
			type: 'course',
			id: courseId
		}, 
		function (data) {
			if (data == 'M') {
				jumpToCourseM(courseId, 'M');
			} else if (data == 'A') {
				jumpToCourseA(courseId, 'A');
			} else if (data == 'W') {
				jumpToCourseW(courseId, 'W');
			} else {	// N
				jumpToCourseN(courseId, 'N');
			}
		}
	);
}

/*  */
function mainAnnounceFriendAfterCreate() {
	/* 三个选择 */
	$('#mainAnnounceFriend .mainAnnounceChooseAll').bind('click', mainAnnounceSelectAll);
	$('#mainAnnounceFriend .mainAnnounceChooseNone').bind('click', mainAnnounceSelectNone);
	$('#mainAnnounceFriend .mainAnnounceChooseOpp').bind('click', mainAnnounceSelectOpp);
	$('#mainAnnounceFriend .userPhoto').bind('click',enterFriendHomepage);
	$('#mainAnnounceFriend .user_name').bind('click',enterFriendHomepage);
}

function mainAnnounceCourseAfterCreate(id) {
	/* 三个选择 */
	$('#' + id + ' .mainAnnounceChooseAll').bind('click', mainAnnounceSelectAll);
	$('#' + id + ' .mainAnnounceChooseNone').bind('click', mainAnnounceSelectNone);
	$('#' + id + ' .mainAnnounceChooseOpp').bind('click', mainAnnounceSelectOpp);
	$('#' + id + ' .userPhoto').bind('click',enterFriendHomepage);
	$('#' + id + ' .user_name').bind('click',enterFriendHomepage);
}


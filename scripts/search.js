function searchInitPage(data) {
	if(data.length > 50) {	 //限制搜索关键词长度
		$('#searchInfo').text('[错误]搜索关键词不能超过50字');
		return;
	}
	$('#searchInfo').text('以下是对 "' + data + '" 的搜索结果');
	$.getJSON(	// user
		Root + 'search.php', {
			keyword: data,
			type: 'user'
		},
		searchSearchUserAfterPost
	);
	$.getJSON(	// course
		Root + 'search.php', {
			keyword: data,
			type: 'course'
		},
		searchSearchCourseAfterPost
	);	
}

function searchSearchUserAfterPost(data){
	searchSearchAfterPost(data, true);
	
}
function searchSearchCourseAfterPost(data){
	searchSearchAfterPost(data, false);
}
function searchSearchAfterPost(data, searchUser) {
	if (data == '0') {		// 尚未登录
		alert('您尚未登录，转至登录页面');
		globalUserId = -1;
		generalGotoLoginPage();
		return;
	}	else if (data == '') {		// 没有信息
		$('#searchInfo').text('没有查到和 "' + data + '" 相关的用户或者课程,请适当修改关键词后再试');
		return;
	}	else {
		if (searchUser) {	// 用户
			$.each(data, function(i, dataSingle){
				if (dataSingle['find_user_id'] != globalUserId) {
					$('#searchUser ul').append(createUser(dataSingle));					// 添加一行用户结果
					$('#apply' + dataSingle['find_user_id']).bind('click', function() {	// 添加申请状态图标的绑定
						$.post(
							Root + 'send_user_apply.php', {			// 发送申请
								from_id: globalUserId,
								to_id: dataSingle['find_user_id'],
								apply_content: '无'
							},
							function(retData) {						// 回调函数
								sendUserApplyCallback(retData, dataSingle['find_user_name'], dataSingle['find_user_id']);
							}
						);
					});
				}
			});
			$('li[name=U] .userName').bind('click', enterFriendHomepage);
			$('li[name=U] .userPhoto').bind('click', enterFriendHomepage);
		}	else {				// 课程
			$.each(data, function(i, dataSingle){
				$('#searchCourse ul').append(createCourse(dataSingle));
				if(dataSingle['verify'] == 'C'){
					var link = 'frmVerifyCourse.php?course_id='+dataSingle['course_id']+'&from_id='+globalUserId;
					$('#courseApply' + dataSingle['course_id']).attr('href', link);
					generalSetIFrame('#courseApply' + dataSingle['course_id'],450,350);	//设置课程验证密码弹出框
				} else {
					$('#courseApply' + dataSingle['course_id']).bind('click', function() {
						$.post(
							Root + 'send_course_apply.php', {
								from_id: globalUserId, 
								course_id: dataSingle['course_id'],
								apply_content: '无'
							},
							function(retData) {
								sendCourseApplyCallback(retData, dataSingle['course_name'], dataSingle['course_id']);
							}
						);
					});
				}
			});
			$('li[name=C] .courseName').bind('click', enterCourseHomepage);
			$('li[name=C] .userPhoto').bind('click', enterCourseHomepage);
			$('li[name=C] .userName').bind('click', enterFriendHomepage);
		}
	}
	T_(globalLanguage);
}

function sendUserApplyCallback(data, userName, userId) {
	if (data == 1) {
		alert('申请发送成功');
	}	else {
		alert('发送失败:'+data);
	}
	// 刷新用户关系
	$.get(	// user
		Root + 'get_relation.php', {
			type: 'user',
			id: userId
		}, 
		function(relation) {
			var $var = $('#apply' + userId);
			if (relation == 'N') {
				// do nothing
			}
			else if (relation == 'Y') {
				sendUserApplyCallbackAid($var, 'hasBeenFriend', userId, '已为好友');
			}
			else {	// W
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
function sendCourseApplyCallback(data, courseName, courseId) {
	if (data == 1) {
		alert('申请发送成功');
	}	else {
		alert('申请发送失败了: '+data);
	}
	// 刷新用户关系
	$.get(	// course
		Root + 'get_relation.php', {
			type: 'course', 
			id: courseId
		},
		function(relation) {
			var $var = $('#courseApply' + courseId);
			if (relation == 'N') {
				// do nothing
			}	else if (relation == 'A') {
				sendCourseApplyCallbackAid($var, 'hasBeenFriend', courseId, '已为好友');
			}	else if (relation == 'W') {
				sendCourseApplyCallbackAid($var, 'waitForAnswer', courseId, '申请中...');
			}
			else {	// M or T
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


function createUser(data) {
	var $newTr;
	var department = data['find_user_department'];
	var major = data['find_user_major'];
	if(!department){
		department = '未知院系';
	}
	if(!major) {
		major = '未知专业';
	}
	if (data['relation'] == 'N') {
		friended = 'friended';
		$newTr = $(
			'<li name="U" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['find_user_id'] + '" class="userPhoto" src="' + data['find_user_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">' 
			+ '<a  name="' + data['find_user_id'] + '" class="userName">' + data['find_user_name'] + '</a>'
			//+' <label class="emailInfo" >'+ data['find_user_email'] +'</label><br/>' 
			+ '<label class = "hometown"> '+ data['find_user_hometown'] + '</label>'
			+'<br/><label class="departmentMajor"><span domain="l10n">' + department + '</span> <span  domain="l10n">'
			+ major + '</span></label></div>' 
			+ '<a style = "float:right" id="apply' + data['find_user_id'] + '" class="sendUserApply" title="加为好友"  ></a>' 
			+ '</li>'
		);
	}	else if (data['relation'] == 'Y') {
		$newTr = $(
			'<li name="U" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['find_user_id'] + '" class="userPhoto" src="' + data['find_user_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">'
			+ '<a  name="' + data['find_user_id'] + '" class="userName">' + data['find_user_name'] + '</a>'
			//+' <label class="emailInfo" >'+ data['find_user_email'] +'</label><br/>' 
			+ '<label class = "hometown"> '+ data['find_user_hometown'] + '</label>'
			+'<br/><label class="departmentMajor"><span domain="l10n">' + department + '</span> <span  domain="l10n">'
			+ major + '</span></label></div>' 
			+ '<a style = "float:right" name="user' + data['find_user_id'] + '" class="hasBeenFriend" title="已为好友"></a>' 
			+ '</li>'
		);
	}	else {// W
		$newTr = $(
			'<li name="U" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['find_user_id'] + '" class="userPhoto" src="' + data['find_user_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">'
			+ '<a  name="' + data['find_user_id'] + '" class="userName">' + data['find_user_name'] + '</a>'
			//+' <label class="emailInfo" >'+ data['find_user_email'] +'</label><br/>' 
			+ '<label class = "hometown"> '+ data['find_user_hometown'] + '</label>'
			+'<br/><label class="departmentMajor"><span domain="l10n">' + department + '</span> <span  domain="l10n">'
			+ major + '</span></label></div>' 
			+ '<a style = "float:right" name="user' + data['find_user_id'] + '" class="waitForAnswer" title="申请中..."></a>' 
			+ '</li>'
		);
	}
	return $newTr;
}

function createCourse(data) {
	if (data['relation'] == 'N') {
		var $newTr = $(
			'<li name="C" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['course_id'] + '" class="userPhoto N" src="' + data['course_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">'
			+ '<a  name="' + data['course_id'] + '" class="courseName N">' + data['course_name'] + '</a>'
			+ '<br/>'
			+ '<label class="infoLabel">老师:  </label><a  name="' + data['teacher_id'] + '" class="userName">' + data['teacher_name'] + '</a>'
			+ '</div>'
			+ '<a style = "float:right" id="courseApply' + data['course_id'] + '" class="sendUserApply" title="申请加入"  ></a>' 
			+ '</li>'
		);
	}	else if (data['relation'] == 'A') {
		var $newTr = $(
			'<li name="C" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['course_id'] + '" class="userPhoto A" src="' + data['course_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">'
			+ '<a  name="' + data['course_id'] + '" class="courseName A">' + data['course_name'] + '</a>'
			+ '<br/>'
			+ '<label class="infoLabel">老师:  </label><a  name="' + data['teacher_id'] + '" class="userName">' + data['teacher_name'] + '</a>'
			+ '</div>'
			+ '<a style = "float:right" name="course' + data['course_id'] + '" class="hasBeenFriend" title="已加入"></a>' 
			+ '</li>'
		);
	}	else if (data['relation'] == 'W'){
		$newTr = $(
			'<li name="C" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['course_id'] + '" class="userPhoto W" src="' + data['course_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">'
			+ '<a  name="' + data['course_id'] + '" class="courseName W">' + data['course_name'] + '</a>'
			+ '<br/>'
			+ '<label class="infoLabel">老师:  </label><a  name="' + data['teacher_id'] + '" class="userName">' + data['teacher_name'] + '</a>'
			+ '</div>'
			+ '<a style = "float:right" name="course' + data['course_id'] + '" class="waitForAnswer" title="申请中..."></a>' 
			+ '</li>'
		);
	}	else {// M
		$newTr = $(
			'<li name="C" class="freshmilkNews">'
			+ '<div class="picDiv"><a  ><img name="' + data['course_id'] + '" class="userPhoto M" src="' + data['course_photo'] + '"></img></a></div>'
			+ '<div class="feedDiv">'
			+ '<a  name="' + data['course_id'] + '" class="courseName M">' + data['course_name'] + '</a>'
			+ '<br/>'
			+ '<label class="infoLabel">老师:  </label><a  name="' + data['teacher_id'] + '" class="userName">' + data['teacher_name'] + '</a>'
			+ '</div>'
			+ '<a style = "float:right" name="course' + data['course_id'] + '" class="manageCourse" title="课程管理者"></a>' 
			+ '</li>'
		);
	}
	return $newTr;
}
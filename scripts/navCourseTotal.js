function totalCourseInit(data) {
	$.getJSON(	// course
		Root + 'view_total_course.php',
		searchSearchCourseAfterPost
	);
}
function searchSearchCourseAfterPost(data){
	searchSearchAfterPost(data, false);
}
function searchSearchAfterPost(data, searchUser) {
	if (data == 0) {		// 尚未登录
		alert('您尚未登录，转至登录页面');
		globalUserId = -1;
		generalGotoLoginPage();
		return;
	}	else {
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
	T_(globalLanguage);
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
	}	else {	// M or T
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
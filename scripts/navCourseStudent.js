var curPage = 1;
var sumPage;

function courseStudentInitPage() {
	/* 这边设置初始界面的内容 */
	$('#noMoreNews').hide();
	$('#courseNewsPagePrev').css('visibility', 'hidden');
	$('#courseNewsPageNext').css('visibility', 'hidden');
	$('#courseStudentPagePrev').bind('click', courseStudentGetPrev);
	$('#courseStudentPageNext').bind('click', courseStudentGetNext);
	courseStudentGetStudent(false, false);
}

function courseStudentGetStudent(prevClick, nextClick) {	
	$.get(Root + 'get_page_nums.php', {	//获取页数的类型
			type: 'course_student',
			course_id: courseId
		},
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'view_studentlist.php', {
						course_id: courseId,
						cur_page: curPage
					},
					courseStudentAddPrevClick
				);
			} else if (nextClick) {
				$.getJSON(
					Root + 'view_studentlist.php', {
						course_id: courseId,
						cur_page: curPage
					},
					courseStudentAddNextClick
				);
			} else {
				$.getJSON(
					Root + 'view_studentlist.php', {
						course_id: courseId,
						cur_page: curPage
					},
					courseStudentAdd
				);
			}
		}
	); 
}
function courseStudentAdd(data) {
	addStudent(data, false, false);
}
function courseStudentAddPrevClick(data) {
	addStudent(data, true, false);
}
function courseStudentAddNextClick(data) {
	addStudent(data, false, true);
}
function addStudent(data, prev, next) {
	if (data == '0') {		//无权查看该页面或者尚未登录
		alert('尚未登录');
		generalGotoLoginPage();
	} else if (data == null) {
		if (prev) {
			courseStudentGetPrevCallback(false);
		} else if (next) {
			courseStudentGetNextCallback(false);
		}
	} else {
		courseStudentDeleteAll();
		$.each(data, function(index, dataSingle) {
			$('#courseStudentList ul').append(createStudent(dataSingle));
		});
		if (prev) {	
			courseStudentGetPrevCallback(true);
		} else if (next) {
			courseStudentGetNextCallback(true);
		} if (courseRelation == 'M' || courseRelation == 'T') {
			$('a[title=删除]').unbind();
			$('a[title=删除]').bind('click', courseStudentDeleteOne);
		} else {
			// do nothing
		}
		$('.userName').bind('click', enterFriendHomepage);
		$('.userPhoto').bind('click', enterFriendHomepage);
		
		// 添加翻译
		if(globalLanguage == 'zh-cn') {	/*翻译中文*/
			T_(globalLanguage);
		}
	}
	processPage();
}
/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#courseStudentPagePrev').css('visibility', 'hidden');
	} else if (curPage == '2') {
		$('#courseStudentPagePrev').css('visibility', 'visible');
	} if (curPage < sumPage) {
		$('#courseStudentPageNext').css('visibility', 'visible');
	} else {
		$('#courseStudentPageNext').css('visibility', 'hidden');
	}
}

/* 移除所有动态 */
function courseStudentDeleteAll() {
	$('#courseStudentList li').remove();
	$('#courseStudentList hr').remove();
}


/* 上一页 */
function courseStudentGetPrev() {
	curPage--;
	courseStudentGetStudent(true, false);
}
function courseStudentGetPrevCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreStudent').show();
		curPage++;
	} else {
		$('#noMoreStudent').hide();
	}
}

/* 下一页 */
function courseStudentGetNext() {
	curPage++;
	courseStudentGetStudent(false, true);
}
function courseStudentGetNextCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreStudent').show();
		curPage--;
	} else {
		$('#noMoreStudent').hide();
	}
}



/* 生成一条记录 */
function createStudent(data) {
	var department = data['user_department'];
	var major = data['user_major'];
	if(!department){
		department = '未知院系';
	}
	if(!major) {
		major = '未知专业';
	}
	if (courseRelation == 'M' || courseRelation == 'T') {
		var $newTr = $(
			'<li id="student' + data['user_id'] +'" class="courseStudentLi">'
			+'<div class="picDiv"><img name="' 
			+ 	data['user_id'] + '" class="userPhoto" src="' + data['user_photo'] + '"></img></div>'
			+ '<div class="studentInfoDiv"><a  name="' + data['user_id'] + '" class="userName">' + data['user_name'] + '</a>'
			+ '<label class = "emailInfo">' + data['user_email'] + '</label>'
			+'<br/><label class="departmentMajor"><span domain="l10n">' + department + '</span> <br /> <span  domain="l10n">'
			+ major + '</span></label></div>' 
			+ '<a name="' + data['user_id'] + '" class="delDiv courseDelete" title="删除"  ></a>'
			+ '</li>'
		);
	} else {
		var $newTr = $(
			'<li id="student' + data['user_id'] +'"  class="courseStudentLi">'
			+'<div class="picDiv"><img name="' 
			+ data['user_id'] + '" class="userPhoto" src="' + data['user_photo'] + '"></img></div>'
			+ '<div class="studentInfoDiv"><a  name="' + data['user_id'] + '" class="userName">' + data['user_name'] + '</a>'
			//+ '<label class = "emailInfo">' + data['user_email'] + '</label>'
			+'<br/><label class="departmentMajor"><span domain="l10n">' + department + '</span> <br /> <span  domain="l10n">'
			+ major + '</span></label></div>' 
			+ '</li>'
		);
	}
	return $newTr;
}

/* 删除一名学生 */
function courseStudentDeleteOne() {
	var studentId = $(this).attr('name');
	$.get(
		Root + 'del_student.php', {
			course_id:courseId,
			student_id: studentId
		},
		function(data) {
			if (data == '1') {	// 成功删除
				$('#student' + studentId).remove();
			} else {
				alert(data);	//数据库错误了
			}
		}
	);
}

/*进入好友个人主页*/
function enterFriendHomepage() {
	// 得到和此人的关系
	var friendId = $(this).attr('name');
	if (friendId == globalUserId) {	// 查看自己 
		changeNav(true, true, 'navUser', 'navUser', 'navUserInfo');
		changeAccount(true);
		changeSearch(true);
		changeContentPane('navUserInfo.html');
	} else {
		// 根据关系判断权限
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
}

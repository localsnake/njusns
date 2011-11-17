var curPage = 1;
var sumPage;

function mainFreshmilkInitPage() {
	/* 这边设置初始界面的内容 */
	$('#mainFreshmilkPagePrev').css('visibility', 'visible');
	$('#mainFreshmilkPageNext').css('visibility', 'hidden');
	/* 这边注册各种事件 */
	$('#mainFreshmilkPagePrev').bind('click', mainFreshmilkGetPrev);
	$('#mainFreshmilkPageNext').bind('click', mainFreshmilkGetNext);
	mainFreshmilkGetNews(false, false);
}


/* 得到新鲜事数据 */
function mainFreshmilkGetNews(prevClick, nextClick) {
	$.get(Root + 'get_page_nums.php?type=freshmilk', //获取页数的类型
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'view_freshmilk.php?cur_page=' + curPage + '&lang='+ globalLanguage,
					mainFreshmilkAddPrevClick
				);
			} else if (nextClick) {
				$.getJSON(
					Root + 'view_freshmilk.php?cur_page=' + curPage+ '&lang='+ globalLanguage,
					mainFreshmilkAddNextClick
				);
			} else {
				$.getJSON(
					Root + 'view_freshmilk.php?cur_page=' + curPage+ '&lang='+ globalLanguage,
					mainFreshmilkAdd
				);
			}
		}
	); 
}

/* 处理新鲜事返回 */
function mainFreshmilkAdd(data) {
	addFreshmilk(data, false, false);
}
function mainFreshmilkAddPrevClick(data) {
	addFreshmilk(data, true, false);
}
function mainFreshmilkAddNextClick(data) {
	addFreshmilk(data, false, true);
}
function addFreshmilk(data, prev, next) {
	if (data == '0') {
		alert('尚未登录');
		generalGotoLoginPage();	
	} else if (data == null) {
		$('#page').hide();
		$('#mainStartInfo').show();
		if (prev) {
			mainFreshmilkGetPrevCallback(false);
		} else if (next) {
			mainFreshmilkGetNextCallback(false);
		}
	} else {
		mainFreshmilkDeleteAll();
		$.each(data, function(index, dataSingle) {
			$('#mainFreshmilkList ul').append(createFreshmilk(dataSingle));
		});
		if (prev) {	
			mainFreshmilkGetPrevCallback(true);
		} else if (next) {
			mainFreshmilkGetNextCallback(true);
		}
		$('.delDiv').unbind();
		$('.delDiv').bind('click', mainFreshmilkDeleteOne);
		$('li[name=U] .user_name').bind('click', enterFriendHomepage);
		$('li[name=U] .userPhoto').bind('click', enterFriendHomepage);
		$('li[name=C] .user_name').bind('click', enterCourseHomepage);
		$('li[name=C] .userPhoto').bind('click', enterCourseHomepage);
	}
	processPage();
}
/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#mainFreshmilkPagePrev').css('visibility', 'hidden');
	} else {
		$('#mainFreshmilkPagePrev').css('visibility', 'visible');
	}
	if (curPage < sumPage) {
		$('#mainFreshmilkPageNext').css('visibility', 'visible');
	} else {
		$('#mainFreshmilkPageNext').css('visibility', 'hidden');
	}
}


/* 生成一条新鲜事 */
function createFreshmilk(data) {
	var actionPre;
	var actionSuf;
	if (data['freshmilk_type'] == 'C') {	// 课程	
		var $newDiv = $('<li id="mainFreshmilk' + data['freshmilk_id'] + '" class="freshmilkNews" name = "' + data['freshmilk_type'] + '">'
						+ '<div class="picDiv">'
						+ '<a>'
						+ '<img class="userPhoto" name ="' + data['from_id'] + '" src="' + data['from_photo'] + '"></img></a></div>'
						+ '<div class="feedDiv">'
						+ '<a name ="' + data['from_id'] + '" class = "user_name">' + data['from_name'] + '</a>'
						+ '<label class="content">' + data['freshmilk_content'] + '</label>'
						+ '<br/>'
						+ '<label class="time">(' + data['freshmilk_time'] + ')</label></div>'
						+ '<a class="delDiv courseDelete" name="' + data['freshmilk_id'] + '" title="删除"></a>'
						+ '</li>');		// Change here
	} else {
		var $newDiv = $('<li id="mainFreshmilk' + data['freshmilk_id'] + '" class="freshmilkNews" name = "' + data['freshmilk_type'] + '">'
						+ '<div class="picDiv">'
						+ '<a>'
						+ '<img class="userPhoto" name ="' + data['from_id'] + '" src="' + data['from_photo'] + '"></img></a></div>'
						+ '<div class="feedDiv">'
						+ '<a name ="' + data['from_id'] + '" class = "user_name">' + data['from_name'] + '</a>'
						+ '<label class="content">' + data['freshmilk_content'] + '</label>'
						+ '<br/>'
						+ '<label class="time">(' + data['freshmilk_time'] + ')</label></div>'
						+ '<a class="delDiv courseDelete" name="' + data['freshmilk_id'] + '" title="删除"></a>'
						+ '</li>');	
	}
	return $newDiv;
}

/*进入好友个人主页*/
function enterFriendHomepage() {
	var friendId = $(this).attr('name');
	// 先判断是否为好友
	$.get(	// user
		Root + 'get_relation.php', {
			type: 'user',
			id: friendId
		}, 
		function(relation) {
			if (relation == 'N') {
				jumpToFriendN(friendId, 'N');
			} else if (relation == 'Y') {
				jumpToFriendY(friendId, 'Y');
			} else {	// W
				jumpToFriendW(friendId, 'W');			
			}
		}
	);
}

/*进入课程主页*/
function enterCourseHomepage() {
	var courseId = $(this).attr('name');
	var courseRelation;
	// 刷新用户关系
	$.get(	// course
		Root + 'get_relation.php', {
			type: 'course', 
			id: courseId
		},
		function(relation) {
			if (relation == 'M' || relation == 'T') {
				jumpToCourse(courseId, relation);
			} else if (relation == 'A') {
				jumpToCourse(courseId, 'A');
			} else if (relation == 'W') {
				jumpToCourse(courseId, 'W');
			} else {	// N
				jumpToCourse(courseId, 'N');
			}
		}
	);
}

/* 删除一条新鲜事 */
function mainFreshmilkDeleteOne() {
	var freshmilkId = $(this).attr('name');
	$.get(
		Root+'del_freshmilk.php?freshmilk_id=' + freshmilkId, 
		function(data) {
			if (data == '1') {
				$('#mainFreshmilk' + freshmilkId).remove();
				$('#hr' + freshmilkId).remove();
			} else {
				alert('删除失败，我也不知道该肿么办');
			}
		}
	);
}


/* 移除所有新鲜事 */
function mainFreshmilkDeleteAll() {
	$('#mainFreshmilkList li').remove();
}

/* 上一页 */
function mainFreshmilkGetPrev() {
	curPage--;
	mainFreshmilkGetNews(true, false);
}
function mainFreshmilkGetPrevCallback(suc) {
	if (! suc) {	// 没有新鲜事，或未登录
		curPage++;
	}
}

/* 下一页 */
function mainFreshmilkGetNext() {
	curPage++;
	mainFreshmilkGetNews(false, true);
}
function mainFreshmilkGetNextCallback(suc) {
	if (! suc) {	// 没有新鲜事，或未登录
		curPage--;
	}
}
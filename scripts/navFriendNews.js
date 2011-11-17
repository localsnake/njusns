var curPage = 1;
var sumPage;
var userId;

function friendNewsInitPage(userIdIn, status) {	// status 为预留，现在都是'Y'，即能看到friendNews的都是好友，而非陌生人
	/* 这边设置初始界面的内容 */
	userId = userIdIn;
	//$('#noMoreNews').hide();
	$('#friendNewsPagePrev').css('visibility', 'hidden');
	$('#friendNewsPageNext').css('visibility', 'hidden');
	/* 这边注册各种事件 */
	$('#friendNewsPagePrev').bind('click', friendNewsGetPrev);
	$('#friendNewsPageNext').bind('click', friendNewsGetNext);
	friendNewsGetNews(false, false);
}
function friendNewsGetNews(prevClick, nextClick) {
	$.get(Root + 'get_page_nums.php', {//获取页数的类型
			type: 'user_news',
			user_id: userId
		},
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'view_user_news.php', {
						user_id: userId, 
						cur_page: curPage
					},
					friendNewsAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root + 'view_user_news.php', {
						user_id: userId, 
						cur_page: curPage
					},
					friendNewsAddNextClick
				);
			}
			else {
				$.getJSON(
					Root + 'view_user_news.php', {
						user_id: userId, 
						cur_page: curPage
					},
					friendNewsAdd
				);
			}
		}
	); 
}



/* 处理用户动态返回 */
function friendNewsAdd(data) {
	addNews(data, false, false);
}
function friendNewsAddPrevClick(data) {
	addNews(data, true, false);
}
function friendNewsAddNextClick(data) {
	addNews(data, false, true);
}
function addNews(data, prev, next) {
	if (data == '0') {
		alert('这里要添加添加好友的页面');
		changeNav(false, false, '', '', '');
		changeAccount(false);
		changeSearch(false);
		changeContentPane('navFriendInfo.html');		
	}
	else if (data == null) {
		if (prev) {
			friendNewsGetPrevCallback(false);
		}
		else if (next) {
			friendNewsGetNextCallback(false);
		}
	}
	else {
		friendNewsDeleteAll();
		$.each(data, function(index, dataSingle) {
			$('#navUserNewsList ul').append(createNews(dataSingle));
		});
		if (prev) {	
			friendNewsGetPrevCallback(true);
		}
		else if (next) {
			friendNewsGetNextCallback(true);
		}
		$('a[title=删除]').unbind();
		$('a[title=删除]').bind('click', friendNewsDeleteOne);
	}
	processPage();
}
/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#friendNewsPagePrev').css('visibility', 'hidden');
	}
	else if (curPage == '2') {
		$('#friendNewsPagePrev').css('visibility', 'visible');;
	}
	if (curPage < sumPage) {
		$('#friendNewsPageNext').css('visibility', 'visible');
	}
	else {
		$('#friendNewsPageNext').css('visibility', 'hidden');
	}
}


/* 移除所有动态 */
function friendNewsDeleteAll() {
	$('#navUserNewsList li').remove();
}


/* 上一页 */
function friendNewsGetPrev() {
	curPage--;
	friendNewsGetNews(true, false);
}
function friendNewsGetPrevCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreNews').show();
		curPage++;
	}
	else {
		$('#noMoreNews').hide();
	}
}

/* 下一页 */
function friendNewsGetNext() {
	curPage++;
	friendNewsGetNews(false, true);
}
function friendNewsGetNextCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreNews').show();
		curPage--;
	}
	else {
		$('#noMoreNews').hide();
	}
}


/* 生成一条用户动态 */
function createNews(data) {
	var $newDiv = $('<li id="friendNews' + data['news_id'] + '" class="freshmilkNews">'
					+ '<div class="picDiv"><img class="userPhoto" src="' + data['user_photo'] + '">' + '</img></div>'
					+ '<div class="feedDiv"><label id="' + data['user_id'] + '" class="user_name">' + data['user_name'] + '</label>'
					+ '<label class="content">' + data['news_content'] + '</label>'
					+ '<br/><label class="time">(' + data['news_time'] + ')</label></div>'
					+ '</li>');
	return $newDiv;
}
/* 删除一条用户动态 */
function friendNewsDeleteOne() {
	var newsId = $(this).attr('name');
	$.get(
		Root + 'del_user_news.php?news_id=' + newsId, 
		function(data) {
			if (data == '1') {
				$('#userNews' + newsId).remove();
			}
			else {
				alert('删除失败，我也不知道该肿么办');
			}
		}
	);
}
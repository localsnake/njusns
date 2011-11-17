var curPage = 1;
var sumPage;

function userNewsInitPage() {
	/* 这边设置初始界面的内容 */
	$('#noMoreNews').hide();
	$('#userNewsPagePrev').css('visibility', 'hidden');
	$('#userNewsPageNext').css('visibility', 'hidden');
	/* 这边注册各种事件 */
	$('#userNewsPagePrev').bind('click', userNewsGetPrev);
	$('#userNewsPageNext').bind('click', userNewsGetNext);
	userNewsGetNews(false, false);
}

/* 得到用户动态 */
function userNewsGetNews(prevClick, nextClick) {
	$.get(Root + 'get_page_nums.php', {//获取页数的类型
			type: 'user_news',
			user_id: globalUserId
		},
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'view_user_news.php', {
						user_id: globalUserId, 
						cur_page: curPage,
						lang:globalLanguage
					},
					userNewsAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root + 'view_user_news.php', {
						user_id: globalUserId, 
						cur_page: curPage,
						lang:globalLanguage
					},
					userNewsAddNextClick
				);
			}
			else {
				$.getJSON(
					Root + 'view_user_news.php', {
						user_id: globalUserId, 
						cur_page: curPage,
						lang:globalLanguage
					},
					userNewsAdd
				);
			}		
		}
	); 
}

/* 处理用户动态返回 */
function userNewsAdd(data) {
	addNews(data, false, false);
}
function userNewsAddPrevClick(data) {
	addNews(data, true, false);
}
function userNewsAddNextClick(data) {
	addNews(data, false, true);
}
function addNews(data, prev, next) {
	if (data == '0') {
		alert('需要修改成加为好友页面');
		changeNav(false, false, '', '', '');
		changeAccount(false);
		changeSearch(false);
		changeContentPane('login.html');		
	}
	else if (data == null) {
		if (prev) {
			userNewsGetPrevCallback(false);
		}
		else if (next) {
			userNewsGetNextCallback(false);
		}
	}
	else {
		userNewsDeleteAll();
		$.each(data, function(index, dataSingle) {
			$('#navUserNewsList ul').append(createNews(dataSingle));
		});
		if (prev) {	
			userNewsGetPrevCallback(true);
		}
		else if (next) {
			userNewsGetNextCallback(true);
		}
		//$('a[title=删除]').unbind();
		//$('a[title=删除]').bind('click', userNewsDeleteOne);
		$('.delDiv').unbind();
		$('.delDiv').bind('click', userNewsDeleteOne);
	}
	processPage();
}
/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#userNewsPagePrev').css('visibility', 'hidden');
	}
	else if (curPage == '2') {
		$('#userNewsPagePrev').css('visibility', 'visible');;
	}
	if (curPage < sumPage) {
		$('#userNewsPageNext').css('visibility', 'visible');
	}
	else {
		$('#userNewsPageNext').css('visibility', 'hidden');
	}
}


/* 移除所有动态 */
function userNewsDeleteAll() {
	$('#navUserNewsList li').remove();
}


/* 上一页 */
function userNewsGetPrev() {
	curPage--;
	userNewsGetNews(true, false);
}
function userNewsGetPrevCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreNews').show();
		curPage++;
	}
	else {
		$('#noMoreNews').hide();
	}
}

/* 下一页 */
function userNewsGetNext() {
	curPage++;
	userNewsGetNews(false, true);

}
function userNewsGetNextCallback(suc) {
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
	var $newDiv = $('<li id="userNews' + data['news_id'] + '" class="freshmilkNews">'
					+ '<div class="picDiv"><img class="userPhoto" src="' + data['user_photo'] + '">' + '</img></div>'
					+ '<div class="feedDiv"><label id="' + data['user_id'] + '" class="user_name">' + data['user_name'] + '</label>'
					+ '<label class="content">' + data['news_content'] + '</label>'
					+ '<br/><label class="time">(' + data['news_time'] + ')</label></div>'
					+ '<a name="' + data['news_id'] + '" class="delDiv courseDelete" title="删除"  ></a>'
					+ '</li>');
	return $newDiv;
}

/* 删除一条用户动态 */
function userNewsDeleteOne() {
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
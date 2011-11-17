var curPage = 1;
var sumPage;

function friendInfoInitPage() {	
	/* 这边设置初始界面的内容 */
	$('#noMoreNews').hide();
	$('#friendInfoPagePrev').css('visibility', 'hidden');
	$('#friendInfoPageNext').css('visibility', 'hidden');
	/* 这边注册各种事件 */
	$('#friendInfoPagePrev').bind('click', friendInfoGetPrev);
	$('#friendInfoPageNext').bind('click', friendInfoGetNext);
	friendInfoGetInfo(false, false);
}

/* 得到好友资料 */
function friendInfoGetInfo(prevClick, nextClick) {
	$.get(Root + 'get_page_nums.php?type=user_friend', //获取页数的类型
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root+'view_friend.php?cur_page=' + curPage,
					friendInfoAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root+'view_friend.php?cur_page=' + curPage,
					friendInfoAddNextClick
				);
			}
			else {
				$.getJSON(
					Root+'view_friend.php?cur_page=' + curPage,
					friendInfoAdd
				);
			}
		}
	); 
}

/* 处理新鲜事返回 */
function friendInfoAdd(data) {
	addInfo(data, false, false);
}
function friendInfoAddPrevClick(data) {
	addInfo(data, true, false);
}
function friendInfoAddNextClick(data) {
	addInfo(data, false, true);
}
function addInfo(data, prev, next) {
	if (data == '0') {
		alert('尚未登录或者无权查看该用户页面');
		generalGotoLoginPage();		
	}
	else if (data == null) {
		if (prev) {
			friendInfoGetPrevCallback(false);
		}
		else if (next) {
			friendInfoGetNextCallback(false);
		}
	}
	else {
		friendInfoDeleteAll();
		$.each(data, function(index, dataSingle) {
			$('#friendInfoList ul').append(createFriendInfo(dataSingle));
		});
		if (prev) {	
			friendInfoGetPrevCallback(true);
		}
		else if (next) {
			friendInfoGetNextCallback(true);
		}
	}
	processPage();
	$('a[title=删除好友]').bind('click', deleteFriend);	
	$('.user_name').bind('click', enterFriendHomepage);
	$('.userPhoto').bind('click', enterFriendHomepage);
	// 添加翻译
	if(globalLanguage == 'zh-cn') {	/*翻译中文*/
		T_(globalLanguage);
	}
}
/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#friendInfoPagePrev').css('visibility', 'hidden');
	}
	else if (curPage == '2') {
		$('#friendInfoPagePrev').css('visibility', 'visible');;
	}
	if (curPage < sumPage) {
		$('#friendInfoPageNext').css('visibility', 'visible');
	}
	else {
		$('#friendInfoPageNext').css('visibility', 'hidden');
	}
}


/* 删除好友 */
function deleteFriend() {
	var friendId = $(this).attr('name');
	$.get(
		Root + 'del_friend.php', {
			friend_id: friendId
		},
		function(data) {
			if (data == '1') {	// 成功删除
				$('li#friendInfo' + friendId).remove();
			}
			else {
				alert('删除失败');
			}
		}
	);
}
/*进入好友个人主页*/
function enterFriendHomepage() {
	var friendId = $(this).attr('name');
	jumpToFriendY(friendId, 'Y');
}

/* 生成一条好友信息 */
function createFriendInfo(data) {
	var department = data['friend_department'];
	var major = data['friend_major'];
	if(!department){
		department = '未知院系';
	}
	if(!major) {
		major = '未知专业';
	}
	var $newDiv = $(
		'<li id="friendInfo' + data['friend_id'] + '" class="freshmilkNews">'+ 
			'<div class="picDiv">'+
				'<a>'+
				'<img class="userPhoto" name = "' + data['friend_id'] +	'" src="' + data['friend_photo'] + '">'+
				'</img>'+
				'</a>'+
			'</div>'+
		'<div class="feedDiv">'+
		'<label name="' + data['friend_id'] + '" class="name">' + '<a name = "'+ data['friend_id'] +'" class = "user_name">' + data['friend_name'] + '</a></label>' + 
		//' <label class="emailInfo" >'+ data['friend_email'] +'</label>' +
		'<br/><label class="departmentMajor"><span domain="l10n">' + department + '</span> <span  domain="l10n">'
			+ major + '</span></label></div>'  +
		'<a name="' + data['friend_id'] + '" class="delDiv courseDelete" title="删除好友"></a></li>'
		// relation的返回没有用到
	);
	return $newDiv;
}

/* 移除所有新鲜事 */
function friendInfoDeleteAll() {
	$('#friendInfoList li').remove();
}


/* 上一页 */
function friendInfoGetPrev() {
	curPage--;
	friendInfoGetInfo(true, false);
}
function friendInfoGetPrevCallback(suc) {
	if (! suc) {	// 没有好友，或未登录
		$('#noMoreNews').show();
		curPage++;
	}
	else {
		$('#noMoreNews').hide();
	}
}

/* 下一页 */
function friendInfoGetNext() {
	curPage++;
	friendInfoGetInfo(false, true);
}
function friendInfoGetNextCallback(suc) {
	if (! suc) {	// 没有好友，或未登录
		$('#noMoreNews').show();
		curPage--;
	}
	else {
		$('#noMoreNews').hide();
	}
}

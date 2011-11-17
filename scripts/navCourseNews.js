var curPage = 1;
var sumPage;
var curContent = '';
var initContet = '有什么课程通知需要发表?';

function courseNewsInitPage(courseIdIn, relation) {
	/* 这边设置初始界面的内容 */
	//$('#noMoreNews').hide();
	$('#courseNewsPagePrev').css('visibility', 'hidden');
	$('#courseNewsPageNext').css('visibility', 'hidden');
	courseId = courseIdIn;
	courseRelation = relation;
	/* 这边注册各种事件 */
	
	if(courseRelation == 'M' || courseRelation == 'T') { //管理员发布状态
		$('#courseNewsInfo').show();
		$('#notice_course_id').val(courseId);
		$('#uploadNoticeBtn').unbind();
		$('#uploadNoticeBtn').bind('click',uploadCourseNotice);
		$('#noticeContent').bind('click',function() { 
			if($('#noticeContent').val() == initContet){
				$('#noticeContent').val(''); 
				$('#noticeContent').css('color','#454545'); 
			}
		});	
		$('#noticeContent').keydown(function() { 
				$('#wordCount').html($('#noticeContent').val().length);
		});	
		$('#noticeContent').keyup(function() { 
				$('#wordCount').html($('#noticeContent').val().length);
		});	
	}
	$('#courseNewsPagePrev').unbind();
	$('#courseNewsPageNext').unbind();
	$('#courseNewsPagePrev').bind('click', courseNewsGetPrev);
	$('#courseNewsPageNext').bind('click', courseNewsGetNext);
	courseNewsGetNews(false, false);
	
}

function uploadCourseNotice() {
	var textLength = $('#noticeContent').val().length;
	if($('#noticeContent').val() == curContent){
		alert('请不要连续重复发送相同的通知');
		return;
	}
	if($('#noticeContent').val()==initContet){
		return;
	}
	if(textLength>0 && textLength<=255){
		$.post(Root+'upload_course_notice.php', {
				course_id:courseId,
				notice_content: $('#noticeContent').val()
			},
			function(data){
				if(data == '1') {
					alert('发送成功');
					$('#noticeContent').val(''); 
					$('#wordCount').html('0');
					courseNewsGetNews(false, false);
				} else {
					alert('发生错误，错误码:' + data);
				}
			}
		);
		curContent = $('#noticeContent').val();
	}
}

function courseNewsGetNews(prevClick, nextClick) {
	$.get(Root + 'get_page_nums.php', {//获取页数的类型
			type: 'course_news',
			course_id: courseId
		},
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'get_coursenotice.php', {
						course_id: courseId,
						cur_page: curPage,
						lang:globalLanguage
					},
					courseNewsAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root + 'get_coursenotice.php', {
						course_id: courseId,
						cur_page: curPage,
						lang:globalLanguage
					},
					courseNewsAddNextClick
				);
			}
			else {
				$.getJSON(
					Root + 'get_coursenotice.php', {
						course_id: courseId,
						cur_page: curPage,
						lang:globalLanguage
					},
					courseNewsAdd
				);
			}
		}
	); 
}

/* 处理用户动态返回 */
function courseNewsAdd(data) {
	addNews(data, false, false);
}
function courseNewsAddPrevClick(data) {
	addNews(data, true, false);
}
function courseNewsAddNextClick(data) {
	addNews(data, false, true);
}
function addNews(data, prev, next) {
	if (data == '0') {		//无权查看该页面或者尚未登录
		alert('尚未登录');
		generalGotoLoginPage();
	}	else if (data == null) {
		if (prev) {
			courseNewsGetPrevCallback(false);
		}
		else if (next) {
			courseNewsGetNextCallback(false);
		}
	}	else {
		courseNewsDeleteAll();
		$.each(data, function(index, dataSingle) {
			$('#navCourseNewsList ul').append(createNews(dataSingle));
		});
		if (prev) {	
			courseNewsGetPrevCallback(true);
		}	else if (next) {
			courseNewsGetNextCallback(true);
		}
		if (courseRelation == 'M' || courseRelation == 'T') {
			$('.delDiv').unbind();
			$('.delDiv').bind('click', courseNewsDeleteOne);
		}
	}
	processPage();
}
/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#courseNewsPagePrev').css('visibility', 'hidden');
	}
	else if (curPage == '2') {
		$('#courseNewsPagePrev').css('visibility', 'visible');
	}
	if (curPage < sumPage) {
		$('#courseNewsPageNext').css('visibility', 'visible');
	}
	else {
		$('#courseNewsPageNext').css('visibility', 'hidden');
	}
}


/* 移除所有动态 */
function courseNewsDeleteAll() {
	$('#navCourseNewsList li').remove();
}


/* 上一页 */
function courseNewsGetPrev() {
	curPage--;
	courseNewsGetNews(true, false);
}
function courseNewsGetPrevCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreNews').show();
		curPage++;
	}
	else {
		$('#noMoreNews').hide();
	}
}

/* 下一页 */
function courseNewsGetNext() {
	curPage++;
	courseNewsGetNews(false, true);
}
function courseNewsGetNextCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		$('#noMoreNews').show();
		curPage--;
	}
	else {
		$('#noMoreNews').hide();
	}
}


function createNews(data) {
	if (courseRelation == 'M' || courseRelation == 'T') {	//助教或者老师都可以删除动态
		var $newLi = $(
			'<li id="courseNews' + data['course_notice_id'] + '"  class="freshmilkNews">'
			+ '<div class="picDiv"><img class="userPhoto" src="'+ data['course_photo'] + '"></img></div>'
			+ '<div class="feedDiv"><label class="user_name">' + data['course_name']  + '</label>'
			+ '<label class="content">' + data['notice_content'] + '</label>'		
			+ '<br/><label class="time">(' + data['notice_time'] + ')</label></div>'
			+ '<a class="delDiv courseDelete" name="' + data['course_notice_id']  + '" title="删除"  ></a>'
			+ '</li>'
		);
	}
	else {
		var $newLi = $(
			'<li id="courseNews' + data['course_notice_id'] + '"  class="freshmilkNews">'
			+ '<div class="picDiv"><img class="userPhoto" src="'+ data['course_photo'] + '"></img></div>'
			+ '<div class="feedDiv"><label class="user_name">' + data['course_name']  + '</label>'
			+ '<label class="content">' + data['notice_content'] + '</label>'	
			+ '<br/><label class="time">(' + data['notice_time'] + ')</label></div>'			
			+ '</li>'
		);
	}
	return $newLi;
}
function courseNewsDeleteOne() {
	var newsId = $(this).attr('name');
	$.get(
		Root + 'del_course_news.php?course_id=' + courseId + '&news_id=' + newsId, 
		function(data) {
			if (data == '1') {
				$('#courseNews' + newsId).remove();
			}
			else {
				alert(data);
			}
		}
	);
}
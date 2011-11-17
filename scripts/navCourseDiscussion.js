var release_array = new Array();
var cur_area_id;
var cur_release_id;
var editor1,editor2,editor3;
var permission_no = 1;
// 记录讨论区中页数,帖子区回复页数
var curPage = 1,responseCurPage = 1;
var sumPage,responseSumPage;
var cur_response_user_id = 0;
var cur_response_user_name = "";

/*初始化讨论区列表*/
function init_discussion_area_list() {
	$("#editReleaseArea").hide();		//隐藏编辑帖子容器
	$("#responseDiv").hide();			//隐藏回复区域
	$("#responseArea").hide();			//隐藏回复输入框
	$("#releaseArea").hide();			//隐藏发帖输入框
	$("#releaseTitle").hide();			//隐藏贴子标题
	$("#newResponseButton").hide();	//隐藏回帖按钮
	$('#delReleaseButton').hide();
	$('#editReleaseButton').hide();
	
	$("#newReleaseButton").show();		//显示发帖按钮
	$("#releaseDiv").show();			//显示所有贴子区域
	$("#areaName").show();				//显示讨论区名
	
	$.getJSON(Root+"get_discussionarea_info.php",{course_id:courseId},function(Info) {
		var area_id = Info["discussion_area_id"];
		if(area_id == 0) {
			alert('抱歉,该讨论区不存在');
			return;
		}
		cur_area_id = area_id;
		$("#areaName").html(Info["discussion_area_name"]);			
		$("#areaName").removeAttr("href");
		$("#releasePagePrev").unbind();
		$("#releasePageNext").unbind();
		$("#releasePagePrev").bind('click', releaseGetPrev);
		$("#releasePageNext").bind('click', releaseGetNext);
		set_release_list();	
		$("#responsePagePrev").bind('click', responseGetPrev);
		$("#responsePageNext").bind('click', responseGetNext);
	});	
}
/*初始化贴子列表*/
function init_discussion_release_list(area_id) {
	curPage = 1;
	sumPage = 1;
	cur_area_id = area_id;
	$("#editReleaseArea").hide();		//隐藏编辑帖子容器
	$("#responseDiv").hide();			//隐藏回复容器
	$("#responseArea").hide();			//隐藏回复输入框
	$("#releaseArea").hide();			//隐藏发帖输入框
	$("#releaseTitle").hide();			//隐藏贴子标题
	$("#newResponseButton").hide();	//隐藏回帖按钮
	$('#delReleaseButton').hide();
	$('#editReleaseButton').hide();
	$("#newReleaseButton").show();		//显示发帖按钮
	//$("#newReleaseTitle").val("");		//清空发帖标题框
	//$("#newReleaseContent").val("");	//清空发帖内容
	//$("#areaName").removeAttr("href");
	$("#releasePagePrev").unbind();
	$("#releasePageNext").unbind();
	$("#releasePagePrev").bind('click', releaseGetPrev);
	$("#releasePageNext").bind('click', releaseGetNext);
	set_release_list();	
	$("#releaseDiv").show();
	$("#areaName").show();
	$("#responsePagePrev").bind('click', responseGetPrev);
	$("#responsePageNext").bind('click', responseGetNext);
}
/* 初始化回复区域 */
function set_release_list() {
	var table_header = '<tr><th class="textTitle">标题</th><th class="textAuthor">作者</th><th class="textTime">发布时间</th><th class="textCount">回帖数</th>';
	var table_row = '';
	var table_whole = '';
	$.get(Root+"get_page_nums.php",{discussion_area_id:cur_area_id,type:'discussion_release'},function(reply) {
		sumPage = reply;
		processPage();
	});
	$.getJSON(Root+"view_release.php",{discussion_area_id:cur_area_id,cur_page:curPage},function(json) {
		$.each(json, function(InfoIndex, Info) {
			/**
			 * discussion_release_id  本贴的发帖id
			 * discussion_release_time 本帖发布时间
			 * discussion_area_id 讨论区id
			 * discussion_release_title 本帖标题
			 * discussion_release_content 本帖内容
			 * discussion_response_num  回帖数
			 * discussion_release_user 作者
			 */
			var release_id = Info["discussion_release_id"];
			var release_user_id = Info['discussion_release_user_id'];
			permission_no = 1;
			if(release_user_id == globalUserId){	// 有权限删除帖子
				permission_no = 2;
			}
			release_array[release_id] = Info;
			table_row += '<tr>';
			table_row += '<td  class="textTitle"><a href = "javascript:init_release_content(' + release_id + ','+permission_no+')">'+ Info["discussion_release_title"] + '</a></td>';
			table_row += '<td>'
				+ "<a  name='" + release_user_id + "' class='userName'>" 
				+ Info['discussion_release_user'] + "</a>" + '</td>';
			table_row += '<td>' + Info["discussion_release_time"] + '</td>';
			table_row += '<td>' + Info["discussion_response_num"] + '</td>';	
			table_row += '</tr>';
		});	
		table_header += table_row;
		$("#titleList").html(table_header);
		$(".userName").bind('click', enterFriendHomepage);
	});
}

/*删除帖子*/
function del_release(){
	$.post(Root+"del_discussion_release.php",	
			{
				discussion_release_id:cur_release_id	
			},
			function(reply) {
				if(reply == '1'){
					//alert('删除成功');
					init_discussion_release_list(cur_area_id);
				} else {
					alert(reply);
				}
			}
	);
}
/* 删除回复 */
function del_response(response_id){
	$.post(Root+"del_discussion_response.php",
			{
				discussion_response_id:response_id
			},
			function(reply) {
				if(reply == '1'){
					//alert('删除成功');
					init_release_content(cur_release_id,permission_no);
				}else {
					alert(reply);
				}
			}
	);
}


/* 加载release CKEditor */
function load_release_ckedit() {
	var html1 = '<textarea class = "ckeditor2" name="msgcontent" id="newReleaseContent" ></textarea>';
	if(CKEDITOR.instances['newReleaseContent'] || editor1){ 	//判断 newReleaseContent 是否已经被绑定
		CKEDITOR.remove(CKEDITOR.instances['newReleaseContent']); //如果绑定了就解绑定
		$('#releaseTd').html('');
		//editor1.distroy();
	}
	$('#releaseTd').html(html1);
	// 这里可以定制想要的编辑功能
	editor1 = CKEDITOR.replace('newReleaseContent',configParam);
}
/* 加载reponse CKEditor */
function load_response_ckedit() {	
	var html2 = '<textarea class = "ckeditor1" name="msgcontent" id="newResponseContent"></textarea>';
	if(CKEDITOR.instances['newResponseContent'] || editor2){ 	//判断 newResponseContent 是否已经被绑定
		CKEDITOR.remove(CKEDITOR.instances['newResponseContent']); //如果绑定了就解绑定
		$('#responseTd').html('');
		//editor2.distroy();
	}
	$('#responseTd').html(html2);
	editor2 = CKEDITOR.replace('newResponseContent',configParam);
	//setTimeout('CKEDITOR.replace("newReleaseContent")',300);
}


/* 处理页数图标的显隐和页数显示 */
function processPage() {
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$("#releasePagePrev").css('visibility', "hidden");
	}	else {
		$("#releasePagePrev").css('visibility', "visible");
	}
	if (curPage < sumPage) {
		$("#releasePageNext").css('visibility', "visible");
	}	else {
		$("#releasePageNext").css('visibility', "hidden");
	}
}

/*下一页*/
function releaseGetNext() {
	if(curPage < sumPage) {
		curPage++;
		set_release_list();
	}
}
/*上一页*/
function releaseGetPrev() {
	if(curPage > 1) {
		curPage--;
		set_release_list();
	}
}

// 显示发帖区域
function show_post_area(){
	$("#responseDiv").hide();		//隐藏回复容器
	$("#responseArea").hide();		//隐藏回复输入框
	$("#releaseTitle").hide();		//隐藏贴子标题
	$("#releaseDiv").hide();
	$("#editReleaseArea").hide();		//隐藏编辑帖子容器
	$("#releaseArea").hide();
	$("#releaseArea").show();
	load_release_ckedit();
}
function set_and_show_response_area() {
	cur_response_user_name = $(this).attr('username');
	cur_response_user_id = $(this).attr('userid');
	show_response_area();
}
// 显示回帖区域
function show_response_area(){
	$("#responseDiv").show();			//隐藏回复容器
	$("#releaseTitle").show();			//隐藏贴子标题
	$("#releaseDiv").hide();
	$("#editReleaseArea").hide();		//隐藏编辑帖子容器
	$('#responseArea').hide();
	$("#releaseArea").hide();
	$('#quickReplyContent').focus();
	var tempContent = "回复"+cur_response_user_name+":";
	if(cur_response_user_id != globalUserId) $('#quickReplyContent').val(tempContent);
}
function show_detail_response_area() {
	$("#responseDiv").hide();		//隐藏回复容器
	$("#releaseTitle").hide();		//隐藏贴子标题
	$("#releaseDiv").hide();
	$("#editReleaseArea").hide();		//隐藏编辑帖子容器
	$("#releaseArea").hide();
	$("#responseArea").show();		//显示回复输入框
	load_response_ckedit();
	editor2.setData($('#quickReplyContent').val());
}

/*初始化贴子*/
function init_release_content(release_id,permission_no) {
	if(!release_array[release_id]) {
		//alert('抱歉，该贴已经删除');
		return;
	}
	cur_release_id = release_id;
	$("#releaseArea").hide();					//隐藏发帖输入框
	$("#editReleaseArea").hide();				//隐藏编辑帖子容器
	$("#releaseDiv").hide();					//隐藏贴子列表
	$("#newReleaseButton").hide();				//隐藏发布新帖按钮
	$("#newResponseButton").show();			//显示回帖按钮
	if(permission_no == 2)  {
		$('#editReleaseButton').show();		//有权限修改则显示修改的按钮
		$('#delReleaseButton').show();			//有权限删除则显示删帖的按钮
	}
	$("#responseArea").hide();		//隐藏回复输入框
	$("#newResponseContent").val("");	//清空回帖内容
	$("#releaseTitle").html(release_array[release_id]["discussion_release_title"]);
	$("#responseTitle").html(release_array[release_id]["discussion_release_title"]);
	$("#releaseTitle").show();
	$("#areaName").attr("href","javascript:init_discussion_release_list("+ release_array[release_id]["discussion_area_id"] +");");
	
	var release_title = release_array[release_id]["discussion_release_title"];
	var release_content = release_array[release_id]["discussion_release_content"];
	var release_time = release_array[release_id]["discussion_release_time"];
	var release_user_id = release_array[release_id]["discussion_release_user_id"];
	var release_user_photo = release_array[release_id]["discussion_release_user_photo"];
	var release_user_name = release_array[release_id]["discussion_release_user"];
	
	cur_response_user_id = release_user_id;
	cur_response_user_name = release_user_name;
	$('#quickReplyContent').val('');
	$('#newResponseButton').attr('userid',release_user_id);
	$('#newResponseButton').attr('username',release_user_name);
	$('#newResponseButton').bind('click',set_and_show_response_area);
	
	var msgInfo = 
		'<div class="msgInfo">'+
			'<div class=responseTitle><span class="spanPadding">标题:'+ release_title + '</span></div>'+
			'<div class ="responseDiv">' +
				'<div class="responseUserInfo">'+
					'<div class="responseUserPhoto">'+
						'<a  >'+
							'<img class="userPhoto" name ="'+release_user_id + '" src="' + release_user_photo+ '">' +
						'</img></a>'+
					'</div>'+
					"<a  name='" + release_user_id + "' class='userName responseUserName'>" + release_user_name + '</a>'+
				'</div>'+
				'<div class="responseContent">'+ release_content + 
					'<p><br /><br /></p>'+
					'<p class="responseTimeInfo">发布于  '+release_time+'&nbsp;&nbsp;&nbsp;&nbsp;0 楼</p>'+
				'</div>'+
			'</div>' +
		'</div>';
	var msgPane = msgInfo;
	$("#responseList").html(msgPane);
	
	courseDiscussionGetResponse(false, false);
}
/*发布新帖*/
function submit_new_release() {
	if(!check_title_and_content())
		return;
	var release_area_id = cur_area_id;
	var release_title = $("#newReleaseTitle").val();
	var release_content = editor1.getData();
	$.post(Root+"release_discussion.php",
			{discussion_area_id:release_area_id,
			discussion_release_title:release_title,
			discussion_release_content:release_content},
			function(reply) {
				if(reply == '1') {
					alert('发布成功');
					init_discussion_release_list(release_area_id);
				} else {
					alert(reply);
				}
	});
}
/* 回复帖子 快速回复 */
function submit_new_quick_response(){
	var release_id = cur_release_id;
	var response_content = $("#quickReplyContent").val();
	if(response_content == ''){
		alert('回复内容不能为空');
		return;
	}
	response_content = ReplaceAll(response_content,'\n','<br/>');	//替换回车
	submit_response(release_id,response_content);
}
/*发布新回复 - 使用CKEditor回复*/
function submit_new_response() {
	if(!check_content())	return;
	var release_id = cur_release_id;
	var response_content = editor2.getData();
	submit_response(release_id,response_content);
}
function submit_response(release_id,response_content){
	$.post(Root+"response_discussion.php",
		{
			course_id:courseId,
			discussion_release_id:release_id,
			discussion_response_content:response_content,
			reponse_user_id:cur_response_user_id
		},
		function(data) {
			if(data=='1'){
				responseCurPage = 1;
				init_release_content(release_id,permission_no);
				 $("#quickReplyContent").val('');	//置空快速回复区域
			}else {
				alert(data);
			}
	});
}
/*取消发布新帖*/
function cancel_new_release() {
	init_discussion_release_list(cur_area_id);
}
/*取消发布回复*/
function cancel_new_response() {
	init_release_content(cur_release_id,permission_no);	
}
/*检查标题和内容是否为空 - 完整版回复检测*/
function check_title_and_content() {
	var release_title = $("#newReleaseTitle").val();
	var release_content = editor1.getData();
	if(release_title == '' || release_content == '') {
		alert('标题和内容不能为空！');
		return false;
	}
	return true;
}
/*检查内容是否为空*/
function check_content() {
	var response_content = editor2.getData();
	if(response_content == '') {
		alert('内容不能为空！');
		return false;
	}
	return true;
}
function ReplaceAll(str, sptr, sptr1) {	
	while (str.indexOf(sptr) >= 0) {
	   str = str.replace(sptr, sptr1);
	}
	return str;
}
function load_release_edit_ckedit() {
	var html3 = '<textarea class = "ckeditor2" name="msgcontent" id="editReleaseContent" ></textarea>';
	if(CKEDITOR.instances['editReleaseContent'] || editor3){ 	//判断 newReleaseContent 是否已经被绑定
		CKEDITOR.remove(CKEDITOR.instances['editReleaseContent']); //如果绑定了就解绑定
		$('#editReleaseTd').html('');
		//editor3.distroy();
	}
	$('#editReleaseTd').html(html3);
	editor3 = CKEDITOR.replace('editReleaseContent',configParam);
	//editor3 = CKEDITOR.replace('editReleaseContent');
}
/*编辑帖子*/
function edit_release() {
	$("#responseDiv").hide();		//隐藏回复容器
	$("#responseArea").hide();		//显示回复输入框
	$("#releaseTitle").hide();		//隐藏贴子标题
	$("#releaseDiv").hide();
	$("#releaseArea").hide();
	$("#editReleaseArea").show();		//隐藏编辑帖子容器
	load_release_edit_ckedit();
	$("#editReleaseTitle").val(release_array[cur_release_id]["discussion_release_title"]);	//设置标题
	editor3.setData(release_array[cur_release_id]["discussion_release_content"]);			//设置内容
}
/*提交帖子修改*/
function submit_edit_release() {
	//alert('submit edit release');
	var release_title = $("#editReleaseTitle").val();
	var release_content = editor3.getData();
	$.post(Root+"edit_release.php",
			{
				discussion_release_id:cur_release_id,
				discussion_release_title:release_title,
				discussion_release_content:release_content},
			function(reply) {
				if(reply == '1')	init_discussion_release_list(cur_area_id);
				else alert(reply);
	});
}
/*修改帖子内容清空*/
function clear_edit_release(){
	$("#editReleaseTitle").attr("value","");
	editor3.setData("");	
}

/** edit by Xiaofan */
function courseDiscussionGetResponse(prevClick, nextClick) {
	$.get(Root + "get_page_nums.php", {//获取页数的类型
			type: "discussion_response",
			discussion_release_id: cur_release_id
		},
		function(data) {
			responseSumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + "view_response.php", {
						discussion_release_id: cur_release_id,
						cur_page: responseCurPage
					},
					responseAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root + "view_response.php", {
						discussion_release_id: cur_release_id,
						cur_page: responseCurPage
					},
					responseAddNextClick
				);
			}
			else {
				$.getJSON(
					Root + "view_response.php", {
						discussion_release_id: cur_release_id, 
						cur_page: responseCurPage
					},
					responseAdd
				);
			}
		}
	); 	
}
/* 处理回复返回 */
function responseAdd(data) {
	addResponse(data, false, false);
}
function responseAddPrevClick(data) {
	addResponse(data, true, false);
}
function responseAddNextClick(data) {
	addResponse(data, false, true);
}
	
function addResponse(data, prev, next) {
	var msgPane = "";
	if (data == "0") {		//无权查看该页面或者尚未登录
		alert("尚未登录");
		generalGotoLoginPage();
	} else if (data == null) {
		if (prev) {
			responseGetPrevCallback(false);
		} else if (next) {
			responseGetNextCallback(false);
		}
	} else {
		$("#responseList .msgPane").remove();			// 清空回复内容
		$.each(data, function(InfoIndex, Info) {
			/*	discussion_release_id  回帖对应的发帖id
				discussion_response_id  回帖id
				discussion_response_content 回帖内容
				discussion_response_time   回帖时间
				discussion_response_user 回帖人
			*/
			var response_content = Info["discussion_response_content"];
			var response_id = Info['discussion_response_id'];
			var response_time = Info["discussion_response_time"];
			var response_user_id = Info["discussion_response_user_id"];
			var response_user_photo = Info["discussion_response_user_photo"];
			var response_user_name = Info["discussion_response_user"];
			var response_floor = Info["discussion_response_floor"];
			
			/* Edit by qiangrw*/
			var msgHeader = '<div class = "msgPane">';
			var msgInfo = 
				'<div class = "msgInfo">'+
					'<div class=responseTitle>';
			if(response_user_id == globalUserId) {
					msgInfo += 
						'<span style="float:right;"><a id="delResponse" title="删除回复" onclick="javascript:del_response('
							+ response_id + ');"></a></span>';
			} else {	// 回复用户
					msgInfo += 
						'<span style="float:right;"><a class="responseButtonClass" username="'+response_user_name+
							'" userid="'+response_user_id+'" title="回复"></a></span>';
			}
			msgInfo +=
					'</div>'+
					'<div class ="responseDiv">' +
						'<div class="responseUserInfo">'+
							'<div class="responseUserPhoto">'+
								'<a  >'+
									'<img class="userPhoto" name ="'+response_user_id + '" src="' + response_user_photo+ '">' +
								'</img></a>'+
							'</div>'+
							"<a  name='" + response_user_id + "' class='userName responseUserName'>"+
								response_user_name + '</a>'+
						'</div>'+
						'<div class="responseContent">'+ response_content + 
							'<p><br /><br /></p>'+
							'<p class="responseTimeInfo">发布于  '+response_time+ '&nbsp;&nbsp;&nbsp;&nbsp;'+response_floor+' 楼</p>'+
						'</div>'+
					'</div>' +
				'</div>';
			var curMsg = msgHeader + msgInfo  + '</div>';
			msgPane += curMsg;		// 添加一条回复
		});	
		$("#responseList").append(msgPane);
		$("#responseDiv").show();
		$(".userPhoto[name!="+globalUserId+"]").bind('click', enterFriendHomepage);
		$(".userName[name!="+globalUserId+"]").bind('click', enterFriendHomepage);
		$('.responseButtonClass').bind('click',set_and_show_response_area);
		
		if (prev) {
			responseGetPrevCallback(true);
		} else if (next) {
			responseGetNextCallback(true);
		}
	}
	responseProcessPage();
}
/* 处理页数图标的显隐和页数显示 */
function responseProcessPage() {
	$('#responsePage').text(responseCurPage + '/' + responseSumPage);
	if (responseCurPage == "1") {
		$("#responsePagePrev").css('visibility', "hidden");
	}
	else if (responseCurPage == "2") {
		$("#responsePagePrev").css('visibility', "visible");
	}
	if (responseCurPage < responseSumPage) {
		$("#responsePageNext").css('visibility', "visible");
	}
	else {
		$("#responsePageNext").css('visibility', "hidden");
	}
}

/* 上一页 */
function responseGetPrev() {
	responseCurPage--;
	courseDiscussionGetResponse(true, false);
}
function responseGetPrevCallback(suc) {
	if (! suc) {
		alert("no more response/off log(I don't want this to show), responseGetPrevCallback, navCourseDiscussion.js");
		responseCurPage++;
	}
}

/* 下一页 */
function responseGetNext() {
	responseCurPage++;
	courseDiscussionGetResponse(false, true);
}
function responseGetNextCallback(suc) {
	if (! suc) {	// 没有动态，或未登录
		alert("no more response/off log(I don't want this to show), responseGetPrevCallback, navCourseDiscussion.js");
		responseCurPage--;
	}
}
/** end edit by Xiaofan */
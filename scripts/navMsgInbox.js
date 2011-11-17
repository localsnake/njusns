var curPage = 1;
var sumPage = 0;
var curGroupId = -1;
var curGroupTitle='TITLE';
var curGroupReceiverId = 0;
var msgReplyEditor;
var myUserPhoto = '';
var myUserName = '';
var curReplyContent = '';

/* 加载release CKEditor */
function load_release_ckedit() {
	var html1 = '<textarea class = "ckeditor2" name="msgcontent" id="mailReplyContent" ></textarea>';
	if(CKEDITOR.instances['mailReplyContent'] || msgReplyEditor){ 	//判断 mailReplyContent 是否已经被绑定
		CKEDITOR.remove(CKEDITOR.instances['mailReplyContent']); //如果绑定了就解绑定
		$('#releaseId').html('');
		//msgReplyEditor.distroy();
	}
	$('#releaseId').html(html1);
	// 这里可以定制想要的编辑功能
	msgReplyEditor = CKEDITOR.replace('mailReplyContent',configParam);
}

function initMsgInboxPage() {
	$('#inboxWrapper').show();
	$('#groupWrapper').hide();
	$('#msgPagePrev').css('visibility', 'hidden');
	$('#msgPageNext').css('visibility', 'hidden');
	$('#msgPagePrev').bind('click', MsgGetPrev);
	$('#msgPageNext').bind('click', MsgGetNext);
	inboxGetMsg(false,false);		
}

function inboxGetMsg(prevClick,nextClick) {
	$.get(Root + 'get_msg_count.php', {//获取页数的类型
			type: 'inbox'
		},
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'view_msg_inbox.php', {
						cur_page: curPage
					},
					MsgAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root + 'view_msg_inbox.php', {
						cur_page: curPage
					},
					MsgAddNextClick
				);
			}
			else {
				$.getJSON(
					Root + 'view_msg_inbox.php', {
						cur_page: curPage
					},
					MsgAdd
				);
			}
		}
	); 
}

function MsgAdd(data) {
	msgAppend(data, false, false);
}
function MsgAddPrevClick(data) {
	msgAppend(data, true, false);
}
function MsgAddNextClick(data){
	msgAppend(data, false, true);
}
function processPage(){
	if(sumPage == 0) {
		$('#page').css('visibility', 'hidden');
	} else {
		$('#page').css('visibility', 'visible');
	}
	$('#page').text(curPage + '/' + sumPage);
	if (curPage == '1') {
		$('#msgPagePrev').css('visibility', 'hidden');
	}
	else if (curPage == '2') {
		$('#msgPagePrev').css('visibility', 'visible');;
	}
	if (curPage < sumPage) {
		$('#msgPageNext').css('visibility', 'visible');
	}
	else {
		$('#msgPageNext').css('visibility', 'hidden');
	}
}
function MsgGetPrev(){
	curPage--;
	inboxGetMsg(true, false);
}
function MsgGetNext(){
	curPage++;
	inboxGetMsg(true, false);
}

function msgAppend(data,prev,next) {
	$('#inboxTable').html('<tr><th class="msgUserInfo">对话人</th>'+
							'<th class="msgTitleContent">标题</th>'+
							'<th class="msgTimeInfo">收信时间</th></tr>');
	$.each(data, function(index, dataSingle) {
		groupId = dataSingle['group_id'];
		receiverId = dataSingle['receiver_id'];
		receiverName = dataSingle['receiver_name'];
		senderId = dataSingle['sender_id'];
		senderName = dataSingle['sender_name'];
		title = dataSingle['title'];
		contentPreview = dataSingle['content_preview'];
		readStatus = dataSingle['read_status'];
		createTime = dataSingle['create_time'];
		groupCount = dataSingle['group_count'];
		// @todo 判断senderId 和 receiverId 哪个是用户自己，替换为 ME
		if(receiverId == globalUserId) {
			receiverName = 'me';
		} 
		if(senderId == globalUserId) {
			senderName = 'me';	
		}
		trString = 
			'<tr class="msgItem'+readStatus+'" id="'+groupId+'">' + 
				'<td class="msgUserInfo" name="'+groupId+'">'+
				'<input class="checkbox" type="checkbox" name='+ groupId +'></input><span class="msgUser" name="'+groupId + '">'+
				senderName+","+receiverName+'('+groupCount+')</span></td>' +
				'<td class="msgTitleContent" name="'+groupId+'">'+
					'<span class="msgTitle"> ' +title + '</span> - <span class="msgPreview">' + contentPreview + '</span></td>' +
				'<td class="msgTimeInfo" name="'+groupId+'">'+createTime+'</td>'+
				//'<td class="msgDelete"><a class="courseDelete courseTableIcon" name="'+groupId+'" title="删除"  ></a></td>' +
			'</tr>';
		$('#inboxTable').append(trString);
	});
	$('.msgUser,td[class=msgTitleContent],td[class=msgTimeInfo]').bind('click',function(){
			$('#inboxWrapper').hide();
			$('#groupWrapper').show();
			$('.mailGroupInfo').html('');
			id = $(this).attr('name');
			initGroupMsg(id);
	});
	$('.msgDelete a').bind('click',function() {
		var id = $(this).attr('name');
		$.post(
			Root+'del_msg_group.php', 
			{	type: 'inbox',
				group_id: id
			},
			afterDelGroupMail
		);
	});
	$('#deleteMsg').bind('click',msgInboxDeleteSeleted);
	$('#newMsg').bind('click',function() {
		changeNav(true, true, '', 'navMsg', 'navMsgSend');
		changeContentPane('navMsgSend.html');
	});
	processPage();
}

function msgInboxDeleteSeleted() {
	var list = $('#inboxTable tr');
	var flag = 0;
	$.each(list, function(i, obj){
		if ($('#' + $(obj).attr('id') + ' input').attr('checked') == 'checked') {
			msgInboxDeleteOne($(obj).attr('id'));
			flag = 1;
		}
	});
	if(flag==0){
		alert("要想执行这项操作，您须选取至少一封邮件。");
	}else {
		initMsgInboxPage();
	}
}
function msgInboxDeleteOne(id) {
	$.post(
		Root+'del_msg_group.php', 
		{	type: 'inbox',
			group_id: id
		}
	);
}


function initGroupMsg(id){	
	$('#quickReply').show();
	$('#mailGroupInfo').show();
	$('#fullReply').hide();
	$('#mailGroupInfo').html('');
	$.getJSON(
		Root + 'view_msg_group.php?type=inbox&group_id='+id, 
		function(msgGroupData){
			/*获取组邮件信息*/
			$.each(msgGroupData, function(index, msgGroupSingle) {
				groupMsgId = msgGroupSingle['msg_id'];
				groupTitle = msgGroupSingle['title'];
				groupContent = msgGroupSingle['content'];
				groupCreateTime = msgGroupSingle['create_time'];
				groupSenderId = msgGroupSingle['sender_id'];
				groupReceiverId = msgGroupSingle['receiver_id'];
				groupSenderName = msgGroupSingle['sender_name'];
				groupReceiverName = msgGroupSingle['receiver_name'];
				groupSenderPhoto = msgGroupSingle['sender_photo'];
				if(groupReceiverId == globalUserId) {
					groupReceiverName = 'me';
					curGroupReceiverName = groupSenderName;
					curGroupReceiverId = groupSenderId;
					myUserPhoto = msgGroupSingle['receiver_photo'];
				} 
				if(groupSenderId == globalUserId) {
					groupSenderName = 'me';
					curGroupReceiverName = groupReceiverName;
					curGroupReceiverId = groupReceiverId;
					myUserPhoto = msgGroupSingle['sender_photo'];
				}
				
				divGroupString =
					'<div class ="responseDiv">' +
						'<div class="responseUserInfo">'+
							'<div class="responseUserPhoto">'+
								'<a  >'+
									'<img class="userPhoto" name ="'+groupSenderId + '" src="' + groupSenderPhoto+ '">' +
								'</img></a>'+
							'</div>'+
							"<a  name='" + groupSenderId + "' class='userName responseUserName'>"+
								groupSenderName + '</a>'+
						'</div>'+
						'<div class="responseContent">'+ groupContent + 
							'<p class="responseTimeInfo time">('+groupCreateTime+')</p>'+
						'</div>'+
					'</div>';
				
				$('#mailGroupInfo').append(divGroupString);
			});
			setUnreadMsgCount();	//重新设置未读站内信多少
			$('#mailGroupTitle').html(groupTitle);
			$(".userPhoto[name!="+globalUserId+"]").bind('click', enterFriendHomepage);
			$(".userName[name!="+globalUserId+"]").bind('click', enterFriendHomepage);
		}
	);
	load_release_ckedit();
	
	$('#replyMailBtn').bind('click',function() {
		curReplyContent = msgReplyEditor.getData();
		replyMsg(curReplyContent,id,curGroupReceiverId,groupTitle);
		$('#quickReply').show();		//显示快捷回复框
		$('#mailGroupInfo').show();	//显示组邮件信息
		$('#fullReply').hide();		//隐藏完整回复
	});
	$('#replyQuickMailBtn').bind('click',function() {
		curReplyContent = $('#msgQuickReplyContent').val();
		replyMsg(curReplyContent,id,curGroupReceiverId,groupTitle);
	});
	$('#replyBckBtn,#replyQuickBckBtn').bind('click',function() {
		initMsgInboxPage();
	});
	$('#mailDelete').bind('click',function() {
		$.post(
			Root+'del_msg_group.php', 
			{	type: 'inbox',
				group_id: id
			},
			afterDelGroupMail
		);
	});
	$('#fullEdition').bind('click',function() {
		$('#quickReply').hide();
		$('#mailGroupInfo').hide();
		$('#fullReply').show();
	});
	$('#quickEdition').bind('click',function() {
		$('#quickReply').show();
		$('#mailGroupInfo').show();
		$('#fullReply').hide();
	});
}

function replyMsg(curReplyContent,id,curGroupReceiverId,groupTitle) {
	if(curReplyContent == "") {
		alert('回复内容不能为空');
		return;
	}
	$.post(
		Root+'send_msg.php', 
		{	group_id: id,
			receiver_id: curGroupReceiverId,
			title: groupTitle,
			content: curReplyContent
		},
		afterReplyMail
	);
}
function afterReplyMail(data) {
	if(!isNaN(data) && data>0) {
		/*divGroupString = 
		'<div class="groupMsgDiv">'+
					'<div><span class="msgGroupUserInfo"> me to '+curGroupReceiverName + '</span>' +
						'<span class="time msgGroupTime">' + '0 min' +
					'<span></div>' +
					'<p id="msgGroupContent">'+msgReplyEditor.getData()+'</p>'
				'</div>';*/
		divGroupString = 
		'<div class ="responseDiv">' +
			'<div class="responseUserInfo">'+
				'<div class="responseUserPhoto">'+
					'<a  >'+
						'<img class="userPhoto" name ="'+globalUserId + '" src="' + myUserPhoto+ '">' +
					'</img></a>'+
				'</div>'+
				"<a  name='" + globalUserId + "' class='userName responseUserName'>"+ 'me' +'</a>'+
			'</div>'+
			'<div class="responseContent">'+ curReplyContent + 
				'<p class="responseTimeInfo time">(0 min)</p>'+
			'</div>'+
		'</div>';
		$('#mailGroupInfo').append(divGroupString);
		$('#msgQuickReplyContent').val('');
		msgReplyEditor.setData('');
	} else {
		alert(data);
	}
}

function afterDelGroupMail(data){
	if(data == '1') {
		alert('删除成功');
		initMsgInboxPage();
	} else {
		alert(data);
	}
}
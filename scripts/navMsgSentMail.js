var curPage = 1;
var sumPage = 0;
curGroupId = -1;
curGroupTitle='TITLE';
curGroupReceiverId = 0;
function initMsgSentMailPage() {
	$('#sentMailWrapper').show();
	$('#groupWrapper').hide();
	$('#msgPagePrev').css('visibility', 'hidden');
	$('#msgPageNext').css('visibility', 'hidden');
	$('#msgPagePrev').bind('click', MsgGetPrev);
	$('#msgPageNext').bind('click', MsgGetNext);
	sentmailGetMsg(false,false);	
}
function sentmailGetMsg(prevClick,nextClick) {
	$.get(Root + 'get_msg_count.php', {//获取页数的类型
			type: 'sentmail'
		},
		function(data) {
			sumPage = data;
			if (prevClick) {
				$.getJSON(
					Root + 'view_msg_sentmail.php', {
						cur_page: curPage
					},
					MsgAddPrevClick
				);
			}
			else if (nextClick) {
				$.getJSON(
					Root + 'view_msg_sentmail.php', {
						cur_page: curPage
					},
					MsgAddNextClick
				);
			}
			else {
				$.getJSON(
					Root + 'view_msg_sentmail.php', {
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
	sentmailGetMsg(true, false);
}
function MsgGetNext(){
	curPage++;
	sentmailGetMsg(true, false);
}
function msgAppend(data,prev,next) {
	$('#sentMailTable').html('<tr><th class="msgUserInfo">对话人</th>'+
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
		createTime = dataSingle['create_time'];
		groupCount = dataSingle['group_count'];
		// @todo 判断senderId 和 receiverId 哪个是用户自己，替换为 ME
		if(receiverId == globalUserId) {
			receiverName = '我';
		} 
		if(senderId == globalUserId) {
			senderName = '我';
		}
		trString = 
			'<tr class="msgItem0" id="'+groupId+'">' + 
				'<td class="msgUserInfo" name="'+groupId+'">'+
				'<input class="checkbox" type="checkbox" name='+ groupId +'></input><span class="msgUser" name="'+groupId + '">'+
				senderName+","+receiverName+'('+groupCount+')</span></td>' +
				'<td class="msgTitleContent" name="'+groupId+'">'+
				 '<span class="msgTitle"> '+title + '</span> - <span>' + contentPreview + '</span></td>' +
				'<td class="msgTimeInfo" name="'+groupId+'">'+createTime+'</td>'+
				//'<td class="msgDelete"><a class="courseDelete courseTableIcon" name="'+groupId+'" title="删除"  ></a></td>' +
			'</tr>';
		$('#sentMailTable').append(trString);
	});
	$('.msgUser,td[class=msgTitleContent],td[class=msgTimeInfo]').bind('click',function(){
			$('#sentMailWrapper').hide();
			$('#groupWrapper').show();
			$('.mailGroupInfo').html('');
			var id = $(this).attr('name');
			initGroupMsg(id);
	});
	$('#deleteMsg').bind('click',msgSentMailDeleteSeleted);
	$('#newMsg').bind('click',function() {
		changeNav(true, true, '', 'navMsg', 'navMsgSend');
		changeContentPane('navMsgSend.html');
	});
	/*$('.msgDelete a').bind('click',function() {
		var id = $(this).attr('name');
		$.post(
			Root+'del_msg_group.php', 
			{	type: 'sentmail',
				group_id: id
			},
			afterDelGroupMail
		);
	});*/
	processPage();
}

function initGroupMsg(id){	
	$.getJSON(
		Root + 'view_msg_group.php?type=sentmail&group_id='+id, 
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
					groupReceiverName = '我';
				} 
				if(groupSenderId == globalUserId) {
					groupSenderName = '我';
				}
				/*divGroupString = 
				'<div class="groupMsgDiv">'+
					'<div><span class="msgGroupUserInfo">' + 
						groupSenderName+' to '+groupReceiverName + '</span>' +
						'<span class="time msgGroupTime">' + groupCreateTime +
					'<span></div>' +
					'<p id="msgGroupContent">'+groupContent+'</p>'
				'</div>';*/
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
			$('#mailGroupTitle').html(groupTitle);
			$(".userPhoto[name!="+globalUserId+"]").bind('click', enterFriendHomepage);
			$(".userName[name!="+globalUserId+"]").bind('click', enterFriendHomepage);
		}
	);
	$('#mailDelete').bind('click',function() {
		$.post(
			Root+'del_msg_group.php', 
			{	type: 'sentmail',
				group_id: id
			},
			afterDelGroupMail
		);
	});
}

function msgSentMailDeleteSeleted() {
	var list = $('#sentMailTable tr');
	var flag = 0;
	$.each(list, function(i, obj){
		if ($('#' + $(obj).attr('id') + ' input').attr('checked') == 'checked') {
			msgSendMailDeleteOne($(obj).attr('id'));
			flag = 1;
		}
	});
	if(flag==0){
		alert("要想执行这项操作，您须选取至少一封邮件。");
	}else {
		initMsgSentMailPage();
	}
}

function msgSendMailDeleteOne(id) {
	$.get(
		Root+'del_msg_group.php', 
		{	type: 'sentmail',
			group_id: id
		}
	);
}

function afterDelGroupMail(data){
	if(data == '1') {
		alert('删除成功');
		initMsgSentMailPage();
	} else {
		alert(data);
	}
}
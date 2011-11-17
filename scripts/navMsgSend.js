var msgPostEditor;

/* 加载release CKEditor */
function load_release_ckedit() {
	var html1 = '<textarea class = "ckeditor2" name="msgcontent" id="msgContent" ></textarea>';
	if(CKEDITOR.instances['msgContent'] || msgPostEditor){ 	//判断 msgContent 是否已经被绑定
		CKEDITOR.remove(CKEDITOR.instances['msgContent']); //如果绑定了就解绑定
		$('#releaseTd').html('');
	}
	$('#releaseTd').html(html1);
	// 这里可以定制想要的编辑功能
	msgPostEditor = CKEDITOR.replace('msgContent',configParam);
}

function initMsgSendPage(){
	$(function(){
		/* 设置查找好友的AutoComplete */
		$('#userName').autocomplete(Root+'search_auto_complete_user.php', {
			multiple: true,
			scroll: false,
			dataType: 'json',
			parse: function(data) {
				return $.map(data, function(row) {
					return {
						data: row,
						value: row.name,
						result: row.id
					}
				});
			},
			formatItem: function(item) {
				return format(item);
			}
			}).result(function(e, item) {
				//$('#userName').val(item.name);		//item.name
				//$('#receiverId').val(item.id);
				setMsgSendReceiver(item.id,item.name);
			});
			
		load_release_ckedit();
		/*设置发送站内信按钮*/
		$('#msgSendBtn').bind('click',msgSendClick);
		$('#msgRstBtn').bind('click',function(){
			msgPostEditor.setData('');
			$('#notes').fadeOut();
		});
		
	});
}

function msgSendClick() {
	var receiverId = $('#receiverId').val();
	var msgTitle = $('#msgTitle').val();
	//var msgContent = $('#msgContent').val();
	var msgContent = msgPostEditor.getData();
	if(!receiverId || !msgTitle || !msgContent){
		$('#notes').text("所有区域不能为空");
		$('#notes').fadeIn();
		return;
	}
	if(!receiverId){
		$('#notes').text("用户未查找到");
		$('#notes').fadeIn();
		return;
	}
	$.post(
		Root+'send_msg.php',
		{
			receiver_id: receiverId,
			title: msgTitle,
			content: msgContent
		},
		function(data){
			if(data > 0){
				$('#userName').val('');
				$('#receiverId').val('');
				$('#msgTitle').val('');
				//$('#msgContent').val('');
				msgPostEditor.setData();
				alert('站内信发送成功');
				//切换到收件箱
				msgInfoClick();	 //该函数在index.js中
			} else {
				alert(data);
			}
		}
	);
}
function setMsgSendReceiver(id,name) {
	$('#userName').val(name);
	$('#receiverId').val(id);
}
function format(row) {	
	if(row.name.match('[a-zA-Z]+') && row.name.length > 6)
		row.name = row.name.substring(0,6) + '...';
	if(!row.name.match('[a-zA-Z]+') && row.name.length > 4)
		row.name = row.name.substring(0,4) + '...';
	return '<img style = "float:left" class="userPhoto" name="' + row.id + '" src= "' + row.photo + '"/><a style = "float:left;position:relative;top:17px;cursor:pointer;color:#F38630;">' + row.name + '</a>';
}
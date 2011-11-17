$(document).ready(function() {
	$('#createBtn').bind('click', createBtnClick);
	$('#courseDiscussionName').bind('keydown', discussionNameHandler);
	$('.cancelBtn').bind('click',function(){
		parent.$.fancybox.close();
	})
});

function createBtnClick() {
	$.post(
		Root+'create_course_discussionarea.php', {
			course_id:$('#courseDiscussionCourseId').val(),
			course_discussion_name: $('#courseDiscussionName').val(),
		}, 
		createBtnClickCallback
	);
}
function createBtnClickCallback(data) {
	if (isNaN(data)) {
		$('#errorEcho label').text(data);
		//$('#errorEcho').css('visibility', 'visible');
		$('.errorWrapper').css('display','inline');
	}
	else {
		//alert('添加成功，要关闭fancybox啦！');
		//$('#errorEcho').css('visibility', 'hidden');
		$('#errorEcho').css('display','none');
		parent.init_discussion_area_list();
		parent.$.fancybox.close(); 
	}
}
function discussionNameHandler(){
	var discussionName=$('#courseDiscussionName').val();
	if(discussionName.trim()!=''){
		$('.errorWrapper').hide();
	}
}

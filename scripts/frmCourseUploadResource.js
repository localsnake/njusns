$(document).ready(function() {
	//$('.acceptBtn').bind('click', acceptBtnClick);
	$('.cancelBtn').bind('click',function(){
		parent.$.fancybox.close();
	});
	$('input[name=resource_type]').click(function(){
		var type = $(this).val();
		if(type == "I"){
			$('#innerTr').show();
			$('#outerTr').hide();
		}else {
			$('#outerTr').show();
			$('#innerTr').hide();
		}
	});
});


flagTitle = false;
flagPath = false;
function checkTitle(){
	if($('#inputTitle').val() == ''){
		$('#errorEcho label').text('请填写资源名！');
		$('.errorWrapper').show();
		flagTitle = false;
	}
	else{
		flagTitle = true;
	}
}

function checkPath(){
	if($('#file').val() == ''){
		$('#errorEcho label').text('请选择附件！');
		$('.errorWrapper').show();
		flagPath = false;
	}
	else{
		flagPath = true;
	}
}

function acceptBtnClick() {
	//checkPath();
	checkTitle();

	if(flagTitle && flagPath){
		$('.errorWrapper').hide();
		//alert('before post');
		$.post(
			Root+'upload_resource.php', {
				course_resource_title:$('#inputTitle').val(),
				course_id: $('#courseID').val()
			}, 
				acceptBtnClickCallback
		);
		//alert('after call back');
	}
}
function acceptBtnClickCallback(data) {
	alert('in call back!');
	if (isNaN(data)) {
		$('#errorEcho label').text(data);
		//$('#errorEcho').css('visibility', 'visible');
		$('.errorWrapper').show();
	}
	else {
		//alert('添加成功，要关闭fancybox啦！');
		//$('#errorEcho').css('visibility', 'hidden');
		$('.errorWrapper').hide();
		parent.courseResourceInitPage();
		parent.$.fancybox.close(); 
	}
}

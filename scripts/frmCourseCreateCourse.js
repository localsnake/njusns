$(document).ready(function() {
	$('#errorEcho').css('visibility', 'hidden');

	$('#timeBegin1').bind('change',timeEndSet1);
	$('#timeBegin2').bind('change',timeEndSet2);
	$('#timeBegin3').bind('change',timeEndSet3);
	
	$('#createBtn').bind('click', createBtnClick);
	$('#courseVerify').bind('click change',function(){
		var verifyType = $('#courseVerify').val();
		if(verifyType != 'C') {
			$('#codeTr').hide();
		} else {
			$('#codeTr').show();
		}
	});
});

function timeEndSet1() {
	timeEndSet(1);
}
function timeEndSet2() {
	timeEndSet(2);
}
function timeEndSet3() {
	timeEndSet(3);
}
function timeEndSet(index) {
	
	var timeBegin = $('#timeBegin' + index).val();
	$('#timeEnd' + index + ' option').remove('option[value!="--"]');
	for (var i=timeBegin; i<=10; i++) {
		$('#timeEnd' + index).append($('<option value="' + i + '">' + i + '</option>'));
	}
}

function createBtnClick() {

	if (isNaN($('#courseNumber').val())) {
		$('#errorEcho label').text('上课人数必须是数字或者留空');
		$('#errorEcho').css('visibility', 'visible');
		return ;
	}
	var courseTime = '';
	for (var i=1; i<=3; i++) {
		var timeWeek = $('#timeWeek'+i).val();
		var timeBegin = $('#timeBegin'+i).val();
		var timeEnd = $('#timeEnd'+i).val();
		if(timeWeek!='--' && timeBegin!='--'){
			if(i>1) courseTime += ':';
			courseTime += (timeWeek + '-' + timeBegin + '-' + timeEnd);
		}
	}

	$.post(
		Root+'create_course.php', {
			course_name: $('#courseName').val(),
			course_time: courseTime,
			course_place: $('#coursePlace').val(),
			course_stu_num: $('#courseNumber').val(),
			course_book: $('#courseBook').val(),
			course_type: $('#courseType').val(),
			verify:$('#courseVerify').val(),
			password:$('#coursePassword').val(),
			course_introduction: $('#courseIntro').val()
		}, 
		createBtnClickCallback
	);
}
function createBtnClickCallback(data) {
	if (isNaN(data)) {
		$('#errorEcho label').text(data);
		$('#errorEcho').css('visibility', 'visible');
	}	else {
		alert('恭喜您,添加课程成功了。');
		parent.doHash();	//刷新页面
		$('#errorEcho').css('visibility', 'hidden');
		parent.$.fancybox.close(); 
	}
}

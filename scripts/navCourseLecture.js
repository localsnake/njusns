function courseLectureInitPage() {
	$('#courseLectureSpecificShow table tr[class="tbody"]').remove();
	if (courseRelation == 'M' || courseRelation == 'T') {
		setLectureUploadFancyBox();	
	}
	courseLectureGetLecture(courseId);
}

function courseLectureGetLecture(courseId) {
	// 得到ppt
	$.getJSON(
		Root + 'get_course_lectureinfo.php', {
			course_id: courseId
		}, 
		courseLectureGetLectureCallback
	);
}
function courseLectureGetLectureCallback(data) {
	if (courseRelation == 'M' || courseRelation == 'T') {					// 显示“新增”“编辑”和“删除”
		$('#courseCreateLecture').css('display', 'block');
		$('th.manager').show();
	}
	$.each(data, function(i, dataSingle) {		
		$('#courseLectureSpecificShow table').append(createLecture(dataSingle, i));
	});
	$('#courseLectureSpecificShowAll').show();
	generalSetIFrame('a[title=编辑]',430,430);
	$('a[title=删除]').unbind();
	$('a[title=删除]').bind('click', courseLectureDeleteOne);
}

/* 生成一条记录 */
function createLecture(data, i) {
	if (courseRelation == 'M' || courseRelation == 'T') {					// 管理者拥有“编辑”“删除”权限
		var modifyLink = 'frmCourseModifyLecture.php?course_id='+courseId+'&id=' + data['course_lecture_id'];
		var $newTr = $(
			'<tr class="tbody" id=lecture' + data['course_lecture_id'] + '>'
			+ '<td><label id="lectureTitle' + data['course_lecture_id'] + '">' + data['course_lecture_title'] + '</label></td>'
			+ '<td><label id="createTime' + data['course_lecture_id'] + '">' + data['create_time'] + '</label></td>'
			+ '<td><label id="updateTime' + data['course_lecture_id'] + '">' + data['update_time'] + '</label></td>'
			+ '<td><label id="visits' + data['visits'] + '">' + data['visits'] + '</label></td>'
			+ '<td><a class="courseDownload courseTableIcon" name="lectureDir' + data['course_lecture_id'] + '" href="' + data['course_lecture_dir'] + '" target="_blank" title="下载"></a></td>'
			+ '<td><a class="courseEdit courseTableIcon" id=lectureEdit' + data['course_lecture_id'] + ' name="' + data['course_lecture_id'] + '" class="edit" href="'+modifyLink + '"   title="编辑"></a></td>'
			+ '<td><a class="courseDelete courseTableIcon" title="删除" name="' + data['course_lecture_id'] + '"></a></td>'
			+ '</tr>'
		);
	}
	else {
		var $newTr = $(
			'<tr class="tbody" id=lecture' + data['course_lecture_id'] + '>'
			+ '<td><label id="lectureTitle' + data['course_lecture_id'] + '">' + data['course_lecture_title'] + '</label></td>'
			+ '<td><label id="createTime' + data['course_lecture_id'] + '">' + data['create_time'] + '</label></td>'
			+ '<td><label id="updateTime' + data['course_lecture_id'] + '">' + data['update_time'] + '</label></td>'
			+ '<td><label id="visits' + data['visits'] + '">' + data['visits'] + '</label></td>'
			+ '<td><a class="courseDownload courseTableIcon" name="lectureDir' + data['course_lecture_id'] + '" href="' + data['course_lecture_dir'] + '" target="_blank" title="下载"></a></td>'
			+ '</tr>'
		);
	}
	return $newTr;
}
// 创建课件的FancyBox
function setLectureUploadFancyBox() {
	generalSetIFrame('#courseCreateLecture',430,300);
	$('#courseCreateLecture').attr('href', 'frmCourseUploadLecture.php?course_id=' + courseId);
}
//删除一个课件
function courseLectureDeleteOne(){
	var lectureId = $(this).attr('name');
	var path = Root+'del_lecture.php?course_id=' + courseId + '&course_lecture_id='+lectureId;
	$.get(
		Root+'del_lecture.php?course_id=' + courseId + '&course_lecture_id='+lectureId, 
		function(data) {
			if (data == '1') {			//课件删除成功了
				$('#lecture' + lectureId).remove();
			}
			else {
				alert(data);
			}
		}
	);
}

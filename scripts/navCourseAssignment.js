function courseAssignmentInitPage() {
	/* 这边设置初始界面的内容 */
	$('#courseAssignmentSpecificShow table tr[class="tbody"]').remove();
	if (courseRelation == 'M' || courseRelation == 'T') {
		$('th.manager').show();
		setAssignmentCreateFancybox();
		//setAssignmentFTPSettingFancybox();	// 设置作业FTP 暂时去除该功能
	} else {	// 上传作业 暂时去除该功能
		//setAssignmentFTPFancybox();
	}
	courseAssignmentGetAssignment(courseId);
}
function courseAssignmentGetAssignment(courseId) {
	// 得到作业
	$.getJSON(
		Root + 'get_course_assignmentinfo.php', {
			course_id: courseId
		}, 
		courseAssignmentGetAssignmentCallback
	);
}
function courseAssignmentGetAssignmentCallback(data) {
	$.each(data, function(i, dataSingle) {
		$('#courseAssignmentSpecificShow table').append(createAssignment(dataSingle));
	});
	$('#courseAssignmentSpecificShowAll').show();
	// Add Fancy Box to show edit form 
	generalSetIFrame('a[title=编辑]',430,430);
	$('a[title=删除]').unbind();
	$('a[title=删除]').bind('click', courseAssignmentDeleteOne);
}
/* 生成一条记录 */
function createAssignment(data) {
	if (courseRelation == 'M' || courseRelation == 'T') {					// 管理者拥有“编辑”“删除”权限
		var modifyLink = 'frmCourseModifyAssignment.php?course_id='+courseId+'&id=' + data['course_assignment_id'];
		var $newTr = $(
			'<tr class="tbody" id="assignment' + data['course_assignment_id'] + '">'
			+ '<td><label>' + data['course_assignment_title'] + '</label></td>'
			+ '<td><label id="assignmentDeadline' + data['course_assignment_id'] + '">' + data['course_assignment_deadline'] + '</label></td>'
			+ '<td><label id="createTime' + data['course_assignment_id'] + '">' + data['create_time'] + '</label></td>'
			+ '<td><label id="updateTime' + data['course_assignment_id'] + '">' + data['update_time'] + '</label></td>'
			+ '<td><a class="courseDownload courseTableIcon" name="assignmentDir'+ data['course_assignment_id'] + '" href="' + data['course_assignment_dir'] + '" target="_blank" title="下载"></a></td>'
			+ '<td><a class="courseEdit courseTableIcon" name="' + data['course_assignment_id'] + '" class="edit"   title="编辑" href="'+modifyLink + '"></span></a></td>'	
			+ '<td><a class="courseDelete courseTableIcon" name="' + data['course_assignment_id'] + '" class="delete" title="删除"  ></a></td>'
			+ '</tr>'
		);
	}
	else {
		var $newTr = $(
			'<tr class="tbody" id="assignment' + data['course_assignment_id'] + '">'
			+ '<td><label>' + data['course_assignment_title'] + '</label></td>'
			+ '<td><label id="assignmentDeadline' + data['course_assignment_id'] + '">' + data['course_assignment_deadline'] + '</label></td>'
			+ '<td><label id="createTime' + data['course_assignment_id'] + '">' + data['create_time'] + '</label></td>'
			+ '<td><label id="updateTime' + data['course_assignment_id'] + '">' + data['update_time'] + '</label></td>'
			+ '<td><a class="courseDownload courseTableIcon" name="assignmentDir'+ data['course_assignment_id'] + '" href="' + data['course_assignment_dir'] + '" target="_blank" title="下载"></a></td>'
			+ '</tr>'
		);
	}
	return $newTr;
}
// 设置界面上的FancyBox
function setAssignmentCreateFancybox() {
	$('#assignmentCreate').css('display', 'inline-block');
	$('#assignmentCreate').attr('href', 'frmCourseUploadAssignment.php?course_id=' + courseId);
	generalSetIFrame('#assignmentCreate',430,300);
}
function setAssignmentFTPFancybox() {
	$('#assignmentFTPUpload').css('display', 'block');
	$('#assignmentFTPUpload').attr('href', 'frmUploadAssignmentFtp.php?course_id='+courseId);
	generalSetIFrame('#assignmentFTPUpload',430,300);
}
function setAssignmentFTPSettingFancybox(){
	$('#assignmentFTPSetting').css('display', 'block');
	$('#assignmentFTPSetting').attr('href', 'frmSetAssignmentFtp.php?course_id='+courseId);
	generalSetIFrame('#assignmentFTPSetting',430,300);
}
function courseAssignmentDeleteOne(){
	var assignmentId = $(this).attr('name');
	$.get(
		Root+'del_assignment.php?course_id=' + courseId + '&course_assignment_id='+assignmentId,
		function(data) {
			if (data == '1') {
				$('#assignment' + assignmentId).remove();
			}	else {
				alert(data);	// alert error info
			}
		}
	);
}
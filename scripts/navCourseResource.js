function courseResourceInitPage() {
	$('#courseResourceSpecificShow table tr[class="tbody"]').remove();
	if (courseRelation == 'M' || courseRelation == 'T') {
		setCreateResourceFancybox();
	}
	courseResourceGetResource(courseId);
}

/* 得到课程资源 */
function courseResourceGetResource(courseId) {
	// 得到简介
	$.getJSON(
		Root + 'get_course_resourceinfo.php', {
			course_id: courseId
		}, 
		courseResourceGetResourceCallback
	);
}
function courseResourceGetResourceCallback(data) {
	if (courseRelation == 'M' || courseRelation == 'T') {					// 显示“新增”“编辑”和“删除”
		$('#courseCreateResource').css('display', 'block');
		$('th.manager').show();
	}
	$.each(data, function(i, dataSingle) {
		$('#courseResourceSpecificShow table').append(createResource(dataSingle));
	});
	$('#courseResourceSpecificShowAll').show();
	generalSetIFrame('a[title=编辑]',430,430);
	$('a[title=删除]').unbind();
	$('a[title=删除]').bind('click', courseResourceDeleteOne);	//添加删除事件
}

/* 生成一条记录 */
function createResource(data) {
	if(data['course_resource_type'] == 'O'){
			iconClass = 'courseDownloadOuter';
	} else {
		iconClass = 'courseDownload';
	}
	if (courseRelation == 'M' || courseRelation == 'T') {					// 管理者拥有“编辑”“删除”权限
		var modifyLink = 'frmCourseModifyResource.php?course_id='+courseId+'&id=' + 
			data['course_resource_id']+'&type='+data['course_resource_type'];
		var $newTr = $(
			'<tr class="tbody" id="resource'+data['course_resource_id']+'">'
			+ '<td><label>' + data['course_resource_title'] + '</label></td>'
			+ '<td><label id="createTime' + data['course_resource_id'] + '">' + data['create_time'] + '</label></td>'
			+ '<td><label id="updateTime' + data['course_resource_id'] + '">' + data['update_time'] + '</label></td>'
			+ '<td><a class="'+iconClass+' courseTableIcon" name="resourceDir'+ data['course_resource_id'] +'" href="' + data['course_resource_dir'] + '" target="_blank" title="下载"></a></td>'	
			+ '<td><a class="courseEdit courseTableIcon" name="' + data['course_resource_id'] + '" class="edit"   title="编辑" href="'+modifyLink+'"></a></td>'
			+ '<td><a class="courseDelete courseTableIcon" name="' + data['course_resource_id'] + '" class="delete" title="删除"  ></a></td>'
			+ '</tr>'
		);
	}	else {
		var $newTr = $(
			'<tr class="tbody" id="resource'+data['course_resource_id']+'">'
			+ '<td><label>' + data['course_resource_title'] + '</label></td>'
			+ '<td><label id="createTime' + data['course_resource_id'] + '">' + data['create_time'] + '</label></td>'
			+ '<td><label id="updateTime' + data['course_resource_id'] + '">' + data['update_time'] + '</label></td>'
			+ '<td><a class="'+iconClass+' courseTableIcon" name="resourceDir'+ data['course_resource_id'] +'" href="' + data['course_resource_dir'] + '" target="_blank" title="下载"></a></td>'	
			+ '</tr>'
		);
	}
	return $newTr;
}
function setCreateResourceFancybox() {
	generalSetIFrame('#courseCreateResource',430,430);
	$('#courseCreateResource').attr('href', 'frmCourseUploadResource.php?course_id=' + courseId);
}
function courseResourceDeleteOne(){
	var resourceId = $(this).attr('name');
	$.get(
		Root+'del_resource.php?course_id=' + courseId + '&course_resource_id='+resourceId,
		function(data) {
			if (data == '1') {
				$('#resource' + resourceId).remove();
			}
			else {
				alert(data);	// alert error info
			}
		}
	);
}
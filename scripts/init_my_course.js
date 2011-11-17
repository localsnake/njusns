function init_my_course_week() {
	if (globalUserType == 'T') {
		setCourseCreateFancyBox();
		$('.fancyboxCreate').show();	
	}	else {
		$('.fancyboxCreate').hide();
	}
	$.getJSON(Root+'get_courseinfo_to_syllabus.php',function (json) {
			var table_row = '';
			$.each(json, function(InfoIndex, Info) {
				var course_name = Info['course_name'];				
				var course_id = Info['course_id'];
				var course_time_string = Info['course_time'];
				var relation = Info['relation'];
				var status = Info['course_status'];
				if(status != 'U') {
					if(course_time_string != 'NULL') {
						do {
							var split_index = course_time_string.indexOf(':');
							var splited;
							if(split_index == -1)
								splited = course_time_string; 
							else {
								splited = course_time_string.substring(0,split_index);
								course_time_string = course_time_string.substring(split_index + 1);
							}
							var week = splited.charAt(0);
							var week_int = parseInt(week);
							
							var start_end_time = splited.substring(2);
							var start_end_index = start_end_time.indexOf('-');
							var start_time = start_end_time.substring(0,start_end_index);
							var start_time_int = parseInt(start_time);		
							var end_time = start_end_time.substring(start_end_index+1);
							var end_time_int = parseInt(end_time);
							/* // @todo course clashed 
							var clashed = false;
							for(var i = start_time_int; i < end_time_int; i++) {
								if(bitmap[week_int][i]) {
									alert('课程冲突！');
									clashed = true;
									break;
								}
							}
							*/
							var cell_id = '#td-' + week + '-' + start_time;						
							var tab = document.getElementById('weekSyllabusTable');
							tab.rows[start_time_int].cells[week_int].rowSpan = end_time_int - start_time_int + 1;
							//隐藏被rowspan大于1的所影响的cell
							for(var i = start_time_int + 1; i <= end_time_int; i++) {
								var removed_cell_id = '#td-' + week + '-' + i;
								$(removed_cell_id).hide();
							}
							//var cell_id = '#td-' + week + '-' + start_time;
							$(cell_id).html('<a class="' + relation + '" href = "javascript:jumpToCourse(' + course_id + ', \'' + relation + '\');">' +course_name + '</a>');							
						}while(split_index != -1);	
					}		
				}
			});	
	});	
	
	var count = 0;
	if(globalUserType == 'S') {		//只有学生就推荐
		// 获取推荐课程的信息
		$.getJSON(Root+'recommand_course.php',function(json){
			$.each(json, function(InfoIndex, Info) {
				var recommand_course_id = Info['course_id'];
				var recommand_course_name = Info['course_name'];
				var recommand_course_photo = Info['course_photo'];
				var recommand_course_relation = Info['relation'];	
				if(recommand_course_id != 0) {
					var div = '<div onclick="jumpToCourse('+recommand_course_id+',\''+ recommand_course_relation +'\');" class="picDiv">'+
								'<img class="userPhoto" src="'+ recommand_course_photo + 
								'" title="' + recommand_course_name +'"></img></div>';
					$('#courseRecommand').append(div);
					$('#recommendNotes').show();
					count++;
					if(count == 10) {
						$('#courseRecommand').append('<br/><br/><br/><br/><br/>');
					}
				}
			});
		});
	}
}


function init_my_course_list() {
	if (globalUserType == 'T') {
		setCourseCreateFancyBox();
		$('.fancyboxCreate').css('display', 'block');
	}	else {
		$('.fancyboxCreate').css('display', 'none');
	}
	/*--------------------------------------*/
	//$('#syllabusTable tr[class!=syllabusTableHeader]').remove();
	$.getJSON(Root+'get_courseinfo_to_syllabus.php',function (json) {							
			//var table_row = '';
			$.each(json, function(InfoIndex, Info) {
				var table_row = '';
				table_row += '<tr><td>' + Info['course_name'] + '</td>';
				table_row += '<td>' + Info['course_place'] + '</td>';
				
				
				var course_time_string = Info['course_time'];
				var course_time_display = '';
				if(course_time_string != 'NULL') {
					do {
						var split_index = course_time_string.indexOf(':');
						var splited;
						if(split_index == -1)
							splited = course_time_string; 
						else {
							splited = course_time_string.substring(0,split_index);
							course_time_string = course_time_string.substring(split_index + 1);
						}
						var weekday;
						switch(splited.charAt(0)) {
							case '1':weekday = '周一';break;
							case '2':weekday = '周二';break;
							case '3':weekday = '周三';break;
							case '4':weekday = '周四';break;
							case '5':weekday = '周五';break;
							case '6':weekday = '周六';break;
							case '7':weekday = '周日';break;
							default:break;
						}
						
						course_time_display += weekday + ' 第' + splited.substring(2) + '节';							
					}while(split_index != -1);	
				}	else {
					course_time_display = 'NULL';
				}
		
				table_row += '<td class="tableInfo">' + course_time_display + '</td>';
				table_row += '<td class="tableInfo">' + Info['course_teacher'] + '</td>';
				table_row += '<td class="tableInfo">' + Info['course_book'] + '</td>';
				table_row += '<td class="tableInfo">' + Info['course_type'] + '</td>';
				//table_row += '<td><a class="' + Info['relation'] + '" href = "javascript:jumpToCourse(' + Info['course_id'] + ', "' + Info['relation'] + '");">点击进入</a></td></tr>';		

				var course_id = Info['course_id'];		// course id
				var relation = Info['relation'];
				var status = Info['course_status'];
				
				var exit = '退出课程';
				var mountOrNot = '在课表中隐藏';
				var mountOrNotFunNo = 4;
				if(status == 'M') {
					mountOrNot = '在课表中显示';
					mountOrNotFunNo = 2;
				}
				var relationNo = 1;
				if(globalUserType == 'T' && relation == 'M'){
					exit = '删除课程';
					relationNo = 2;
				}
				var div_string = 
				'<div class="courseOpDiv">'  +
					'<a id="enter'+course_id+'" class="courseEnterCourse" title="进入课程"></a>';
				if(status == 'M') {
					div_string +=
					'<a id="mountCount'+course_id+'" class="courseUnMount" title="'+mountOrNot+'"></a>';
				} else {
					div_string +=
					'<a id="mountCount'+course_id+'" class="courseMount" title="'+mountOrNot+'"></a>';
				}
				div_string +=
					'<a id="delCourse'+course_id+'" class="courseExitCourse" title="'+exit+'"></a>'  +
				'</div>';
				
				table_row += '<td class="tableOperation>'+div_string+'</td></tr>';
				$('#syllabusTable').append(table_row);
				
				$('#enter'+course_id).bind('click', function(){
					jumpToCourse(course_id,relation);
				});
				$('#mountCount'+course_id).bind('click',function(){
					mountCourseHandler(course_id);
					/*
					if(status == 'M') {		// 已挂载 可以卸载
						unmountCourse(course_id);
					} else {					// 已卸载 可以挂载
						mountCourse(course_id);
					}
					*/
				});
				$('#delCourse'+course_id).bind('click',function(){
					if(exit == '退出课程') {
						quitCourse(course_id);
					} else {
						alert('del_course.php?course_id=course_id 要不要删呢？');
						//delCourse(course_id);
					}
				});
			});
	});			
}


function mountCourseHandler(course_id) {
	var classType = $('#mountCount' + course_id).attr('class');
	if (classType == 'courseMount') {
		$('#mountCount' + course_id).attr('class','courseUnMount');
		$('#mountCount' + course_id).attr('title','在课表中显示');
		$.post(
		Root + 'set_course_status.php', {
			course_id: course_id,
			status:	'M'
		},
		function(data) {
			if( data != '1') {
				//alert(data);
				return;
			}
			//alert('卸载课程成功,该课程将不在课程表上显示');
		}
		);
	}
	else if(classType == 'courseUnMount') {
		$('#mountCount' + course_id).attr('class','courseMount');
		$('#mountCount' + course_id).attr('title','在课表中隐藏');
		$.post(
		Root + 'set_course_status.php', {
			course_id: course_id,
			status:	'U'
		},
		function(data) {
			if( data != '1') {
				//alert(data);
				return;
			}
			//alert('挂载课程成功,该课程将在课程表上显示');
		}
		);
	}
}



function quitCourse(course_id) {
	$.post(
		Root + 'del_student.php', {
			student_id:	globalUserId,
			course_id: course_id
		},
		function(data) {
			if( data != '1') {
				alert(data);
				return;
			}
			alert('退出课程成功');
			init_my_course_list();
		}
	);
}

function delCourse(course_id) {
	$.post(
		Root + 'del_course.php', {
			course_id:	course_id
		},
		function(data) {
			if( data != '1') {
				alert(data);
				return;
			}
			alert('退出课程成功');
			init_my_course_list();
		}
	);
}

function changeFunc(funcNo,courseId,relationNo){
	if(funcNo == 1) {
		$('#rerun'+courseId+' span').html('点击进入');
	} else if(funcNo == 2){
		$('#rerun'+courseId+' span').html('卸载课程');
	} else if(funcNo == 3){
		if(globalUserType == 'T' && relationNo == 2){			//老师且管理该课程
			$('#rerun'+courseId+' span').html('删除课程');
		} else {
			$('#rerun'+courseId+' span').html('退出课程');
		}
	} else if(funcNo == 4){
		$('#rerun'+courseId+' span').html('挂载课程');
	}
}

function setCourseCreateFancyBox() {
	$('.fancyboxCreate').attr('href', 'frmCourseCreateCourse.html');
	generalSetIFrame('.fancyboxCreate',900,450);
}

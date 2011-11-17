
function init_calendar() {	
	setLanguage(globalLanguage);
	scheduler.xy.menu_width = 0;
	scheduler.config.details_on_dblclick = true;
	scheduler.config.details_on_create = true;
	scheduler.config.multi_day = true;
	scheduler.attachEvent('onClick',function(){ return false; });

	scheduler.config.prevent_cache = true;
	scheduler.init('scheduler_here',null,'week');
	scheduler.config.xml_date='%Y-%m-%d %H:%i';
	
	//scheduler.config.first_hour = 8;
	scheduler.load(Root+'load_event.php','json');		

	scheduler.attachEvent('onEventAdded',addEventHandler);
	scheduler.attachEvent('onEventChanged',changeEventHandler);
	scheduler.attachEvent('onBeforeEventDelete',deleteEventHandler);

	
	scheduler.templates.event_class = function(start,end,event){
		if(event.type != 'normal')
			return 'course_event';
		else if (start < (new Date())) //if date in past
			return 'past_event'; //then set special css class for it
	}
	setTimeout(setReadOnlyEvents,500);

}


function setLanguage(lang) {
	if(lang == 'en') {
		scheduler.locale={
				date:{
					month_full:["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
					month_short:["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
					day_full:["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
					day_short:["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
				},
				labels:{
					dhx_cal_today_button:"Today",
					day_tab:"Day",
					week_tab:"Week",
					month_tab:"Month",
					new_event:"New event",
					icon_save:"Save",
					icon_cancel:"Cancel",
					icon_details:"Details",
					icon_edit:"Edit",
					icon_delete:"Delete",
					confirm_closing:"",//Your changes will be lost, are your sure ?
					confirm_deleting:"Event will be deleted permanently, are you sure?",
					section_description:"Description",
					section_time:"Time period",
					full_day:"Full day",
					
					/*recurring events*/
					confirm_recurring:"Do you want to edit the whole set of repeated events?",
					section_recurring:"Repeat event",
					button_recurring:"Disabled",
					button_recurring_open:"Enabled",
					
					/*agenda view extension*/
					agenda_tab:"Agenda",
					date:"Date",
					description:"Description",
					
					/*year view extension*/
					year_tab:"Year"
				}
		};
	}
	else {
		scheduler.config.day_date="%M %d日 %D";
		scheduler.config.default_date="%Y年 %M %d日";
		scheduler.config.month_date="%Y年 %M";
		scheduler.locale={
			date: {
				month_full: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
				month_short: ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
				day_full: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"],
				day_short: ["日", "一", "二", "三", "四", "五", "六"]
			},
			labels: {
				dhx_cal_today_button: "今天",
				day_tab: "日",
				week_tab: "周",
				month_tab: "月",
				new_event: "新建日程",
				icon_save: "保存",
				icon_cancel: "关闭",
				icon_details: "详细",
				icon_edit: "编辑",
				icon_delete: "删除",
				confirm_closing: "请确认是否撤销修改!", //Your changes will be lost, are your sure?
				confirm_deleting: "是否删除日程?",
				section_description: "描述",
				section_time: "时间范围",
				full_day: "整天",

				confirm_recurring:"请确认是否将日程设为重复模式?",
				section_recurring:"重复周期",
				button_recurring:"禁用",
				button_recurring_open:"启用",
				
				/*agenda view extension*/
				agenda_tab:"议程",
				date:"日期",
				description:"说明",
				
				/*year view extension*/
				year_tab:"今年"
			}
		};
	}
}
			
			
	
function setReadOnlyEvents() {
	var evs = scheduler.getEvents(new Date(1990,1,1),new Date(9999,1,1)); 
    for (var i = 0; i < evs.length; i++)		
		if(evs[i].type != 'normal') {
			var readonly_id = evs[i].id;
			scheduler.getEvent(readonly_id).readonly = true;
		}
}
			
function deleteEventHandler(event_id, event_object) {
	if(scheduler.getEvent(event_id).type != 'normal') {
		return;
	}
	var convert = scheduler.date.date_to_str('%Y-%m-%d %H:%i');
	var start_date = convert(event_object.start_date);
	var end_date = convert(event_object.end_date);
	
	$.post(Root+'del_event.php',{event_id:event_id},function(reply) {
		scheduler.clearAll();
		scheduler.load(Root+'load_event.php','json');
		setTimeout(setReadOnlyEvents,500);
	});
	

}


function changeEventHandler(event_id, event_object) {
	if(scheduler.getEvent(event_id).type != 'normal') {
		scheduler.clearAll();
		scheduler.load(Root+'load_event.php','json');
		setTimeout(setReadOnlyEvents,500);
		return;
	}
	var convert = scheduler.date.date_to_str('%Y-%m-%d %H:%i');
	var start_date = convert(event_object.start_date);
	var end_date = convert(event_object.end_date);
	$.post(Root+'edit_event.php',{event_id:event_id,event_begintime:start_date,event_endtime:end_date,event_type:'',event_content:event_object.text},function(reply) {
					
	});
}

function addEventHandler(event_id, event_object) {
	var convert = scheduler.date.date_to_str('%Y-%m-%d %H:%i');
	var start_date = convert(event_object.start_date);
	var end_date = convert(event_object.end_date);
	event_object.type = 'normal';
	$.post(Root+'save_event.php',{event_begintime:start_date,event_endtime:end_date,event_type:'',event_content:event_object.text},function(reply) {
		var new_id = reply;
		scheduler.changeEventId(event_id,new_id);
	});

	
}


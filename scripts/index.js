//所有全局变量的定义
var Root = './includes/interface/';
var globalUserId = -1;
var courseId = -1;
var courseRelation = "Z";
var globalUserType = "N";
var userId = "-1";
var userRelation = "N";
var globalLanguage = 'zh-cn';
var lang = getParam('lang', '');

var configParam = {
	toolbar :	[['NewPage','Preview','-','Templates'],
				['Cut','Copy','Paste','PasteText','PasteFromWord'],
				['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
				['NumberedList','BulletedList'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				['Link','Unlink'],
				'/',
				['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
				['Font','FontSize'],
				['TextColor','BGColor'],
				['Maximize','ShowBlock'],
				['Image','Table','HorizontalRule','Smiley','SpecialChar'],
				],uiColor:'#dddddd',
	skin:'v2'
};			// CKEditor 的配置参数

$(document).ready(indexInitPage);		//界面准备好,初始化


var version = $.browser.version;
if($.browser.msie && ( version == "7.0" || version == "6.0")) {
	$(document).hashchange(function(){	// IE 无法识别window对象
		doHash();
	});
}
$(window).hashchange(function(){
	doHash();
});

$.l10n.init({		//初始化l10n库
	dir: 'languages',
	lang: lang,
	debug: false
});

/* 初始为登录界面 */
function indexInitPage() {
	$('#navigation a').bind('click', navClick);
	$('#subNavigation a').bind('click', subNavClick);
	$('#accountPwd').bind('click', accountPwdClick);
	$('#msgInfo').bind('click', msgInfoClick);
	$('#accountLogoff').bind('click', accountLogoffClick);
	
	// 搜索设置相关
	$('#searchBtn').bind('click', searchBtnClick);
	$('#searchKey').css('color','gray');
	$('#searchKey').attr('value','搜索好友、课程');
	$('#searchKey').bind('focus', searchFocus);
	$('#searchKey').bind('blur', searchBlur);
	$('#searchKey').bind('keypress', searchKeypress);
	initSearchAutoCompelete();							//设置搜索框的AutoComplete
	
	// 语言设置相关
	$('[domain=l10n]').l10n();
	$('#languageChange').bind('click',changeLanguageLinkClick);
	if($.cookie('language') == null || $.cookie('language') == 'zh-cn') {
	  T_('zh-cn');		//以后默认设置保存在Cookie中
	  globalLanguage = 'zh-cn';
	  $('#languageChange').html('English');
	} else {
	  $('#languageChange').html('中文');
	  globalLanguage = 'en';
	}
}


function changeLanguageLinkClick() {
	lang = $(this).html();
	if(lang == 'English') {
		T_('en');
		$(this).html('中文');
		$.cookie('language','en',{path: '/', expires:365 });
		globalLanguage = 'en';
	} else {
		T_('zh-cn');
		$(this).html('English');
		$.cookie('language','zh-cn',{path: '/', expires:365 });
		globalLanguage = 'zh-cn';
	}
	doHash();
}
/*刷新用户基本信息：用户头像 通知个数 站内信个数等*/
function refreshUserBaseInfo() {
	$.getJSON(
		Root + 'view_main_info.php',
		setUserBaseAfterGet
	);
}
function setUserBaseAfterGet(data){
	var type = data['user_type'];
	var userName = data['user_name'];
	var usergender = data['user_gender'];
	var userPhoto = data['user_photo'];
	var unreadCount = data['msg_unread_count'];
	//var totalCount = data['msg_total_count'];
	var applyCount = data['apply_count'];
	if(type == 'T') {
		type = '老师';
	} else {
		type = '同学';
	}
	$('#userface').attr('src',userPhoto);
	$('#username').html(userName);
	$('#usertype').html(type);
	if(unreadCount) {
		$('#unreadMsgCountLabel').html('('+unreadCount+')');
	} else {
		$('#unreadMsgCountLabel').html('');
	}
	if(applyCount > 0){
		$('#apply_count').html('(' + applyCount + ')');
	} else {
		$('#apply_count').html('');
	}
}
/* Desperate设置新建个数 */
function setUnreadMsgCount() {
	$.post(
		Root + 'get_msg_count.php',
		function(unreadCount) {
			if(unreadCount > 0) {
				$('#unreadMsgCountLabel').html("("+unreadCount+")");
			} else {
				$('#unreadMsgCountLabel').html('');
			}
		}
	);
}
/*刷新通知个数*/
function refreshAnnounceCount() {
	/*获取申请通知个数*/
	$.get(Root + 'get_apply_count.php',function (count) {
		if(count > 0) {
			$('#apply_count').html('(' + count + ')');
		}
		else {
			$('#apply_count').html('');
		}
	});
}

/*设置头像名字*/
function initUserBaseInfo() {
	$.getJSON(
		Root + 'view_info.php?type=base&user_id=' + globalUserId,
		setUserBaseInfo
	);
	setUnreadMsgCount();
}
function setUserBaseInfo(data) {
		var type = data['user_type'];
		var userName = data['user_name'];
		var usergender = data['user_gender'];
		var userPhoto = data['user_photo'];
		if(type == 'T') {
			type = '老师';
		} else {
			type = '同学';
		}
		$('#userface').attr('src',userPhoto);
		$('#username').html(userName);
		$('#usertype').html(type);
}


function searchFocus() {
	if($('#searchKey').val() == '搜索好友、课程') {
		$('#searchKey').css('color','black');
		$('#searchKey').attr('value','');
	}
}
function searchBlur() {
	if($('#searchKey').val() == '' || $('#searchKey').val() == null) {
		$('#searchKey').attr('value','搜索好友、课程');
		$('#searchKey').css('color','gray');
	}
}
function searchKeypress(event) {
	if (event.keyCode == '13') {
		/* 动画效果 */
		pushSearchBtn();
		setTimeout('bounceSearchBtn();',100);
		searchBtnClick();
	}
}

/* 将登录按钮弹起1px */
function bounceSearchBtn() {
	$('#searchBtn').css('top','0px');
}

/* 将按钮按下1px */
function pushSearchBtn() {
	$('#searchBtn').css('top','1px');
}

function initSearchAutoCompelete(){
	function format(row) {	
		if(row.name.match('[a-zA-Z]+') && row.name.length > 6)
			row.name = row.name.substring(0,6) + '...';
		if(!row.name.match('[a-zA-Z]+') && row.name.length > 4)
			row.name = row.name.substring(0,4) + '...';
		/*return '<img class="userPhoto" name="' + row.id + '" src= "' + Root + row.photo + '">' + row.name + '</img>';*/
		return '<img style = "float:left" class="userPhoto" name="' + row.id + '" src= "' + row.photo + '"/><a style = "float:left;position:relative;top:17px;cursor:pointer;color:#F38630;">' + row.name + '</a>';
		/*return '<li id = "auto_complete"><img class="userPhoto" name="' + row.id + '" src= "' + Root + row.photo + '"/><label id = "user_name">' + row.name + '</label></li>';*/
	}
	$('#searchKey').autocomplete(Root+'search_auto_complete.php', {
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
			$('#searchKey').val('');		//item.name
			if(item.relation == 'F') {
				//jumpToFriendHomepage(item.id, 'Y');
				jumpToFriendY(item.id, 'Y'); //小凡修改后的函数
			} else{			// 在小凡修改了权限查看后这里修改函数	
				$.get(
					Root + 'get_relation.php', {
						type: 'course', 
						id: item.id
					}, 
					function (relation) {
						jumpToCourse(item.id, relation);
					}
				);
			}
		});
}

/* 
 * 导航栏的改变
 * navNeedShow：是否显示主导航
 * subNavNeedShow：是否显示子导航
 * whichNav：主导航的哪个元素需要高亮显示
 * whichSubClass：子导航的哪种类需要显示
 * whichSubNav：子导航的哪个元素需要高亮显示
 */
function changeNav(navNeedShow, subNavNeedShow, whichNav, whichSubClass, whichSubNav) {
	// 若whichNav，whichSubClass，whichSubNav有误
	/** 补充跳转 */
	/* 判断主导航 */
	if (navNeedShow == true) {				// 显示主导航
		$('#navigation a').each(
			function(index) {
				$(this).removeClass('active');
				$(this).removeClass('inactive');
		    	if (this.id == whichNav) {
					$(this).addClass('active');
				} else {
					$(this).addClass('inactive');
				}
				$(this).show();
			});
	} else {								    // 不显示主导航
		$('#navigation a').hide();
	}
	//alert('changeNav ' + navNeedShow + " - " + subNavNeedShow  + "-" + whichNav + "-" + whichSubClass + "-" + whichSubNav);
	/* 判断子导航 */	
	var version = $.browser.version;
	if (subNavNeedShow == true) {			// 显示子导航
		$('#subNavigation a').each(function(index) {	// 设置active或inactive
			$(this).removeClass('active');
			$(this).removeClass('inactive');
			if (this.id == whichSubNav) {
				$(this).addClass('active');
			} else {
				$(this).addClass('inactive');
			}
			var classArrayString = $(this).attr('class');
			var splitIndex = classArrayString.indexOf(' ');
			var mainClass = classArrayString.substring(0,splitIndex);
			if ($(this).hasClass(whichSubClass) || mainClass == whichSubClass) {		// 设置是否显示
				$(this).css('display','inline-block');
				$(this).show();
			} else {
				$(this).css('display','none');
				$(this).hide();
			}
  		});
		
		//if(whichSubClass && whichSubClass != 'undefined') alert('#subNavigation .'+whichSubClass);
		//if(whichSubClass && whichSubClass != 'undefined') $('#subNavigation .'+whichSubClass).show();
		//if(whichSubClass && whichSubClass != 'undefined') $('#subNavigation .'+whichSubClass).css('display','inline-block');
	} else {	// 子导航全部不显示
		$('#subNavigation a').hide();
	}
	if($.browser.msie && ( version == "8.0" || version == "7.0" || version == "6.0")) {
		if(whichSubClass && whichSubClass != 'undefined') $('#subNavigation a').hide();
		if(whichSubClass && whichSubClass != 'undefined')  $('#subNavigation .'+whichSubClass).show();
	}	
}


/* 判断账号与搜索 */
function changeAccount(accountShow) {
	if (accountShow == true) {
		$('#accountPane').show();
	} else {
		$('#accountPane').hide();
	}
}

function changeSearch(searchShow) {
	if (searchShow == true) {
		$('#searchPane').show();
	} else {
		$('#searchPane').hide();
	}
}

/* 单击进行搜索 */
function searchBtnClick() {
	if ($('#searchKey').val() == '' || $('#searchKey').val() == null || $('#searchKey').val() == '搜索好友、课程') {
		return; // donothing;
	}
	changeNav(true, false, '', '', '');
	changeAccount(true);
	changeSearch(true);
	changeContentPane('search.html', $('#searchKey').val());
}

/* 鼠标移动到帐号上 */
function accountMouseover() {
	$('#accountPane li').show();
}
/* 鼠标从账号上移出 */
function accountMouseout() {
	$('#accountPane li').hide();
	/* 将活动li重置为accountPwd */
	$('#accountPwd').removeClass();
	$('#accountPwd').addClass('active');
	$('#accountLogoff').removeClass();
	$('#accountLogoff').addClass('inactive');
}
/* 改变活动li */
function accountLiMouseover() {
	if (this.id == 'accountPwd') {
		$('#accountPwd').removeClass();
		$('#accountPwd').addClass('active');
		$('#accountLogoff').removeClass();
		$('#accountLogoff').addClass('inactive');
	} else {
		$('#accountPwd').removeClass();
		$('#accountPwd').addClass('inactive');
		$('#accountLogoff').removeClass();
		$('#accountLogoff').addClass('active');
	}
}
/* 单击修改密码 */
function accountPwdClick() {
	changeNav(true, false, '', '', '');
	changeAccount(true);
	changeSearch(true);
	changeContentPane('changePwd.html');
}
/*单击显示站内信子菜单*/
function msgInfoClick(){ 
	changeNav(true, true, '', 'navMsg', 'navMsgInbox');
	changeContentPane('navMsgInbox.html');
}
/* 单击注销账号 */
function accountLogoffClick() {
	$.cookie('keepLogin',null,{path: '/'});		// 删除自动登录Cookie
	$.post(
		Root + 'logout.php', 
		function(data) {
			if (data == '1') {
				$('#top_nav').hide();				//用户信息界面不显示
				changeNav(false, false, '', '', '');
				changeAccount(false);
				changeSearch(false);
				changeContentPane('login.html');
				$('#suggestion').hide();			//隐藏建议链接
				setLoginPageFancybox();	
				globalUserId = -1;
			}
		}
	);
}

/* 修改contentPane的src属性，即导入新的src内容 */
function changeContentPane(newSrc, data) {
	/* 这些是新加的用于hash的前进后退 by Xiao Fan*/
	var navActive = $('#navigation a.active').attr('id');
	var subNavActive = $('#subNavigation a.active').attr('id');
	//var subNavList = $('#subNavigation a[style*="display: inline-block"]');	// 这句话对IE6-8是不支持的
	var subNavList = $('#subNavigation a:visible');
	var subNavClass;
	subNavList.each(function (index){
		var id = $(subNavList[0]).attr('id');
		if (subNavList.length == 1) {
			if (id == 'navCourseIntro') {
				subNavClass = 'navCourseStranger';
			} else if (id == 'navScheduleInfo') {
				subNavClass = 'navSchedule';
			} else if (id == 'navFriendHomepage') {
				subNavClass = 'navFriendStranger';
			} else if (id == 'navFriendInfo') {
				subNavClass = 'navFriend';
			} else {
				alert('Warning, warning, Xiao Fan"s mistake here!');
			}
		} else {
			if (id=='navMainAnnounce' || id=='navMainFreshmilk') {
				subNavClass = 'navMain';
			} else if (id=='navUserInfo' || id=='navUserNews') {
				subNavClass = 'navUser';
			} else if (id=='navCourseList' || id=='navCourseWeek' || id == 'navCourseTotal') {
				subNavClass = 'navCourse';
			} else if (id=='navCourseDiscussion' || id=='navCourseResource' || id=='navCourseAssignment' || id=='navCourseLecture' || id=='navCourseStudent' || id=='navCourseIntro' || id=='navCourseNews') {
				subNavClass = 'navCourseNone';
			} else if (id=='navFriendHomepage' || id=='navFriendNews') {
				subNavClass = 'navFriendNone';
			} else if(id== 'navFriendInfo'){
				subNavClass = 'navFriend';
			} else if( id == 'navMsgSentMail' || id == 'navMsgInbox' || id == 'navMsgSend') {
				subNavClass = 'navMsg';
			} else {
				alert('需要添加代码了哦');
			}
		}
	});
	setHash('#' + newSrc + '?' + userId + '&' + userRelation + '&' + courseId + '&' + courseRelation + 
				'&' + navActive + '&' + subNavClass + '&' + subNavActive + '&' + data);
	/* 这些是新加的用于hash的前进后退end by Xiao Fan*/
}

function loadContentPane(newSrc, data) {
	$('#loading').fadeIn();
	//$('#contentPane').load(newSrc+"?rand="+Math.random()*99999, '', function() {
	$('#contentPane').load(newSrc+"?v="+'1.0.1', '', function() {
		if(newSrc != 'login.html' && newSrc != 'registration.html'){
			refreshUserBaseInfo();
		}
		switch(newSrc) {
		case 'login.html':
			loginInitPage();
			break;
		case 'registration.html':
			registrationInitPage();
			break;
		case 'navMainCourseFeed.html':
			mainCourseFeedInitPage();
			break;
		case 'navUserNews.html':
			userNewsInitPage();
			break;
		case 'navUserInfo.html':
			userInfoInitPage();
			break;
		case 'navMainFreshmilk.html':
			mainFreshmilkInitPage();
			break;
		case 'navMainAnnounce.html':
			mainAnnounceInitPage();
			break;
		case 'navCourseWeek.html':
			init_my_course_week();
			break;
		case 'navCourseList.html':
			init_my_course_list();
			break;
		case 'navCourseTotal.html':
			totalCourseInit('数据');
			break;
		case 'navCourseNews.html':
			courseNewsInitPage(data, courseRelation);
			break;
		case 'navCourseIntro.html':
			courseIntroInitPage(data, courseRelation);
			break;
		case 'navCourseStudent.html':
			courseStudentInitPage();
			break;
		case 'navCourseLecture.html':
			courseLectureInitPage();
			break;
		case 'navCourseAssignment.html':
			courseAssignmentInitPage();
			break;
		case 'navCourseDiscussion.html':
				init_discussion_area_list();
				//setTimeout('init_discussion_release_list('+courseAreaId+')',300);
			break;
		case 'navCourseResource.html':
			courseResourceInitPage();
			break;
		case 'navScheduleInfo.html':
			setTimeout(init_calendar,100);	//防止calendar所需的js库未加载
			break;
		case 'navFriendInfo.html':
			friendInfoInitPage();
			break;
		case 'navFriendNews.html':
			friendNewsInitPage(data, userRelation);
			break;
		case 'navFriendHomepage.html':
			friendHomepageInitPage(data, userRelation);
			break;
		case 'changePwd.html':
			changePwdInitPage();
			break;
		case 'search.html':
			searchInitPage(data);
			break;
		case 'navMsgSend.html':
			initMsgSendPage();
			break;
		case 'navMsgInbox.html':
			initMsgInboxPage();
			break;
		case 'navMsgSentMail.html':
			initMsgSentMailPage();
			break;
		default:	// 地址错误，肿么办，需要补充
			alert('in loadContentPane, invalid html address: ' + newSrc + '. Don"t know what to do.T.T.');
			/** 补充跳转 */
		}
		if(globalLanguage == 'zh-cn'){
			T_(globalLanguage);
			$('#subNavigation a').css('font-size','14px;');	//中文则改为14px
		}
		$('#loading').fadeOut();
	});
}


/* 这些是和hash控制的前进后退有关的函数 by Xiao Fan*/
function setHash(a){			
	if (a == location.hash) {	// 这时浏览器检测不到hashchange，需要手动dohash
		doHash();
	} else {					// 此时，会改变地址栏地址，浏览器检测hashchange
		$.browser.msie?$.locationHash(a):location.hash=a;		
	}
}
function doHash(){
	var h = location.hash;
	// 需要判断hash是否有效，若否，则跳转到首页
	if (h == null || h == '') {
		/** 补充跳转 */
		// 这是临时方法
		changeNav(true, true, 'navMain', 'navMain', 'navMainCourseFeed');
		changeContentPane('navMainCourseFeed.html');
		/** 想法：判断当前globalUserId，（是否为登录状态），如php，若是，则跳转首页新鲜事，否则，跳转登录页面 */
		return;
	} 
	var addrAndArgs = h.split('#')[1].split('?');
	if (addrAndArgs.length < 2) {
		/** 补充跳转 */
		return;
	}
	var addr = addrAndArgs[0];
	var args = addrAndArgs[1].split('&');
	if (args.length < 8) {
		/** 补充跳转 */
		return;
	}
	userId = args[0];
	userRelation = args[1];
	courseId = args[2];
	courseRelation = args[3];
	changeNav(true, true, args[4], args[5], args[6]);
	data = args[7];
	loadContentPane(addr, data);
}
/* 这些是和hash控制的前进后退有关的函数end by Xiao Fan*/

/* 点击主导航栏的事件处理函数 */
function navClick() {
	switch($(this).attr('id')) {
	case 'navMain':
		changeNav(true, true, 'navMain', 'navMain', 'navMainCourseFeed');
		changeContentPane('navMainCourseFeed.html');
		break;
	case 'navUser':
		changeNav(true, true, 'navUser', 'navUser', 'navUserNews');
		changeContentPane('navUserNews.html');
		break;
	case 'navCourse':
		changeNav(true, true, 'navCourse', 'navCourse', 'navCourseWeek');
		changeContentPane('navCourseWeek.html');
		break;
	case 'navSchedule':
		changeNav(true, true, 'navSchedule', 'navSchedule', 'navScheduleInfo');
		changeContentPane('navScheduleInfo.html');
		break;
	case 'navFriend':
		changeNav(true, true, 'navFriend', 'navFriend', 'navFriendInfo');
		changeContentPane('navFriendInfo.html');
		break;
	}
}

/* 点击子导航栏的事件处理函数 */
function subNavClick() {
	switch($(this).attr('id')) {
	case 'navMainCourseFeed':
		changeNav(true, true, 'navMain', 'navMain', 'navMainCourseFeed');
		changeContentPane('navMainCourseFeed.html');
		break;
	case 'navMainAnnounce':
		changeNav(true, true, 'navMain', 'navMain', 'navMainAnnounce');
		changeContentPane('navMainAnnounce.html');
		break;
	case 'navMainFreshmilk':
		changeNav(true, true, 'navMain', 'navMain', 'navMainFreshmilk');
		changeContentPane('navMainFreshmilk.html');
		break;
	case 'navUserInfo':
		changeNav(true, true, 'navUser', 'navUser', 'navUserInfo');
		changeContentPane('navUserInfo.html');
		break;
	case 'navUserNews':
		changeNav(true, true, 'navUser', 'navUser', 'navUserNews');
		changeContentPane('navUserNews.html');
		break;
	case 'navCourseList':
		changeNav(true, true, 'navCourse', 'navCourse', 'navCourseList');
		changeContentPane('navCourseList.html');
		break;
	case 'navCourseTotal':
		changeNav(true, true, 'navCourse', 'navCourse', 'navCourseTotal');
		changeContentPane('navCourseTotal.html');
		break;
	case 'navCourseWeek':
		changeNav(true, true, 'navCourse', 'navCourse', 'navCourseWeek');
		changeContentPane('navCourseWeek.html');
		break;
	case 'navCourseDiscussion':
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseDiscussion');
		changeContentPane('navCourseDiscussion.html');
		break;
	case 'navCourseResource':
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseResource');
		changeContentPane('navCourseResource.html');
		break;
	case 'navCourseAssignment':
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseAssignment');
		changeContentPane('navCourseAssignment.html');
		break;
	case 'navCourseLecture':
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseLecture');
		changeContentPane('navCourseLecture.html');
		break;
	case 'navCourseStudent':
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseStudent');
		changeContentPane('navCourseStudent.html');
		break;
	case 'navCourseIntro':
		if (courseRelation == 'M' || courseRelation == 'A' || courseRelation == 'T') {
			changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseIntro');
			changeContentPane('navCourseIntro.html');
		} else {
			changeNav(true, true, 'navCourse', 'navCourseStranger', 'navCourseIntro');
			changeContentPane('navCourseIntro.html', courseId);
		}
		break;
	case 'navCourseNews':
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseNews');
		changeContentPane('navCourseNews.html', courseId);
		break;		
	case 'navScheduleInfo':
		changeNav(true, true, 'navSchedule', 'navSchedule', 'navScheduleInfo');
		changeContentPane('navScheduleInfo.html');
		break;
	case 'navFriendInfo':
		changeNav(true, true, 'navFriend', 'navFriend', 'navFriendInfo');
		changeContentPane('navFriendInfo.html');
		break;
	case 'navFriendHomepage':
		if (userRelation == 'Y') {
			changeNav(true, true, 'navFriend', 'navFriendNone', 'navFriendHomepage');
			changeContentPane('navFriendHomepage.html', userId);
		} else {
			changeNav(true, true, 'navFriend', 'navFriendStranger', 'navFriendHomepage');
			changeContentPane('navFriendHomepage.html', userId);
		}
		break;
	case 'navFriendNews':
		changeNav(true, true, 'navFriend', 'navFriendNone', 'navFriendNews');
		changeContentPane('navFriendNews.html', userId);
		break;
	case 'navMsgInbox':
		changeNav(true, true, '', 'navMsg', 'navMsgInbox');
		changeContentPane('navMsgInbox.html');
		break;
	case 'navMsgSentMail':
		changeNav(true, true, '', 'navMsg', 'navMsgSentMail');
		changeContentPane('navMsgSentMail.html');
		break;
	case 'navMsgSend':
		changeNav(true, true, '', 'navMsg', 'navMsgSend');
		changeContentPane('navMsgSend.html');
		break;
	default:
		alert('这里是枚举，需要填代码了');
		break;
	}
}



/* 下面是Login.js里面的内容 移到这里*/
/* 初始化函数 */
function loginInitPage() {
	/* 这边设置初始界面的内容 */
	$().ready(function() {
		$('#contentPane').css({padding:0});
		$('#contentPane').css({overflow:'auto'});
		$('#loginBtn').bind('click', loginLogin);
		$('#loginPwd').bind('keypress', loginLoginKey);
		$('#regBtnPage').bind('click', loginRegister);
		$('#top_nav').hide();
		$('#loginEmail').autocomplete( Root+'automail.php', {
			width: 195,
			selectFirst: false,
			scroll: false
		});
		if($.cookie('keepLogin')){			//如果自动登录有值，直接自动登录
			$.post(
				Root+'user_login.php', 
				{	email: $.cookie('loginEmail'),
					password: $.cookie('loginPwd')
				},
				loginAfterPost,
				'json'
			);
		}
		if($.cookie('loginEmail')) {  //如果有值
			$('#loginEmail').val($.cookie('loginEmail'));
			$('#loginPwd').focus();
			$('#loginEmail').css('z-index','10');
		}
		setLoginPageFancybox();		//设置登录界面的FancyBox
	});
}

/* 登录 */
function loginLogin() {
	/* 检查登录信息 */
	$.post(
		Root+'user_login.php', 
		{	email: $('#loginEmail').val(),
			password: $('#loginPwd').val()
		},
		loginAfterPost,
		'json'
	);
}
/* 捕获键盘 */
function loginLoginKey(event){
	if (event.keyCode == '13') {
		/* 动画效果 */
		pushLoginBtn();
		setTimeout('bounceLoginBtn();',100);
		loginLogin();
	}
}
/* 将登陆按钮弹起1px */
function bounceLoginBtn() {
	$('#loginBtn').css('top','10px');
}
/* 将登陆按钮按下1px */
function pushLoginBtn() {
	$('#loginBtn').css('top','11px');
}
/* 处理登录返回数据 */
function loginAfterPost(data) {
	if (data['user_id'] >= 1) {	//登录成功
		if($('#loginEmail').val()) generalSetCookie('loginEmail',$('#loginEmail').val());
		if($('#loginPwd').val()) generalSetCookie('loginPwd',$('#loginPwd').val());
		if( $('#keepLogin').attr('checked')){
			generalSetCookie('keepLogin',true);
		} else {
			generalSetCookie('keepLogin',null);
			generalSetCookie('loginPwd',null);
		}
		globalUserId = data['user_id'];
		globalUserType = data['user_type'];
		generalSetCookie('globalUserId',globalUserId);
		generalSetCookie('globalUserType',globalUserType);
		// 获取信息，进入首页的新鲜事
		$('#errorEcho label').text('');
		$('#errorEcho').css('visibility', 'hidden');
		changeAccount(true);		//显示账号框
		changeSearch(true);		//显示搜索框
		//refreshUserBaseInfo();	//设置用户导航基本信息
		$('#top_nav').show();	 	//显示用户信息界面
		$('#contentPane').css({padding:'20px'});
		
		// 进入新鲜事页面-课程通知
		changeNav(true, true, 'navMain', 'navMain', 'navMainCourseFeed');	
		changeContentPane('navMainCourseFeed.html');	
		
		//显示建议链接
		$('#suggestion').show();
	}
	/* 登录失败 */
	else {
		$('#errorEcho label').text(data['error']);
		$('#errorEcho').css('visibility', 'visible');
		globalUserId = '-1';
		globalUserType = 'N';
	}
}

/* 注册 */
function loginRegister() {
	changeContentPane('registration.html','');
}
function setLoginPageFancybox() {
	generalSetIFrame('#forgotPassword',430,280);
	generalSetIFrame('#sendConfirmCode',430,280);
}

/* ------------------ 以下是重构以后的通用函数 ----------------------------------- */
/**
 * 设置Fancybox的通用函数，没有标题
 * string selectorName
 * integer width
 * integer height
 */ 
function generalSetIFrame(selectorName,width,height){
	$(selectorName).fancybox({
		'titleShow'  		: false,
		'showCloseButton'	: true,
		'width'				: width,
		'height'			: height,
		'autoScale'			: false,
		'transitionIn'		: 'none',
		'transitionOut'		: 'none',
		'type'				: 'iframe'
	});
	$(selectorName).show();
}
/**
 * 设置Cookie值 保留7天 
 * string name
 * string value
 */
function generalSetCookie(name,value) {
	$.cookie(name,value,{path: '/', expires:7 });
}

/**
 * 获取URL的get参数
 * string key
 * string value
 */ 
function getParam(key, value) {
	var s = document.location.search;
	if (!s) return value;
	s = s.substr(1);
	var re = new RegExp('(^|\&)(' + key + ')\=([^\&]*)(\&|$)', 'gi');
	var res = re.exec(s);
	return (res != null) ? decodeURIComponent(res[3]) : value;
}

/** 
 * 翻译HTMl的函数, 并设置中英文下CSS变化
 * string lang
 */ 
function T_(lang) {
	if(lang == 'zh-cn') {
		$('#subNavigation a').css('font-size','15px');
	}	else {		//改变英文二级导航字体大小
		$('#subNavigation a').css('font-size','14px');
	}
	$('[domain=l10n]').l10n({
		'lang': lang
	});
}

/**
 *  跳转到登录界面，隐藏搜索，账户面板
 */
function generalGotoLoginPage(){
	changeAccount(false);
	changeSearch(false);
	changeNav(false, false, "", "", "");
	changeContentPane('login.html','');
}

/**
 * 跳转到讨论区的某个帖子
 */
function jumpToRelease (course_id,release_id,permission_no){
	courseId = course_id;
	$.get(
		Root + 'get_relation.php', {
			type: 'course', 
			id: courseId
		}, 
		function (relation) {
			courseRelation = relation;
			changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseDiscussion');
			changeContentPane('navCourseDiscussion.html');
			
			$(document).ready(function(){
				setTimeout('init_release_content('+release_id+','+permission_no+');',300);
			});
		}
	);
}

/**
 * 进入课程主页(关系未知情况下)
 * 要求name区域是courseId
 */
function enterCourseHomepage() {
	// 得到和此课程的关系
	var courseId = $(this).attr('name');
	// 根据关系判断权限
	$.get(
		Root + 'get_relation.php', {
			type: 'course',
			id: courseId
		}, 
		function (data) {
			jumpToCourse(courseId, data);
		}
	);
}
function jumpToCourse(id, relation) {
	if(relation == 'M' || relation == 'T') {
		jumpToCourseM(id,relation);
	} else if(relation == 'A') {
		jumpToCourseA(id,relation);
	} else if(relation == 'W'){
		jumpToCourseW(id,relation);
	} else if(relation == 'N') {
		jumpToCourseN(id,relation);
	} else {
		courseId = id;				// set global var, (see index.php)
		courseRelation = relation;	// set global var, (see index.php)
		changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseNews');
		changeContentPane('navCourseNews.html', courseId);
	}
}
function jumpToCourseM(id, relation) {
	courseId = id;				// set global var, (see index.php)
	courseRelation = relation;	// set global var, (see index.php)
	changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseNews');
	changeContentPane('navCourseNews.html', courseId);
}
function jumpToCourseA(id, relation) {
	courseId = id;				// set global var, (see index.php)
	courseRelation = relation;	// set global var, (see index.php)
	changeNav(true, true, 'navCourse', 'navCourseNone', 'navCourseNews');
	changeContentPane('navCourseNews.html', courseId);
}
function jumpToCourseW(id, relation) {
	courseId = id;				// set global var, (see index.php)
	courseRelation = relation;	// set global var, (see index.php)
	changeNav(true, true, 'navCourse', 'navCourseStranger', 'navCourseIntro');
	changeContentPane('navCourseIntro.html', courseId);
}
function jumpToCourseN(id, relation) {
	courseId = id;				// set global var, (see index.php)
	courseRelation = relation;	// set global var, (see index.php)
	changeNav(true, true, 'navCourse', 'navCourseStranger', 'navCourseIntro');
	changeContentPane('navCourseIntro.html', courseId);
}

/**
 * 进入好友个人主页(关系未知情况下) 要求name区域是userId
 */
function enterFriendHomepage() {
	// 得到和此人的关系
	var friendId = $(this).attr('name');
	if (friendId == globalUserId) {	// 查看自己 
		changeNav(true, true, "navUser", "navUser", "navUserNews");
		changeAccount(true);
		changeSearch(true);
		changeContentPane("navUserNews.html");
	} else {
		// 根据关系判断权限
		$.get(
			Root + "get_relation.php", {
				type: "user",
				id: friendId
			}, 
			function (data) {
				jumpToFriend(friendId, data);
			}
		);
	}
}
/* 跳转到好友个人主页 */
function jumpToFriend(id, relation) {
	if (relation == 'Y') {
		jumpToFriendY(id, relation);
	} else if (relation == 'N') {
		jumpToFriendN(id, relation);
	} else {
		jumpToFriendW(id, relation);
	}
}
function jumpToFriendY(id, relation) {
	userId = id;				// set global var, (see index.php)
	userRelation = relation;	// set global var, (see index.php)
	// 暂时直接跳到资料页面，以后有了感兴趣的动态以后更改 navFriendNews
	changeNav(true, true, 'navFriend', 'navFriendNone', 'navFriendHomepage');
	changeContentPane('navFriendHomepage.html', userId);
}
function jumpToFriendN(id, relation) {
	userId = id;				// set global var, (see index.php)
	userRelation = relation;	// set global var, (see index.php)
	changeNav(true, true, 'navFriend', 'navFriendStranger', 'navFriendHomepage');
	changeContentPane('navFriendHomepage.html', userId);
}
function jumpToFriendW(id, relation){	// 等待申请
	userId = id;			// set global var
	userRelation = relation;// set global var
	changeNav(true, true, 'navFriend', 'navFriendStranger', 'navFriendHomepage');
	changeContentPane('navFriendHomepage.html', userId);
}
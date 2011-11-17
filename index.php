<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>学术社交网</title><link rel="icon" href="localsnake.ico" type="image/x-icon"/>
  <link rel="stylesheet" type="text/css" href="css/index.css?v=1.1.0" media="screen"/>
  <link rel="stylesheet" type="text/css" href="lib/fancybox/fancy_autocomplete.css" media="screen" /><!--Fancy Box-->
  <script type="text/javascript" src="lib/njusns_lib_min.js" ></script>  
  <script type="text/javascript" src="scripts/index.js" ></script>
  <script src="ckeditor/ckeditor.js" type="text/javascript"></script>
<?php if(!isSet($_SESSION['user_id']) || $_SESSION['user_id'] <= 0 ){ ?>
  <script type="text/javascript">
	$(function(){	//尚未登录 进入登录页面
		globalUserId = -2;
		generalGotoLoginPage();
	});
   </script>
<?php }else { ?>
   <script type="text/javascript">
    //已经登录过了，直接进入首页
	$(function(){
		// init global vars
		curPage = 1;
		globalUserId = <?php echo $_SESSION['user_id']; ?>;
		globalUserType = "<?php echo $_SESSION['user_type']; ?>";
		if($.cookie('language') != null) 	globalLanguage = $.cookie('language');
		// 获取信息，进入首页的新鲜事
		$('#errorEcho label').text("&nbsp;");
		$('#errorEcho').css('visibility', "hidden");
		changeAccount(true);
		changeSearch(true);	
		$('#contentPane').css({padding:"20px"});	 // 统一设置contentPane的上下padding
		$('#top_nav').show();						 // 显示用户信息界面
		$('#suggestion').show();					 // 显示用户建议按钮
		setHash(location.hash);						 // 获取当前hash地址，并进行刷新
	});
  </script>
<?php } ?>


<script type="text/javascript">
	//Google Analytics
	 var _gaq = _gaq || [];
	 _gaq.push(['_setAccount', 'UA-26552799-1']);
	 _gaq.push(['_setCookiePath', '/njusns/']);
	 _gaq.push(['_trackPageview']);
	 (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	 })();
</script>
</head>
<body>
<!--[if lt IE 8]> 
<style type="text/css">
#searchBtn{	top:-30px;	}
#headerPane{	left:-1px;		}
.loginInputType{	margin-left:0px;	}
#tableOperation{	width:120px;	}
</style>
<![endif]-->

<!--中间容器-->
<div id="container">
    <!-- 此为左侧的主功能导航区域 -->
	<!--在注册和登录时，需要设置这个div的display属性为none -->
	<div id="top_nav" style="display:none;">
		<div id="menubar" >
			<div id="menuitems">
				<a id="msgInfo"><span domain="l10n">Message</span>
				<label id="unreadMsgCountLabel" class="darkRed"></label></a>
				<span>|</span>
				<a id="accountPwd" domain="l10n">ResetPwd</a>
				<span>|</span>
				<a id="accountLogoff" domain="l10n">Logout</a>
				<!--<span>|</span><a id="languageChange" domain="l10n">English</a>-->
				<input type="text" id="searchKey"/>
				<a id="searchBtn" name="searchBtn"></a>
			</div>
		</div>
		<div id="titlepane">
			<img id="face_title" src="./images/face_title.png" alt="face" />
			<div id="user_title">		
			<span id="username">Username</span>
			<span id="usertype" domain="l10n"></span>
			<!--</span> 您好!</span>-->
			</div>
			<!--<img id="userface" width="60" height="60" src="./images/nav/face.png" alt="myface" />-->
			<img id="userface" width="60" height="60" style="width:60px;height:60px;" src="#" alt="myface" />
		</div>
		<div id="navigation">
			<div id="itemwrapper">
				<div class="navitem" id="navitme1">
					<a id="navMain" domain="l10n" class="active">Home</a>
				</div>
				<div class="navitem" id="navitme2">
					<a id="navUser" domain="l10n"  class="inactive" >Profile</a>
				</div>
				<div class="navitem" id="navitme3">
					<a id="navCourse" domain="l10n" class="inactive" >Classes</a>
				</div>
				<div class="navitem" id="navitme4">
					<a id="navSchedule" domain="l10n" class="inactive" >Agenda</a>
				</div>
				<div class="navitem" id="navitme5">
					<a id="navFriend" domain="l10n" class="inactive" >Friends</a>
				</div>
				<div class="navitem" id="navitme6">
					<a id="navSuggestion" domain="l10n" class="inactive" href="suggestion.html" target="_blank">Suggestion</a>
				</div>

			</div>
		</div>
	</div>
	
	<!-- 此为右侧的首部区域和子功能导航的主内容区域 -->
	<div id="rightPane">	
		<div id='headerPane' style='display:inline-block;'>
 		<!--头部元素Logo-->
		<div id="loading" style="display:none;"></div>
		<!-- 此为子导航区域 -->
		<div id="subNavigation">
		<!-- 登录界面的二级导航栏处的“登录|注册新用户” -->	
		<a id="navLogin"  domain="l10n" class="navLogin inactive">Login</a>
		<a id="navRegister" domain="l10n" class="navLogin inactive">Register</a>

		<a id="navMainAnnounce" class="navMain inactive subNavBarRight subNavMainPos">
			<span domain="l10n">Notification</span><label id="apply_count"></label>
		</a>
		<a id="navMainFreshmilk" domain="l10n" class="navMain inactive subNavBarMiddle" >Newsfeed</a>
		<a id="navMainCourseFeed" domain="l10n" class="navMain  active subNavBarLeft">Class</a>
		
		<a id="navUserInfo"	domain="l10n" class="navUser inactive subNavBarRight subNavUserPos">Info</a>
		<a id="navUserNews"	domain="l10n" class="navUser inactive subNavBarLeft">News</a>
		
		<a id="navCourseTotal" domain="l10n"class="navCourse inactive subNavBarRight subNavCoursePos">Total Course</a>
		<a id="navCourseList" domain="l10n"class="navCourse inactive subNavBarMiddle">List View</a>
		<a id="navCourseWeek" domain="l10n" class="navCourse inactive subNavBarLeft">Week View</a>
		
		
		<a id="navCourseDiscussion" domain="l10n" class="navCourseNone inactive subNavBarRight subNavCourseNonePos">Discussion</a>
		<a id="navCourseResource" domain="l10n" class="navCourseNone inactive subNavBarMiddle">Resource</a>
		<a id="navCourseAssignment" domain="l10n" class="navCourseNone inactive subNavBarMiddle">Assignment</a>
		<a id="navCourseLecture" domain="l10n" class="navCourseNone inactive subNavBarMiddle">Slides</a>
		<a id="navCourseStudent" domain="l10n" class="navCourseNone inactive subNavBarMiddle">Students</a>
		<a id="navCourseIntro" domain="l10n" class="navCourseNone inactive navCourseStranger subNavBarMiddle">Info</a>
		<a id="navCourseNews" domain="l10n" class="navCourseNone inactive subNavBarLeft">News</a>
		
		
		<a id="navScheduleInfo" domain="l10n" class="navSchedule inactive subNavBarSingle subNavSchedulePos">Agenda</a>
		
		<a id="navFriendHomepage" domain="l10n" class="navFriendNone inactive navFriendStranger subNavBarRight subNavFriendNonePos">Info</a>
		<a id="navFriendNews" domain="l10n" class="navFriendNone inactive subNavBarLeft">News</a>
	
		<a id="navFriendInfo" domain="l10n" class="navFriend inactive subNavBarSingle subNavFriendPos">Friends</a>
		
		<a id="navMsgSentMail" domain="l10n" class="navMsg inactive subNavBarRight subNavMsgPos">SentMail</a>
		<a id="navMsgInbox" domain="l10n" class="navMsg inactive subNavBarMiddle">Inbox</a>
		<a id="navMsgSend" domain="l10n" class="navMsg inactive subNavBarLeft">Compose</a>
		</div>
	</div>
	  <!--<br/>
	  <br/>-->
	  	<!-- R2:此为主内容区域 -->
	<div id="contentPane">
		
	</div>
	<div id="bottomPane">
		<div id="announcementBar">
			<div id="centerWrapper">
				<div id="announcementItems">
					<a id="copyright" >Copyright &copy; LocalsNake Net League</a>
					<!--<span>|</span>
					<a id="privacy" >Privacy Policy</a>-->
				</div>
				<div id="blank">
					&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
					&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
					&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
					&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
				</div>
				<div id="contactItems">
					<!--<a id="map" >Map</a>
					<span>|</span>-->
					<span id="suggestion" style="display: none;">
						<a domain="l10n" href="suggestion.html" target="_blank">Suggest</a>
						<span>|</span>
					</span>
					<a id="about" domain="l10n" href="about.html"  target="_blank">About</a>
					<span>|</span>
					<!--<a id="contact" domain="l10n" href="contact.html" target="_blank">Contact</a>
					<span>|</span>-->
					<a id="languageChange">English</a>
				</div>
			</div>
		</div>
		<div id="recordNum">		
			<label>Designed For <a href="http://www.google.cn/chrome/intl/zh-CN/landing_chrome.html" target="_blank">Chrome</a> / <a href="http://www.firefox.com.cn/download/" target="_blank">FireFox</a>. IE sucks.</label>
		</div>
	</div>
</div>
</div>
</body>
</html>

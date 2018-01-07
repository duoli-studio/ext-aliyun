<!doctype html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="author" content="Tencent-TGideas">
    <meta name="Copyright" content="Tencent">
    <title>符文查询</title>
    <link rel="stylesheet" href="http://cdn.tgp.qq.com/pallas/css/common/common.css?t=20151204204145" />
    <script>
	(function(){
		//if(document.documentElement.clientHeight > 600){
		//IE9+支持addEventListener和DOMContentLoaded.
		if(window.addEventListener){
			window.addEventListener("DOMContentLoaded", insertNewCss, false);
		}else{
			window.attachEvent("onload", insertNewCss);
		}
		//}

		function insertNewCss(){
			var newCss = document.createElement("link");
			newCss.rel = "stylesheet";
			newCss.href = "http://cdn.tgp.qq.com/pallas/css/common/large.css?t=20151009";
			document.getElementsByTagName("head")[0].appendChild(newCss);
		}
	})();
    </script>
    <link rel="stylesheet" href="/3rd/tgp/pls.overview.css?201410291505&t=20151103103628" />
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="http://cdn.tgp.qq.com/pallas/css/common/filter.css?t=20150605164126" />
    <![endif]-->
    <link rel="stylesheet" href="/3rd/tgp/popup.css?t=20140823" />
    <script type="text/javascript" src="/3rd/tgp/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/3rd/tgp/common.js?t=20150925171425"></script>
    <script src="/3rd/tgp/pls.runes.20140702.js?t=20150521105638"></script>
    <script src="http://cdn.tgp.qq.com/pallas/conf/LOLGameArea.js?t=20140829"></script>
    <script src="http://cdn.tgp.qq.com/pallas/conf/LOLKeywords.js?t=20150925171424"></script>
    <script src="http://cdn.tgp.qq.com/pallas/conf/LOLRunes.js?t=20140819"></script>
    <script src="http://cdn.tgp.qq.com/pallas/conf/LOLChampion.js?t=20151201120326"></script>
    <script type="text/javascript">
	var qf = GetQueryString("qf");
	var unid = GetQueryString("unid");
	function GetQueryString(name)
	{
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if(r!=null)return  unescape(r[2]); return null;
	}
	!function(){
		PLS.globStatus = {
			"vAID":qf,
			"vUIN":unid,
			"vUKEY":"",
			"vANAME":"",
			"vUName":"",
			"myAID":0,
			"myUIN":0,
			"myKEY":"",
			"myANAME":"",
			"isSelf":false,
			"isFollow":0,
			"refreshTime" : 0,											//刷新时间
			"tierList":PLS.LOLKeywords.tier,
			"tierlevel":PLS.LOLKeywords.level,
			"tierTime":PLS.LOLKeywords.tierTime,
			"gTypeList":PLS.LOLKeywords.type,
			"search":""
		};
	}();
    </script>
    <style type="text/css">
        .hero-list{
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-arrow-color: #7DB3CD;
            scrollbar-face-color: #083959;
            scrollbar-darkshadow-color: #083959;
            scrollbar-highlight-color: #083959;
            scrollbar-3dlight-color: #0A4A74;
            scrollbar-shadow-color: #083959;
            scrollbar-track-color: #1B242E;
        }
    </style>
</head>
<body>
<div id="historyArea" class="hero-area" >
    <!-- filter-result -->
    <div style="display: none;" id="rateInfo"></div>
    <div class="game-select" style="margin-left:5px;">
        <ul id="switchTab">
            <li><span class="on" attr="runes" style="width:108px;">排位赛<font color="red">使用过的</font>英雄</span></li>
        </ul>
    </div>
    <!-- filter-result end -->
    <div tag="content">
        <div class="hero-list"  style="height: 600px;">
            <ul class="game-list" >
                <!-- 由 模板填充 -->
            </ul>
        </div>
    </div>

    <!-- no hero area end -->
</div>
<div class="main-cont" style="width: 600px">
    <div class="game-select sub-nav" style="margin-left:11px;">
        <ul id="switchTab">
            <li><span class="on" attr="runes">战绩</span></li>
        </ul>
    </div>
    <!-- record-detail -->
    <div id="statArea" class="content-box record-detail" style="height:169px;margin-left:80px;margin-top:0px;">
        <div class="title-row">
            <div class="table-td1" style="height:27px;">类型</div>
            <div class="table-td2" style="height:27px;">总场次数</div>
            <div class="table-td3" style="height:27px;">胜率</div>
            <div class="table-td4" style="height:27px;">胜场</div>
            <div class="table-td5" style="height:27px;">负场</div>
            <div class="table-td6" style="height:27px;">段位</div>
            <div class="table-td7" style="height:27px;">胜点</div>
        </div>
        <ul class="record-table">    <li class="record-row0">   <div class="table-td1">    经典对战       </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7">--</div>  </li>    <li class="record-row1">   <div class="table-td1">    极地大乱斗       </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7">--</div>  </li>    <li class="record-row2">   <div class="table-td1">    S7季前赛              </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7"></div>  </li>    <li class="record-row3" style="display: block;">   <div class="table-td1">    S6排位赛       </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7"></div>  </li>   </ul>
    </div>
    <!-- record-detail end -->
    <div class="game-select sub-nav">
        <ul id="switchTab">
            <li><span class="on" attr="runes">符文</span></li>
        </ul>
    </div>
    <div id="runesArea">
        <!-- runes-page -->
        <div class="runes-page">
            <ul id="runesTag">
            </ul>
        </div>
        <!-- runes-page -->

        <!-- runes-area -->
        <div class="content-box runes-area">
            <div class="title">
                <h3 id="runesTitle"></h3>
                <p class="intro">总价：<span class="gold" id="tGold"></span>金币</p>
            </div>

            <div class="content" id="runesList">
            </div>
        </div>
        <!-- runes-area end -->

        <!-- status-area -->
        <div class="content-box status-area">
            <div class="title">
                <h3>状态</h3>
            </div>
            <div class="content">
                <ul class="status-list" id="totalStatus">
                </ul>
            </div>
        </div>
        <!-- status-area end -->
    </div>
</div>
</body>
<script type="text/x-template" id="statTmpl">
<% for(var i = 0, l = list.length;i < l; i++){
var d = list[i];
%>
<li class="record-row">
    <div class="table-td1" style="height:29px;">
<%=["经典对战", "S7单双排", "S7灵活组排", "S7季前赛", "S6排位赛"][i]%>
</div>
<div class="table-td2" style="height:29px;"><%=d.use_num%></div>
<div class="table-td3" style="height:29px;"><%=d.win_rate===0 ? '--' : d.win_rate+'%' %></div>
<div class="table-td4" style="height:29px;"><%=d.win_num%></div>
<div class="table-td5" style="height:29px;"><%=d.lose_num%></div>
<div class="table-td6" style="height:29px;">
<%
    var s = '--';
    if(typeof d.tier === "number" && d.tier !== 255){
        s = '<span class="grading-img grading-img'+ d.tier +'"></span><span class="grading-name">'+ d.tier_name +'</span>';
    }
%>
<%=s%>
</div>
<div class="table-td7" style="height:29px;"><%=d.win_point_txt%></div>
</li>
<% } %>
</script>
<script type="text/x-template" id="usedHeroIconsTmpl">
<% for(var i = 0,l = list.length; i < l; i++){
var hero = list[i];
%>
<li data-hid="<%=hero.champion_id%>"><a href="#" hidefocus><span class="hero-img icon-<%=hero.champion_id%>" efuin="<%=hero.uin%>" efaid="<%=hero.aid%>" efhid="<%=hero.champion_id%>" eftype="0"></span><span class="img-mask" efuin="<%=hero.uin%>" efaid="<%=hero.aid%>" efhid="<%=hero.champion_id%>" eftype="0"></span></a></li>
<% } %>
</script>

<script type="text/x-template" id="heroTipsTmpl">
<div class="graph-tips" style="left:<%= left %>px; top:<%= top %>px;">
<h3><%= title %></h3>
<p>使用：<%= use %>次<br>胜率：<%= winRate %><br>熟练度：<%= expValue %></p>
<div class="arrow"></div>
</div>
</script>

<script type="text/x-template" id="heroTipsTmpl1">
<div class="graph-tips graph-tips2" style="left:<%= left %>px; top:<%= top %>px;">
<p>胜场补充分：<span class="supplement"><%= deltaExp %></span></p>
<span class="arrow"></span>
</div>
</script>

<script type="text/x-template" id="heroTipsTmpl2">
<div class="qualifying-tips qualifying-tips2" style=" display:block;left:<%= left %>px;top:<%= top %>px;">
<p class="date"><%= dateTime %><b class="qualifying-icon"></b></p>
<p>熟练度：<%= expValue %></p>
<div class="arrow"></div>
<div class="arrow-in"></div>
</div>
</script>

<script type="text/x-template" id="gameItemTpl">
<li>
<span class="hero-img icon-<%=hid%>" efhid="<%=hid%>" eftype="0"><%=mvp%></span>
<b class="arrow"></b>
<div class="mark-area">
    <b class="img-icon" style="display:none;"></b>
    <b class="marking friend-mark" style="display:none;"></b>
</div>
</li>
</script>

<script type="text/x-template" id="historyItemTmpl">
<li>
<span class="hero-detail" style="width:120px;height:40px;">
    <img width="35px" height="35px" src="<%=imgurl%>" style="float:left;margin:0px 5px 0 9px;"/>
    <span class="hero-title"><span style="font-size:14px"><%=rolename%></span></span>
</span>
</li>
</script>
<script src="/3rd/tgp/tgpInfo.js?t=20160906000002"></script>
<script>
PLS.runes.main();
(function(){
PLS.overview.init();
})();
</script>
</html>

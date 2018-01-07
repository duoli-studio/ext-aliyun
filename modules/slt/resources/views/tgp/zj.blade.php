
<!doctype html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="author" content="Tencent-TGideas">
    <meta name="Copyright" content="Tencent">
    <title>战绩查询</title>
    <link rel="stylesheet" href="http://cdn.tgp.qq.com/pallas/css/common/common.css?t=20151204204145" />
    <script>
	function errorFun(){
		return true;
	}
	window.onerror = errorFun;
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
    <link rel="stylesheet" href="/3rd/tgp/tgpzj.css?t=20140824" />
    <script type="text/javascript" src="/3rd/tgp/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/3rd/tgp/common.js?t=20150925171428"></script>
    <script src="http://cdn.tgp.qq.com/pallas/conf/LOLKeywords.js?20141217&t=20160102105850"></script>
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
	function scjt(){
		$("#sczjjt").attr("disabled",true);
		$("#sczjjt").val("上传中..........");
		var orderid = setTimeout(
			function(){
				try{
					window.external.UploadRecordScreenshot();
				}catch (e) {
					alert("请使用最新版客户端");
				}
				$("#sczjjt").attr("disabled",false);
				$("#sczjjt").val("上传战绩截图");
			},200);
	}
    </script>
</head>
<body>
<span style="position: absolute;top: 10px;left: 600px;z-index: 99999;"><input type="button" class="button blue" value="上传战绩截图" onclick="scjt()" id="sczjjt"/></span>
<div class="main-cont" style="width: 600px">
    <!-- record-detail -->
    <div id="statArea" class="content-box record-detail" style="height:127px;margin-left:150px;margin-top:0px;">
        <div class="title-row">
            <div class="table-td1">类型</div>
            <div class="table-td2">总场次数</div>
            <div class="table-td3">胜率</div>
            <div class="table-td4">胜场</div>
            <div class="table-td5">负场</div>
            <div class="table-td6">段位</div>
            <div class="table-td7">胜点</div>
        </div>
        <ul class="record-table">    <li class="record-row0">   <div class="table-td1">    经典对战       </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7">--</div>  </li>        <li class="record-row2">   <div class="table-td1">    S7季前赛              </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7"></div>  </li>    <li class="record-row3" style="display: block;">   <div class="table-td1">    S6排位赛       </div>   <div class="table-td2">0</div>   <div class="table-td3">--</div>   <div class="table-td4">0</div>   <div class="table-td5">0</div>   <div class="table-td6">        --   </div>   <div class="table-td7"></div>  </li>   </ul>
    </div>
    <br/>
    <div id="historyArea" class="hero-area" style="height:500px">
        <!-- debut-cont -->
        <div class="debut-cont" id="debutArea" >
            <p class="debut-current"><span tag="total" id="curFilter">全部战绩</span><span class="arrow-icon"></span></p>
            <div class="drop-down" id="debutDrop" style="right: -90px">
                <a class="close-btn" href="javascript:;" hidefocus></a>
                <h3 class="first-title">筛选战绩</h3>
                <a class="result-btn" href="#h" hidefocus data-title="全部战绩" efhid="0" eftype="0"><span data-title="全部战绩" efhid="0" eftype="0">全部战绩</span><b class="btn-side"></b></a>
                <h3>按比赛类型</h3>
                <a class="result-btn qualifying-btn fl" href="#h" hidefocus data-title="排位赛" efhid="0" eftype="4"><span>排位赛</span><b class="btn-side"></b></a>
                <a class="result-btn matching-btn fl" href="#h" hidefocus data-title="匹配赛" efhid="0" eftype="1"><span>匹配赛</span><b class="btn-side"></b></a>
                <a class="result-btn smash-btn fl" href="#h" hidefocus data-title="大乱斗" efhid="0" eftype="6"><span>大乱斗</span><b class="btn-side"></b></a>
            </div>
        </div>
        <!-- debut-cont end -->

        <!-- filter-result -->
        <div class="filter-result" type="0" hid="0" id="rateInfo">
            <p class="smart">智能筛选战绩</p>

            <div class="exclamatory-point">
                <a class="exclamation" tag="filterTips" href="#h"></a>
                <div class="debut-tips" style="display: none;">
                    <p>可根据常用英雄和比赛类型对最近500场比赛进行筛选</p>
                    <div class="arrow"></div>
                </div>
            </div>
        </div>
        <!-- filter-result end -->

        <!-- hero-list -->
        <div tag="content" style="display:none;">
            <div class="hero-list">
                <ul class="game-list">
                    <!-- 由 模板填充 -->
                </ul>
            </div>
        </div>
        <!-- hero-list end -->

        <!-- no hero area -->
        <div class="no-heros-area" id="noContent" style="display:none;">
            <p>最近没有相关战绩<br>请重新筛选</p>
        </div>
        <!-- no hero area end -->

        <div class="page-turn"><a tag="prev" class="prev-gray" href="#" hidefocus></a><input type="text" /><span class="page-num"></span><a class="go-btn" href="#" hidefocus>GO</a><a tag="next" class="next-gray" href="#" hidefocus></a></div>
    </div>

    <div class="history-area">
        <div class="history-intro">
            <ul id="overViewList">
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" hidefocus>
                    </a>
                </li>
            </ul>
            <div id="banBtn" class="forbid-area" style="visibility:hidden">
                <a class="forbid-text" href="javascript:;" hidefocus="">禁用<br>英雄</a>
                <div class="forbid-list">
                    <ul id="banList">
                        <li><span id="banListItem1" class="hero-img icon-none" href="#" hidefocus=""></span></li>
                        <li><span id="banListItem3" class="hero-img icon-none" href="#" hidefocus=""></span></li>
                        <li><span id="banListItem5" class="hero-img icon-none" href="#" hidefocus=""></span></li>
                        <li><span id="banListItem2" class="hero-img icon-none" href="#" hidefocus=""></span></li>
                        <li><span id="banListItem4" class="hero-img icon-none" href="#" hidefocus=""></span></li>
                        <li><span id="banListItem6" class="hero-img icon-none" href="#" hidefocus=""></span></li>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

        <!-- history-cont -->
        <div class="history-title" id="playListHeader">
            <p><span class="cubes"></span>胜利队伍</p>
            <p><span class="cubes cubes2"></span>战败队伍</p>
            <p class="appraise">评价</p>
            <p class="money">金钱</p>
            <p class="kill">杀/死/助</p>
            <p class="equip">出装</p>
        </div>
        <div class="history-cont" id="gameDetailList">
            <ul>
                <li style="margin-top: 30px">&nbsp;&nbsp;&nbsp;&nbsp;<b style="font-size: 20px;line-height: 40px">非常抱歉，暂不能提供战绩明细查询</b></li>
            </ul>
            <ul>

            </ul>
        </div>
        <!-- history-cont end -->
    </div>
</div>
</body>
<script type="text/x-template" id="gameItemTpl">
<li>
    <a href="#h" hidefocus data-hid="{#hid#}" data-gameid="{#gameId#}">
        {#extFlagHtml#}
        <span class="hero-img icon-{#hid#}" efhid="{#hid#}" eftype="0">{#mvp#}</span>
        <span class="hero-detail">
				<span class="hero-title">{#desc#}<span class="{#typeClass#}">（{#gameType#}）</span></span>
				<span class="date">{#date#}</span>
			</span>
        <b class="arrow"></b>
        <div class="mark-area">
            <b class="img-icon" style="display:none;"></b>
            <b class="marking friend-mark" style="display:none;"></b>
            <b class="defeat-mark" style="{#defeat#}"></b>
        </div>
    </a>
</li>
</script>
<script type="text/x-template" id="historyItemTmpl">
<li data-gameid="<%=gameId%>">
            <%=judgementHtml%>
            <span class="hero-img icon-<%=hid%>" efhid="<%=hid%>" eftype="0"><%=mvp%></span>
			<span class="hero-detail">
				<span class="hero-title"><%=desc%><span class="<%=typeClass%>">（<%=gameType%>）</span></span>
				<span class="date"><%=date%></span>
			</span>
			<div class="mark-area">
				<b class="img-icon" style="display: none;"></b>
				<b class="marking friend-mark" style="display:none;"></b>
                <b class="defeat-mark" style="<%=defeat%>"></b>
			</div>
	</li>
</script>

<script type="text/x-template" id="statTmpl">
	<% for(var i = 0, l = list.length;i < l; i++){
		var d = list[i];
	%>
	<li class="record-row<%=i%>">
		<div class="table-td1">
			<%=["经典对战", "S7单双排", "S7灵活组排", "S7季前赛", "S6排位赛"][i]%>
		</div>
		<div class="table-td2"><%=d.use_num%></div>
		<div class="table-td3"><%=d.win_rate===0 ? '--' : d.win_rate+'%' %></div>
		<div class="table-td4"><%=d.win_num%></div>
		<div class="table-td5"><%=d.lose_num%></div>
		<div class="table-td6">
			<%
				var s = '--';
				if(typeof d.tier === "number" && d.tier !== 255){
					s = '<span class="grading-img grading-img'+ d.tier +'"></span><span class="grading-name">'+ d.tier_name +'</span>';
				}
			%>
			<%=s%>
		</div>
		<div class="table-td7"><%=d.win_point_txt%></div>
	</li>
	<% } %>
</script>
<script src="http://cdn.tgp.qq.com/lib/svg/raphael-min.js"></script>
<script src="http://cdn.tgp.qq.com/lib/svg/morris.min.2014060.js?20140709"></script>
<script src="http://cdn.tgp.qq.com/pallas/js/profile/pls.b1b.20140623.js?t=20151221185525"></script>
<script src="http://cdn.tgp.qq.com/pallas/conf/LOLChampion.js?t=20151201120326"></script>
<script src="/3rd/tgp/tgpInfo_zj.js?t=20160503171421"></script>
<script type="text/javascript" >
$(function(){
	PLS.overview.init();
	//PLS.record.main();
});
</script>
</html>

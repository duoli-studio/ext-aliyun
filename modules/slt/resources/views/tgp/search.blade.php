<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <title>YDL_TGP</title>
    <link rel="stylesheet" href="/3rd/tgp_search/common.css" />
    <script>
        (function () {
            //if(document.documentElement.clientHeight > 600){
            //IE9+支持addEventListener和DOMContentLoaded.
            if (window.addEventListener) {
                window.addEventListener("DOMContentLoaded", insertNewCss, false);
            } else {
                window.attachEvent("onload", insertNewCss);
            }
            //}

            function insertNewCss() {
                var newCss = document.createElement("link");
                newCss.rel = "stylesheet";
                newCss.href = "/3rd/tgp_search/large.css?t=20170318";
                document.getElementsByTagName("head")[0].appendChild(newCss);
            }
        })();
    </script>
    <link rel="stylesheet" href="/3rd/tgp_search/pls.overview.css?201410291505&t=20150916144110" />
    <!--[if lte IE 8]>
    <link rel="stylesheet" href="/3rd/tgp_search/filter.css" />
    <![endif]-->
    <link rel="stylesheet" href="/3rd/tgp_search/popup.css?t=20140823" />
    <link rel="stylesheet" href="/3rd/tgp_search/common2.css?t=20170318" />
    <script type="text/javascript" src="/3rd/tgp_search/jquery-1.7.2.min.js?t=20151019180422"></script>
    <script type="text/javascript" src="/3rd/tgp_search/common.js?t=20151019180422"></script>
    <!--<script src="lib/pls.runes.20140702.js?t=20151019180422"></script>-->
    <script src="/3rd/tgp_search/zj.js?t=20161214"></script>
    <script src="/3rd/tgp_search/runes.js?t=20170318"></script>
    <script src="/3rd/tgp_search/hero.js?t=20170318"></script>
    <script src="/3rd/tgp_search/pls.history.20140924.js?t=20170318"></script>
    <script src="/3rd/tgp_search/LOLGameArea.js?t=20140829"></script>
    <script src="/3rd/tgp_search/LOLKeywords.js?t=20160804"></script>
    <script src="/3rd/tgp_search/lolutils.js?t=20161130"></script>
    <script src="http://cdn.tgp.qq.com/pallas/conf/LOLWhiteList.js?t=20151204"></script>
    <link rel="stylesheet" href="/3rd/tgp_search/showLoading.css" />
    <script src="/3rd/tgp_search/jquery.showLoading.min.js"></script>

    <script type="text/javascript">
        !function () {
            PLS.globStatus = {
                "vAID": 0,
                "vUIN": "",
                "vUKEY": "",
                "vANAME": "",
                "vUName": "",
                "myAID": 0,
                "myUIN": 0,
                "myKEY": "",
                "myANAME": "",
                "isSelf": false,
                "isFollow": 0,
                "refreshTime": 0,											//刷新时间
                "tierList": PLS.LOLKeywords.tier,
                "tierlevel": PLS.LOLKeywords.level,
                "tierTime": PLS.LOLKeywords.tierTime,
                "gTypeList": PLS.LOLKeywords.type,
                "search": "",
                "PKey": ""
            };
        }();
    </script>

    <style>
        .gp {
            width: 100px;
            height: 100px;
        }

        .screenshot-list .inl {
            display: inline;
        }
        /*战绩总计列表的第四行S4赛季默认隐藏
        .record-row3{display: block;}
        */
        .graph-area {
            overflow: hidden;
            cursor: pointer;
        }

        .blank-cover {
            visibility: hidden;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.9;
            z-index: 200;
            background-color: #1B1D1B;
        }

        .blank-cover .ct {
            font-size: 14px;
            font-family: "宋体";
            color: #6b91ad;
            font-weight: bold;
            text-align: center;
        }

        /*历史战绩的无数据展示*/
        .no-data {
            display: none;
            background-color: #161b21;
            line-height: 18px;
            text-align: center;
        }

        #historyArea .no-data {
            height: 400px;
            clear: both;
        }

        .history-no-data {
            position: relative;
            top: 136px;
        }

        /*#usedHerosArea .no-data{margin-top:40px;height:18px;background: url(http://cdn.tgp.qq.com/pallas/images/pallas/warn-min.png) no-repeat 50px 1px;*background-position-y:-1px;}*/
        .no-data-1 {
            margin-top: 3em;
            line-height: 18px;
            background: url(http://cdn.tgp.qq.com/pallas/images/pallas/warn-min.png) no-repeat;
            position: relative;
            left: 50px;
            padding-left: 22px;
        }

        .inner {
            margin-top: 40px;
            height: 18px;
            background: url(http://cdn.tgp.qq.com/pallas/images/pallas/warn-min.png) no-repeat 50px 1px;
            *background-position-y: -1px;
        }
    </style>
</head>

<body>
<div style="position:absolute; overflow:auto">
    <div class="main-cont">
        <div class="game-select sub-nav">
            <ul id="switchTab2">
                <li><span class="on">战绩总览</span></li>
            </ul>
        </div>
        <!-- record-detail -->
        <div class="mod-box my-score">
            <div class="bd">
                <table class="mod-table score-table score-table-a">
                    <thead>
                    <tr>
                        <th>类型</th>
                        <th>总数场次</th>
                        <th>胜率</th>
                        <th>胜场</th>
                        <th>负场</th>
                        <th>段位</th>
                        <th>胜点</th>
                    </tr>
                    </thead>
                    <tbody id="battleStatArea">
                    <!-- battleStatListTmpl -->
                    <!-- 下边纯粹是为了初始化先占用地方，取消的话会闪 -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <!-- 隐藏的当前赛季的前一赛季 -->
                <div id="s00battleStatArea" class="drop-table-wp">
                    <table class="mod-table score-table">
                        <tbody>
                        <tr></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- record-detail end -->
        <div class="add_test_img" style="float:left;"></div>
        <div class="wrapper">

            <div class="game-select sub-nav">
                <ul id="switchTab">
                    <li><span class="on" attr="history">历史战绩</span></li>
                    <li><span attr="runes">符文</span></li>
                </ul>
            </div>
            <div id="historyArea">
                <!-- hero-area -->
                <div class="hero-area hero-area3" id="gameListArea" style="width:142px;">
                    <!-- debut-cont -->
                    <div class="debut-cont" id="debutArea" style="width:142px;display:none;">
                        <p class="debut-current"><span tag="total" id="curFilter">全部战绩</span><span class="arrow-icon"></span></p>
                        <div class="drop-down" id="debutDrop">
                            <a class="close-btn" href="javascript:;" hidefocus></a>
                            <h3 class="first-title">筛选战绩</h3>
                            <a class="result-btn" href="#h" hidefocus data-title="全部战绩" efhid="0" eftype="0"><span data-title="全部战绩" efhid="0" eftype="0">全部战绩</span><b class="btn-side"></b></a>
                            <a class="result-btn" href="javascript:;" hidefocus data-title="MVP对局" efhid="0" eftype="1001">M V P 对 局<b class="btn-side"></b></a>
                            <h3>按比赛类型</h3>
                            <a class="result-btn qualifying-btn fl" href="#h" hidefocus data-title="排位赛" efhid="0" eftype="4"><span>排位赛</span><b class="btn-side"></b></a>
                            <a class="result-btn matching-btn fl" href="#h" hidefocus data-title="匹配赛" efhid="0" eftype="1"><span>匹配赛</span><b class="btn-side"></b></a>
                            <a class="result-btn smash-btn fl" href="#h" hidefocus data-title="大乱斗" efhid="0" eftype="6"><span>大乱斗</span><b class="btn-side"></b></a>
                        </div>
                    </div>
                    <!-- debut-cont end -->
                    <!-- filter-result -->
                    <div class="filter-result" type="0" hid="0" id="rateInfo" style="width:142px;display:none;">
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
                    <div class="hero-list" id="listContent" style="width:142px;">
                        <ul id="gameList"></ul>
                    </div>
                    <!-- hero-list end -->
                    <!-- no hero area -->
                    <div class="no-heros-area" id="noContent" style="display:none;">
                        <p>最近没有相关战绩<br>请重新筛选</p>
                    </div>
                    <!-- no hero area end -->

                    <div id="pageBar"><div class="page-turn"><a class="prev" tag="prev" href="#h" hidefocus></a><input type="text" /><span class="page-num"></span><a class="go-btn" href="#h" hidefocus>GO</a><a class="next" tag="next" href="#h" hidefocus></a></div></div>
                </div>
                <!-- hero-area -->
                <!-- history-area -->
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

                    <div class="history-img" id="honorImg">
                        <img class="default-img" src="http://cdn.tgp.qq.com/pallas/images/pallas/default_img2.jpg" width="66" height="38" alt="" />
                    </div>
                    <div class="analysis-area" id="battleInfo" style="display:none">
                        <a href="#h" hidefocus>
                            <i class="analysis-icon"></i>
                            <p class="num" id="honorNum"></p>
                            <h4>对局分析</h4>
                        </a>
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
                            <li>&nbsp;&nbsp;&nbsp;&nbsp;<b style="font-size: 14px;line-height: 40px">对不起，易代练暂不提供战绩明细查询</b></li>
                        </ul>
                        <ul></ul>
                    </div>
                    <!-- history-cont end -->
                </div>
                <!-- history-area end -->
            </div>
            <div id="runesArea" style="display:none">
                <!-- runes-page -->
                <div class="runes-page">
                    <ul id="runesTag"></ul>
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
                        <ul class="status-list" id="totalStatus"></ul>
                    </div>
                </div>
                <!-- status-area end -->
            </div>
        </div>

    </div>

    <div id="historyArea4" style="float: left;">
        <div class="game-select" style="margin-left:30px;">
            <ul id="switchTab3">
                <li><span class="on" attr="runes" style="width:108px;">排位赛<font color="red">使用过的</font>英雄</span></li>
            </ul>

        </div>

        <!-- hero-list -->
        <div class="hero-area2" id="gameList_hero">
            <ul id="gameList_hero"></ul>
        </div>
        <!-- hero-list end -->

    </div>

</div>
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
                <span class="hero-img icon-<%=hid%>" efhid="<%=hid%>" eftype="0"></span>
                <span class="hero-detail">
                    <span class="hero-title"><span class="<%=typeClass%>"><%=rolename%></span></span>
                </span>
            </li>
        </script>
        <!-- 我的战报 -->
        <script type="text/x-template" id="battleStatListTmpl">
            <% for(var i = 0, l = list.length;i < l; i++){
            var item = list[i];
            %>
            <tr data-index="<%=i%>">
                <td><%=item.battle_type_name%></td>
                <td><%=item.use_num%></td>
                <td><%=item.win_rate === 0 ? '--' : item.win_rate+'%' %></td>
                <td><%=item.win_num%></td>
                <td><%=item.lose_num%></td>
                <td><%=item.tier_name%></td>
                <td><%=item.win_point_txt%></td>
            </tr>
            <% } %>
        </script>

        <script>
            var unid = getUrlParam('unid');
            var qf = getUrlParam('qf');
            if (unid != null) {
                $(".add_test_img").showLoading();

                $.ajax({
                    type: "post",
                    dataType: "text",
                    url: "/tgp/search",
                    data: {
                        "_token": '{!! csrf_token() !!}',
                        "unid": unid,
                        "qf": qf
                    },
                    complete: function () { $(".add_test_img").hideLoading(); },
                    success: function (data) {
                        var core = null, runes = null, hero = null, qquin = null, areaid = null;
                        var arr = data.split("|-|");
                        if (arr != null) {
                            if (arr.length > 0) {
                                core = arr[0];
                            }
                            if (arr.length > 1) {
                                runes = arr[1];
                                hero = arr[2];
                                qquin = arr[3];
                                areaid = arr[4];
                            }

                            if (runes == null) {
                                alert(core);
                            }
                            else {

                                PLS.zj.main(eval('(' + core + ')'));
                                PLS.runes.main(eval('(' + runes + ')'));
                                if (hero != "") {
                                    PLS.record.showGameList_hero(eval('(' + hero + ')'));
                                }
                                PLS.globStatus["vAID"] = areaid;
                                PLS.globStatus["vUIN"] = qquin;
                                PLS.globStatus["vUKEY"] = qquin;
                                //PLS.globStatus["PKey"] = pkey;

                                PLS.record.main();

                            }
                        }
                    }
                });
            }
            function getUrlParam(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                if (r != null)
                    return unescape(r[2]);
                return null;
            }
            function getCookie(name) {
                var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
                if (arr = document.cookie.match(reg))
                    return unescape(arr[2]);
                else
                    return null;
            }
        </script>
        <script src="/3rd/tgp_search/raphael-min.js"></script>
        <script src="/3rd/tgp_search/morris.min.2014060.js?t=20140709"></script>
        <script src="/3rd/tgp_search/LOLChampion.js?t=20150716154827?t=20150716154827"></script>
        <script src="/3rd/tgp_search/pls.b1b.20140623.js?t=20140623"></script>
        <script src="/3rd/tgp_search/LOLRunes.js?t=20140819"></script>
</body>
</html>
@extends('lemon.template.shopnc')
@section('desktop-main')
	<div class="doc-container">
		@include('lemon.shopnc.header')
		<div class="doc-header">
			组件 <span class="intro">系统自定义组件</span>
		</div>
		<h3 class="doc-title">Tab组件</h3>
		<div class="mt10 clearfix">
			<div class="span-24">
				<div class="doc-emmet">
					[生成tab样式链接]
					ul.tab-base>(li>a.current[href=#]>span{tab1})+(li>a[href=#]>span{tab2})
  			</div>
				<div class="doc-jsGen clearfix">
					<ul class="tab-base">
						<li><a href="#" class="current"><span>tab1</span></a></li>
						<li><a href="#"><span>tab2</span></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="mt10 clearfix">
			<div class="span-24 last">
				<div class="doc-emmet">
					[生成tab] <br>
					div.clearfix>(ul.tab-base.J_tab[data-relation=rel_key]>(li>a.current[href=#]>span{tab1})+(li>a[href=#]>span{tab2}))<br>
					[生成tab-content] <br>
					div.clearfix>(div[data-rel=rel_key]{tab1-content})+(div[data-rel=rel_key]{tab2-content})
				</div>
				<div class="doc-jsGen clearfix">
					<div class="clearfix">
						<ul class="tab-base J_tab" data-relation="rel_key">
							<li><a href="#" class="current"><span>tab1</span></a></li>
							<li><a href="#"><span>tab2</span></a></li>
						</ul>
					</div>
					<div class="clearfix">
						<div data-rel="rel_key">tab1-content</div>
						<div data-rel="rel_key">tab2-content</div>
					</div>
				</div>
			</div>
		</div>
		<h3>面包屑</h3>
		<div class="mt10 clearfix">
			<div class="span-24">
				<div class="doc-emmet">
					div.location>strong{您的位置:}+div#J_crumbs.crumbs>span{扩展功能}+span.arrow{&nbsp;}+span{扩展功能}
				</div>
				<div class="doc-jsGen clearfix">
					<div class="location" style="position: static"><strong>您的位置:</strong>
						<div id="J_crumbs" class="crumbs"><span>扩展功能</span><span class="arrow">&nbsp;</span><span>扩展功能</span></div>
					</div>
				</div>
			</div>
		</div>
		<h3>分页</h3>
		<div class="mt10 clearfix">
			<div class="span-24">
				<div class="doc-emmet">ul.pagination>(li.disabled>span{«})+(li.active>span{1})+(li*7>a)+(li.disabled>span{...})+(li>a*2)+(li>a[rel=next]{»})</div>
				<div class="doc-jsGen clearfix">
					<ul class="pagination">
						<li class="disabled"><span>«</span></li>
						<li class="active"><span>1</span></li>
						<li><a href="">2</a></li>
						<li><a href="">3</a></li>
						<li><a href="">4</a></li>
						<li><a href="">5</a></li>
						<li><a href="">6</a></li>
						<li><a href="">7</a></li>
						<li><a href="">8</a></li>
						<li class="disabled"><span>...</span></li>
						<li><a href="">15</a></li>
						<li><a href="">16</a></li>
						<li><a href="" rel="next">»</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

@endsection
@extends('lemon.template.shopnc')
@section('desktop-main')
	<div class="doc-container">
		@include('lemon.shopnc.header')
        <div class="doc-header">
            Font Awesome 图标
        </div>
		<h3 class="doc-title">字体颜色</h3>
		<div class="mt10 clearfix">
			<div class="span-6">
				<div class="doc-emmet">span.green{绿色}</div>
				<div class="doc-jsGen">
					<span class="green">绿色</span>
				</div>
			</div>
			<div class="span-6">
				<div class="doc-emmet">span.red{红色}</div>
				<div class="doc-jsGen">
					<span class="red">红色</span>
				</div>
			</div>

			<div class="span-6">
				<div class="doc-emmet">span.blue{蓝色}</div>
				<div class="doc-jsGen">
					<span class="blue">蓝色</span>
				</div>
			</div>
			<div class="span-6 last">
				<div class="doc-emmet">span.grey{灰色}</div>
				<div class="doc-jsGen">
					<span class="grey">灰色</span>

				</div>
			</div>

		</div>
		<h3 class="doc-title">常用图标</h3>
		<div class="mt10 clearfix">

			<div class="span-6">
				<div class="doc-emmet">i.fa.fa-unlock.fa-lg.green</div>
				<div class="doc-jsGen">
					<i class="fa fa-unlock fa-lg green"></i>
				</div>
			</div>
			<div class="span-6">
				<div class="doc-emmet">i.fa.fa-lock.fa-lg</div>
				<div class="doc-jsGen">
					<i class="fa fa-lock fa-lg"></i>
				</div>
			</div>
			<div class="span-6">
				<div class="doc-emmet">i.fa.fa-check.fa-lg.green</div>
				<div class="doc-jsGen">
					<i class="fa fa-check fa-lg green"></i>
				</div>
			</div>
			<div class="span-6 last">
				<div class="doc-emmet">i.fa.fa-close.fa-lg.red</div>
				<div class="doc-jsGen">
					<i class="fa fa-close fa-lg red"></i>
				</div>
			</div>

		</div>
		<div class="mt10 clearfix">
			<div class="span-6 ">
				<div class="doc-emmet">i.fa.fa-edit.fa-lg.green</div>
				<div class="doc-jsGen">
					<i class="fa fa-edit fa-lg"></i>
				</div>
			</div>
			<div class="span-6">
				<div class="doc-emmet">i.fa.fa-check-square-o.fa-lg</div>
				<div class="doc-jsGen">
					<i class="fa fa-check-square-o fa-lg"></i>
				</div>
			</div>

			<div class="span-6">
				<div class="doc-emmet">i.fa.fa-search.fa-lg</div>
				<div class="doc-jsGen">
					<i class="fa fa-search fa-lg"></i>
				</div>
			</div>
			<div class="span-6 last">
				<div class="doc-emmet">i.fa.fa-plus.fa-lg.blue</div>
				<div class="doc-jsGen">
					<i class="fa fa-plus fa-lg blue"></i>
				</div>
			</div>

		</div>
		<p class="mt10"><a class="blue" target="_blank" href="http://fontawesome.dashgame.com/" >点击访问更多图标more</a></p>
	</div>
@endsection
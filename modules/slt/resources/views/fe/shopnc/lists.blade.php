@extends('lemon.template.shopnc')
@section('desktop-main')
	<div class="doc-container">

	@include('lemon.shopnc.header')
        <div class="doc-header">
            列表 <span class="intro">列表 样式</span>
        </div>
	<h3 class="doc-title">list-mix</h3>
	<div class="mt10 clearfix">
		<div class="span-8">
			<div class="doc-emmet">ul>li{title$}*4</div>
			<div class="doc-jsGen">
				<ul>
					<li>title1</li>
					<li>title2</li>
					<li>title3</li>
					<li>title4</li>
				</ul>
			</div>
		</div>
		<div class="span-8">
			<div class="doc-emmet">...</div>
			<div class="doc-jsGen">

			</div>
		</div>
		<div class="span-8 last">
			<div class="doc-emmet">...</div>
			<div class="doc-jsGen">

			</div>
		</div>
	</div>
</div>
@endsection
<div class="prd-header">
	<div class="container clearfix">
		<div class="prd-header-left pull-left clearfix">
			@if (isset($parent_id) && $parent_id)
				<a class="pull-left" href="{!! route('web:prd.show', [$parent_id]) !!}">
					{!! Html::image('project/daniu/images/prd/return.png', '返回上一页', ['width' => 20]) !!}
					返回上一页
				</a>
			@else
                @if (isset($item) && $item->type == \App\Models\PrdContent::TYPE_PUBLIC)
				<a class="pull-left" href="{!! route('web:prd.public') !!}">
					{!! Html::image('project/daniu/images/prd/return.png', '返回上一页', ['width' => 20]) !!}
					返回上一页
				</a>
                @endif
                @if (isset($item) && ($item->type == \App\Models\PrdContent::TYPE_PRIVATE || $item->type == \App\Models\PrdContent::TYPE_TEAM))
				<a class="pull-left" href="{!! route('web:prd.project', $item->cat_id) !!}">
					{!! Html::image('project/daniu/images/prd/return.png', '返回上一页', ['width' => 20]) !!}
					返回上一页
				</a>
                @endif
			@endif
			{{--
			{!! Form::model(\Input::all(), ['route' => 'web:prd.live_search', 'class' => 'pull-left']) !!}
			{!! Form::text('search', null, ['placeholder' => '搜索当前文档页面内容']) !!}
			{!! Form::image('project/daniu/images/prd/search.png', 'submit', ['title'=> '搜索']) !!}
			{!! Form::close()  !!}
			--}}
		</div>
		<div class="prd-header-right pull-right">
			<a id="J_full_screen" class="prd-header-full-screen">
				{!! Html::image('project/daniu/images/prd/full_screen.png', '全屏浏览', ['width' => 20, 'height'=>20]) !!}
				全屏浏览
			</a>
		</div>
	</div>
</div>
<script>
require(['jquery', 'lemon/util'], function ($, util) {
	$(function(){
		$('#J_full_screen').on('click', function(){
			util.full_screen();
		});
	})
})
</script>
@extends('slt::tpl.default')
@section('body-start')
	<body class="bg-grey">@endsection
	@section('tpl-main')
		@include('slt::article.header')
		<div class="container markdown prd-detail">
			<div class="prd-detail-title">
				<h3 class="clearfix">
					@if (count($level_titles))
						@foreach($level_titles as $num => $prd)
							@if ($num == 0)
								<a class="prd-title-first" href="{!! $prd->crypt_url !!}">
									{!! $prd->title !!}
								</a>
							@else
								&gt;
								<a class="prd-title-small" href="{!! $prd->crypt_url !!}">
									{!! $prd->title !!}
								</a>
							@endif
						@endforeach
						@if ($item)
							&gt;
							<small>
								{!! $item->title !!}
							</small>
						@else
							&gt;
							<span class="prd-title-small">
									{!! $title !!}
								</span>
						@endif

					@else
						@if ($item)
							{!! $item->title !!}
						@else
							{!! $parent->title !!}
							&gt;
							<span class="prd-title-small">
									{!! $title !!}
								</span>
						@endif
					@endif

					@if ($item)
						@can('edit', $item)
							<a class="btn btn-success pull-right" href="{!! route('slt:article.establish', $item->id) !!}">
								编辑本页
							</a>
						@endcan
					@else
						<a href="{!! route_url('web:prd.create', null, ['title'=> $title, 'parent_id' => $parent_id]) !!}" class="btn btn-success pull-right">
							编辑本页
						</a>
					@endif
				</h3>
			</div>
			<div class="markdown-body">
				@if ($item)
					{!! $item->content !!}
				@else
					无内容
				@endif
			</div>
		</div>
@endsection
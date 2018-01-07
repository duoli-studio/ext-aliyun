@extends('daniu.template.main')
@section('body-start')
	<body class="bg-grey">@endsection
	@section('daniu-main')
		@include('front.inc.nav')
		@include('front.prd.header')
		<div class="container markdown prd-detail">
			<div class="prd-detail-title">
				<h3 class="clearfix">
					@if (count($level_titles))
						@foreach($level_titles as $num => $prd)
							@if ($num == 0)
								<a class="prd-title-first" href="{!! $prd->crypt_url !!}">
									{!! $prd->prd_title !!}
								</a>
							@else
								&gt;
								<a class="prd-title-small" href="{!! $prd->crypt_url !!}">
									{!! $prd->prd_title !!}
								</a>
							@endif
						@endforeach
						@if ($item)
							&gt;
							<small>
								{!! $item->prd_title !!}
							</small>
						@else
							&gt;
							<span class="prd-title-small">
									{!! $title !!}
								</span>
						@endif

					@else
						@if ($item)
							{!! $item->prd_title !!}
						@else
							{!! $parent->prd_title !!}
							&gt;
							<span class="prd-title-small">
									{!! $title !!}
								</span>
						@endif
					@endif

					@if ($item)
						@can('edit', $item)
							<a class="btn btn-success pull-right" href="{!! route('web:prd.edit', $item->id) !!}">
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
					{!! $item->prd_content !!}
				@else
					无内容
				@endif
			</div>
		</div>
@endsection
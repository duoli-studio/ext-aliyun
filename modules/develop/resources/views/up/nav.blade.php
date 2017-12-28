<nav class="navbar navbar-default">
	<div class="navbar-header">
		<a class="navbar-brand" href="{!! route('dev_home.cp') !!}"><i class="fa fa-home"></i></a>
	</div>
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			@foreach($data['group'] as $group_key => $group)
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{!! $group_key !!} <span class="caret"></span></a>
				<ul class="dropdown-menu">
					@foreach($group as $link)
						<li><a href="{!! route_url('dev_up.auto',null, ['url'=>$link->url, 'method' => $link->type]) !!}">{!! $link->title !!}</a></li>
					@endforeach
				</ul>
			</li>
			@endforeach
		</ul>
		@if (isset($self_menu))
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">帮助文档 <span class="caret"></span></a>
					<ul class="dropdown-menu">
						@foreach($self_menu as $title => $link)
							<li><a target="_blank" href="{!! $link !!}">{!! $title !!}</a></li>
						@endforeach
					</ul>
				</li>
			</ul>
		@endif
	</div>
</nav>
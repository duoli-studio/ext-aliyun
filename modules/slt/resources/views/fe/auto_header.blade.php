<nav class="navbar navbar-default">
	<div class="navbar-header">
		<a class="navbar-brand" href="{!! route('web:fe.md') !!}"><i class="fa fa-home"></i></a>
	</div>
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		@foreach($singles as $key => $single)
			<ul class="nav navbar-nav">
				@if (isset($single) && $single)
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">Js ({!! $key !!}) <span class="caret"></span></a>
						<ul class="dropdown-menu">
							@foreach($single as $link)
								<li><a href="{!! route('web:fe.js', $link) !!}">{!! $link !!}</a></li>
							@endforeach
						</ul>
					</li>
				@endif
			</ul>
		@endforeach
		@foreach($jquerys as $key => $single)
			<ul class="nav navbar-nav">
				@if (isset($single) && $single)
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">jQuery ({!! $key !!}) <span class="caret"></span></a>
						<ul class="dropdown-menu">
							@foreach($single as $link)
								<li><a href="{!! route('web:fe.js', ['jquery.'. $link]) !!}">{!! $link !!}</a></li>
							@endforeach
						</ul>
					</li>
				@endif
			</ul>
		@endforeach
		@foreach($bt3s as $key => $single)
			<ul class="nav navbar-nav">
				@if (isset($single) && $single)
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">jQuery Bt3({!! $key !!}) <span class="caret"></span></a>
						<ul class="dropdown-menu">
							@foreach($single as $link)
								<li><a href="{!! route('web:fe.js', ['jquery.bt3.'. $link]) !!}">{!! $link !!}</a></li>
							@endforeach
						</ul>
					</li>
				@endif
			</ul>
		@endforeach
	</div>
</nav>

@extends('lemon.template.desktop_angle')
@section('desktop-main')
	<div class="page">
		@include('desktop.platform_order.header_detail')
		<div>
			@include('desktop.inc.pt_status_detail')
		</div>
	</div>
@endsection
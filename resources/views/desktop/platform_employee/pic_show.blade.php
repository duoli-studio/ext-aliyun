@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page">
        @include('desktop.platform_employee.header_button')
        <div class="clearfix">
            @include('desktop.platform_employee.header_detail')
        </div>
		<div>
			{{--@if ($pictures)--}}
				<table class="table table-bordered">
					<tr>
						<th class="w144">时间:</th>
						<td class="w144">说明</td>
						<td>截图</td>
						<td>上传者</td>
					</tr>
					@foreach($pictures as $picture)
						<tr>
							<th class="w144">{!! $picture['created_at'] !!}</th>
							<td class="w144">{!! $picture['pic_desc'] !!}</td>
							<td>
								@if ($picture['pic_screen'])
									{!! Form::showThumb($picture['pic_screen'], ['height'=>30]) !!}
								@endif
							</td>
							<td>{!! $picture['account_name'] !!}</td>
						</tr>
					@endforeach
				</table>
				{{--@if ($pictureshasPages())--}}
					{{--<div class="clearfix">--}}
						{{--<div class="pull-right">{!!$pictures->render()!!}</div>--}}
					{{--</div>--}}
				{{--@endif--}}
			{{--@else--}}
				{{--@include('desktop.inc.empty')--}}
			{{--@endif--}}
		</div>
	</div>
@endsection
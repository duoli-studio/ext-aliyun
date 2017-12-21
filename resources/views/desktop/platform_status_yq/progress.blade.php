@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
	<div class="page">
		@include('desktop.platform_order.header_detail')
		<div>
				<table class="table table-bordered">
					<tr>
						<th class="w144">时间:</th>
						<td>说明</td>
						<td>截图</td>
						<td>上传者id</td>
						<td>上传者</td>
					</tr>
					@foreach($pictures as $picture)
						<tr>
							<th>{!! $picture['create_time'] !!}</th>
							<td>{!! $picture['comment'] !!}</td>
							<td>
								@if ($picture['path'])
									{!! Form::showThumb($picture['path'], ['height'=>30]) !!}
								@endif
							</td>
                            <td>{!! $picture['uid'] !!}</td>
							<td>{!! $picture['nickname'] !!}</td>
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
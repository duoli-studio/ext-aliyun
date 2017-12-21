@extends('lemon.template.desktop_angle')
@section('desktop-main')
		@include('desktop.game_server.header')
        <div class="panel panel-default">
            <div class="panel-body">
			{!! Form::open(['route' => 'dsk_game_server.sort', 'id' => 'form_sort', 'method' => 'post']) !!}
			<table width="98%" border="0" cellpadding="5" cellspacing="1" class="table J_hover">
				<tr class="thead thead-space thead-center">
					<th class="w72">排序</th>
					<th class="w72">ID</th>
					<th>区/服</th>
					<th>游戏名称</th>
					<th>腾讯编码</th>
					<th>代练猫标识</th>
					<th>代练通标识</th>
					<th>易代练标识</th>
					<th>17代练标识</th>
					<th>电竞包子标识</th>
					<th class="w216">管理操作</th>
				</tr>
				{!! $htmlTree!!}
				<tr>
					<td colspan="7" valign="middle">
						<button class="btn btn-primary" type="submit"><span>排序</span></button>
					</td>
				</tr>
			</table>
			{!! Form::close() !!}
		</div>
	</div>
@endsection
@extends('lemon.template.desktop_angle')
@section('desktop-main')
    @include('desktop.platform_employee.header')
    <div class="panel panel-default">
        <div class="panel-body">
            {!! Form::model(\Input::all(),['id' => 'form_search', 'method' => 'get','class' => 'form-inline']) !!}
            <table class="table table-search">
                <tr>
                    <td class="search-head">
                        {!! Form::select('order_status', \App\Models\PlatformOrder::kvEmployeeOrderStatus(), null, ['id'=>'search_status', 'placeholder' => '订单状态(所有)', 'class' => 'form-control']) !!}
                        {!! Form::label('game_id', '游戏:') !!}
                        {!! Form::select('game_id', \App\Models\GameName::kvLinear(), null, ['id'=>'game_id', 'class' => 'form-control']) !!}
                        <em id="server_ctr">
                            {!!Form::select('server_id', ['请选择游戏'], null, ['id' => 'server_id', 'class' => 'form-control'])!!}
                        </em>
                        <em id="type_ctr">
                            {!!Form::select('type_id', ['请选择代练类型'], null, ['id' => 'type_id', 'class' => 'form-control'])!!}
                        </em>
                        {!! Form::select('field', $fields, null, ['placeholder'=>'搜索选项','class' => 'form-control']) !!}
                        {!! Form::text('kw', null, ['placeholder' => '搜索值', 'class' => 'form-control', 'id'=>'search_kw']) !!}
                        {!! Form::label('input_date', '发单时间:') !!}
                        {!! Form::text('publish_start_date', null, ['id'=> 'J_publishStart', 'class' => 'form-control w120', 'placeholder'=>'开始时间']) !!}
                        {!! Form::text('publish_end_date', null, ['id'=> 'J_publishEnd', 'class' => 'form-control w120', 'placeholder'=>'结束时间']) !!}
                        {!! Form::text('pagesize', $_pagesize, ['class' => 'form-control w48','placeholder' => '分页数量']) !!}
                        {!! Form::label('export', '导出') !!}
                        {!! Form::checkbox('export', 1, null) !!}
                        {!! Form::button('搜索', ['class'=> 'btn btn-primary', 'type'=> 'submit']) !!}
                        <a href="{!! route_url() !!}" class="btn btn-default">重置搜索</a>
                    </td>
                </tr>
                <script>
					require(['jquery', 'jquery.ui'], function ($) {
						$(function () {
							$("#J_inputStart").datepicker({
								onClose   : function (selectedDate) {
									$("#J_inputEnd").datepicker("option", "minDate", selectedDate);
								},
								dateFormat: "yy-mm-dd"
							});
							$("#J_inputEnd").datepicker({
								maxDate   : 0,
								onClose   : function (selectedDate) {
									$("#J_inputStart").datepicker("option", "maxDate", selectedDate);
								},
								dateFormat: "yy-mm-dd"
							});
							$("#J_publishStart").datepicker({
								onClose   : function (selectedDate) {
									$("#J_publishEnd").datepicker("option", "minDate", selectedDate);
								},
								dateFormat: "yy-mm-dd"
							});
							$("#J_publishEnd").datepicker({
								maxDate   : 0,
								onClose   : function (selectedDate) {
									$("#J_publishStart").datepicker("option", "maxDate", selectedDate);
								},
								dateFormat: "yy-mm-dd"
							});
						})
					})
                </script>
            </table>
            {!! Form::close() !!}
            <script>
				require(['jquery', 'lemon/util', 'fadan/util', 'jquery.tagging-js'], function ($, util, helper) {
					$(function () {
						helper.server_html('game_id', 'server_ctr', 'server_id', {
							'id'       : 'server_id',
							'server_id': @if (Input::input('server_id')) {!! Input::input('server_id') !!} @else 0 @endif
						});
						helper.type_html('game_id', 'type_ctr', 'type_id', {
							'id'     : 'server_id',
							'type_id': @if (Input::input('type_id')) {!! Input::input('type_id') !!} @else 0 @endif
						});
					});
				});
            </script>
            <!-- 数据表格 -->
            {!! Form::open() !!}
            <table class="table table-condensed">
                <tr>
                    <th><input type="checkbox" class="J_checkAll"/></th>
                    <th class="w48">订单ID</th>
                    <th class="w84">发单时间{!! Form::order('published_at') !!}</th>
                    <th class="w144">区服</th>
                    <th>标题</th>
                    <th>分配到电脑</th>
                    <th class="w120">角色名</th>
                    <th class="w84">游戏名称</th>
                    <th class="w108">当前状态</th>
                    <th class="w72">剩余时间{!! Form::order('order_left_hours') !!}</th>
                    <th class="w84">操作</th>
                </tr>
                @foreach($items as $item)
                    <tr class="border {!! $item->order_color ? 'show-color-'.$item->order_color: '' !!}">
                        <td><input type="checkbox" name="id[]" value="{{$item->order_id}}"
                                   class="J_checkItem"/></td>
                        <td>{{$item['order_id']}}</td>
                        <td>{{ $item->published_at ? \App\Lemon\Repositories\Sour\LmTime::datetime($item->published_at, '2-2') : '--'}}</td>
                        <td>{{$item['game_area']}}</td>
                        <td>
                            <a href="{!! route('dsk_platform_employee.employee_order_detail', [$item->order_id]) !!}"
                               data-width="800"
                               data-shade_close="true"
                               class="J_iframe">
                                @if ($item->order_status != \App\Models\PlatformOrder::ORDER_STATUS_CREATE)
                                    @if ($item->order_lock)
                                        <i class="fa fa-lock text-danger"
                                           data-toggle="tooltip"
                                           title="{!! \App\Models\PlatformOrder::kvOrderLock($item->order_lock) !!}"></i>
                                    @else
                                        <i class="fa fa-unlock text-success"
                                           data-toggle="tooltip"
                                           title="{!! \App\Models\PlatformOrder::kvOrderLock($item->order_lock) !!}"></i>
                                    @endif
                                @endif
                                {{$item->order_title}}
                            </a>

                        </td>
                        <td>
                            @if ($item['pc_num'])
                                {!! $item['pc_num'] !!}
                            @else
                                --
                            @endif
                        </td>
                        <td>{{$item['game_actor']}}

                            @if ($item['order_qq'])
                                {!! im($item['order_qq'], 'qq') !!}
                            @endif
                        </td>
                        <td>{!! \App\Models\GameName::getName($item['game_id']) !!}</td>
                        <td>
                            @if($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                {!! \App\Models\PlatformOrder::kvCancelType($item->cancel_type) !!}
                            @endif
                            {{ \App\Models\PlatformOrder::kvOrderStatus($item->order_status) }}
                            @if($item->order_status == \App\Models\PlatformOrder::ORDER_STATUS_CANCEL)
                                {!! \App\Models\PlatformOrder::kvCancelStatus($item->cancel_status) !!}
                            @endif
                        </td>
                        <td>
                            {!!  \App\Lemon\Repositories\Sour\LmTime::surplus($item->created_at,$item->order_hours) !!}
                        </td>
                        <td>
                            @can('employee_delete', $item)
                                <a href="{!! route('dsk_platform_employee.delete', [$item->order_id]) !!}"
                                   class="J_request" data-confirm="确定删除该订单吗?">
                                    <i class="fa fa-remove fa-lg text-info" data-toggle="tooltip" title="拒绝接单"></i>
                                </a>
                                ---
                            @endcan
                            @can('employee_handle', $item)
                                <a href="{!! route('dsk_platform_employee.handle', [$item->order_id]) !!}"
                                   class="J_request" data-confirm="确定接手订单?">
                                    <i class="fa fa-check fa-lg text-info" data-toggle="tooltip" title="同意接单"></i>
                                </a>
                            @endcan
                            @can('employee_over', $item)
                                <a href="{!! route('dsk_platform_employee.examine', [$item->order_id]) !!}"
                                   class="J_iframe" data-title="确认完单">
                                    <i class="fa fa-empire fa-lg text-info" data-toggle="tooltip" title="确认完单"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                        </td>
                    </tr>
                @endforeach
            </table>
            <button class="btn-small J_submit red" data-confirm="批量接单?"
                    data-url="{{route('dsk_platform_employee.branch_handle')}}" data-ajax="true"><span>批量接单</span>
            </button>
        {!! Form::close() !!}
        <!-- 分页 -->
            <div class="clearfix mt10">
                <div class="pagination">
                    {!! $items->render() !!}
                </div>
            </div>
        </div>
    </div>
    <script>
		requirejs(['jquery', 'jquery.layer', 'lemon/util'], function ($, layer, util) {
			$('.J_p_note').on('click', function () {
				var type    = $(this).attr('data-type') ? $(this).attr('data-type') : 'actor';
				var content = $(this).attr('data-content');
				var title   = $(this).attr('data-title') ? $(this).attr('data-title') : '输入备注的内容';
				var id      = $(this).attr('data-id');
				//prompt层
				layer.prompt({
					title   : title,
					formType: 2,
					value   : content
				}, function (text) {
					util.make_request('{!! route('dsk_platform_order.note') !!}', {
						'id'   : id,
						'type' : type,
						'value': text
					}, util.splash);
				});
			})
		})
    </script>
@endsection
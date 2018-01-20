@extends('system::backend.tpl.dialog')
@section('backend-main')
    {!! Form::open(['route' => ['backend:role.menu', $role->id]]) !!}
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
		<?php $display = false;?>
        @foreach($permission as $pk => $pv)
            <li role="presentation" class="{!! $display ? '' : 'active' !!}">
                <a href="#{!! $pk !!}" aria-controls="{!! $pk !!}" role="tab" data-toggle="tab">{!! $pv['title'] !!}</a>
            </li>
        @endforeach
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        @foreach($permission as $pk => $pv)
            <div role="tabpanel" class="tab-pane {!! $display ? '' : 'active' !!}" id="{!! $pk !!}">
                @if ($pv['groups'])
                    <table class="table">
                        <tr>
                            <th class="w108">分组</th>
                            <th>权限</th>
                        </tr>
                        @foreach($pv['groups'] as $gk => $gv)
                            <tr>
                                <td>{!! $gv['title'] !!}</td>
                                <td>
                                    @foreach($gv['permissions'] as $sk => $sv)
                                        {!! Form::checkbox('permission_id[]', $sv['id'], $sv['value']) !!} {!! $sv['description'] !!}
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
			<?php $display = true; ?>
        @endforeach
    </div>
    {!! Form::button('保存', ['class'=>'btn btn-primary J_submit', 'type'=>'submit']) !!}
    {!!Form::close()!!}
@endsection
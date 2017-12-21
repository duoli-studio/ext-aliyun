@extends('lemon.template.desktop_angle_iframe')
@section('desktop-main')
    {!! Form::open(['route' => ['dsk_pam_role.menu', $role->id]]) !!}
    <table class="table">
        @foreach($permission as $title => $links)
            <tr>
                <td>
                    {!! $title !!}
                </td>
                <td>
                    @foreach($links as $link)
                        <label for="per_{!! $link->id !!}" class="checkbox-inline inline-block w144 ml0 mt4">
                            <input id="per_{!! $link->id !!}" type="checkbox" name="key[{!! $link->id !!}]"
                                   @if ($role->hasPermission($link->permission_name)) checked="checked"
                                   @endif value="1">
                            @if ($link->is_menu)
                                {!! Form::tip("菜单项目", "fa-bars") !!}
                            @endif
                            {!! $link->permission_title !!}
                        </label>
                    @endforeach
                </td>
            </tr>
        @endforeach
        <tr>
            <td>{!! Form::button('保存', ['class'=>'btn btn-primary', 'type'=>'submit']) !!}</td>
        </tr>
    </table>
    {!!Form::close()!!}
@endsection
@extends('system::tpl.develop')
@section('develop-main')
    @include('system::develop.inc.header')
    @foreach($_menus as $nav_key => $nav)
        <div class="row">
            <div class="col-md-12">
                <h3 id="{{$nav_key}}">{!! app('poppy')->get($nav_key."::name") !!}</h3>
                <table class="table table-bordered">
                    @foreach($nav as $nav_group)
                        <tr>
                            <td class="col-lg-1">
                                <button class="btn btn-success">{!! $nav_group['title'] !!}</button>
                            </td>
                            <td>
                                @if (isset($nav_group['children']) && is_array($nav_group['children']))
                                    @foreach($nav_group['children'] as $sub)
                                        <a class="btn btn-default" href="{{ $sub['url'] }}" data-rel="{{$sub['key']}}">
                                            {{$sub['title']}}
                                        </a>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endforeach
@endsection
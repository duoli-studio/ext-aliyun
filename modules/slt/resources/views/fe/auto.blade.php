@extends('slt::inc.tpl')
@section('tpl-main')
    @include('web.fe.auto_header')
    <h3>{!! $plugin !!}</h3>
    <div>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" style="margin-bottom: 15px;" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">源代码示例</a>
            </li>
            @if ($readme)
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab"
                                           data-toggle="tab">Readme</a></li>
            @endif
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                @if ($view)
                    {!! $view !!}
                @else
                    暂时没有源码示例
                @endif
            </div>
            @if ($readme)
                <div role="tabpanel" class="tab-pane" id="profile">{!! $readme !!}</div>
            @endif
        </div>
    </div>
@endsection
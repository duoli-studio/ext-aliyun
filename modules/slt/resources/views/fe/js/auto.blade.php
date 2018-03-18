@extends('slt::tpl.default')
@section('tpl-main')
    @include('slt::fe.js.auto_header')
    <div class="container fe--jslib">
        <h2>{!! $plugin !!}</h2>
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" style="margin-bottom: 15px;" role="tablist">
                <li role="presentation" class="active">
                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">示例</a>
                </li>
                @if ($readme)
                    <li role="presentation">
                        <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Readme</a>
                    </li>
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
                    <div role="tabpanel" class="tab-pane editor-preview-side jslib-markdown" id="profile">
                        {!! $readme !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
	require(['poppy/doc', 'bt3', 'bt3.hover-dropdown'], function(doc) {
		doc.fill_and_highlight('J_script_source', 'J_script', 'script');
		doc.highlight();
		doc.trim_content('J_html');
		$('.dropdown-toggle').dropdownHover();
	});
    </script>
@endsection
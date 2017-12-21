<div class="content-heading">
    允许访问的IP
    <div class="pull-right">
        <div class="btn-group">
            <a class="btn btn-default {{ ($_route == 'dsk_plugin_ip.index') ? 'btn-info' : '' }}"
               href="{{route('dsk_plugin_ip.index')}}"><span>允许IP列表</span></a>
            <a class="btn btn-default {{ ($_route == 'dsk_plugin_ip.create') ? 'btn-info' : '' }}"
               href="{{route('dsk_plugin_ip.create')}}"><span>添加IP</span></a>
        </div>
    </div>
</div>
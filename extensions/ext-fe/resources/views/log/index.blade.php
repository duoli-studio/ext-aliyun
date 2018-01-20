@extends('system::tpl.develop')
@section('head-css')
    @parent()
    <style>
        .stack {
            font-size : 0.85em;
        }
        .date {
            min-width : 75px;
        }
        .text {
            word-break : break-all;
        }
        a.llv-active {
            z-index          : 2;
            background-color : #f5f5f5;
        }
        .dataTables_wrapper {
            padding-bottom : 30px;
        }
    </style>
@endsection
@section('develop-main')
    @include('system::develop.inc.header')
    <div class="row">
        <div class="col-sm-3 col-md-2">
            <h6><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Laravel 日志查看器</h6>
            <p class="text-muted"><i>by Sour Lemon Team</i></p>
            <div class="list-group">
                @foreach($files as $file)
                    <a href="?l={{ base64_encode($file) }}"
                       class="list-group-item @if ($current_file == $file) llv-active @endif"
                       style="padding: 7px;">
                        {{$file}}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-sm-9 col-md-10 table-container">
            @if ($logs === null)
                <div>
                    日志文件 > 20M, 请直接下载.
                </div>
            @else
                <table id="table-log" class="table table-striped dataTable">
                    <thead>
                    <tr>
                        <th style="width:70px;">分级</th>
                        <th>时间</th>
                        <th>日志</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($logs as $key => $log)
                        <tr>
                            <td class="text-{{$log['level_class']}}"><span
                                        class="glyphicon glyphicon-{{$log['level_img']}}-sign"
                                        aria-hidden="true"></span> &nbsp;{{$log['level']}}</td>
                            <td class="date">{{$log['date']}}</td>
                            <td class="text">
                                @if ($log['stack']) <a class="pull-right expand btn btn-default btn-xs"
                                                       data-display="stack{{$key}}"><span
                                            class="glyphicon glyphicon-search"></span></a>@endif
                                {{$log['text']}}
                                @if (isset($log['in_file'])) <br/>{{$log['in_file']}}@endif
                                @if ($log['stack'])
                                    <div class="stack" id="stack{{$key}}"
                                         style="display: none; white-space: pre-wrap;">{{ trim($log['stack']) }}</div>@endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
            <div>
                <a href="?dl={{ base64_encode($current_file) }}"><span
                            class="glyphicon glyphicon-download-alt"></span> 下载文件</a>
                -
                <a id="delete-log" href="?del={{ base64_encode($current_file) }}"><span
                            class="glyphicon glyphicon-trash"></span> 删除日志</a>
            </div>
        </div>
    </div>

    <script>
    requirejs(['jquery', 'datatables.net', 'bt3.data-tables'], function ($) {
	    $(document).ready(function () {
		    $('#table-log').DataTable({
			    "order" : [1, 'desc'],
			    "stateSave" : true,
			    "stateSaveCallback" : function (settings, data) {
				    window.localStorage.setItem("datatable", JSON.stringify(data));
			    },
			    "stateLoadCallback" : function (settings) {
				    var data = JSON.parse(window.localStorage.getItem("datatable"));
				    if ( data ) data.start = 0;
				    return data;
			    }
		    });

		    $('.table-container').on('click', '.expand', function () {
			    $('#' + $(this).data('display')).toggle();
		    });
		    $('#delete-log').click(function () {
			    return confirm('确认删除?');
		    });
	    });
    })
    </script>
@endsection
@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        发单价格管理
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
        {!! Form::open(['route' => 'dsk_billing_price.index','id' => 'form_role','class' => 'form-horizontal']) !!}
        <!-- 数据表格 -->
            <ul class="nav nav-tabs" data-relation="order">
                <li role="presentation" class="active"><a href="#danshaung" aria-controls="danshuang" role="tab"
                                                          data-toggle="tab"><span>单双排</span></a></li>
                <li role="presentation"><a href="#linghuo" aria-controls="linghuo" role="tab"
                                           data-toggle="tab"><span>灵活排位</span></a></li>
            </ul>
            <div class="tab-content" role="tablist">
                <div role="tabpanel" class="tab-pane active" id="danshaung">
                    <table class="table table-narrow" width="100%">
                        <tr>
                            <th>价格区</th>
                            @foreach($items['area'] as $item)
                                <th>{{$item}}</th>
                            @endforeach
                        </tr>
                        @foreach($items['dan'] as $k=>$v)
                            <tr>
                                <td>
                                    {{App\Lemon\Dailian\Application\Lol\Price::fullDesc($k)['start_desc']}}-
                                    {{App\Lemon\Dailian\Application\Lol\Price::fullDesc($k)['end_desc']}}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal]'.'[' .$k. ']',$data['single_double']['normal'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal-good]'.'[' .$k. ']', $data['single_double']['normal-good'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal-best]'.'[' .$k. ']',$data['single_double']['normal-best'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal-level]'.'[' .$k. ']', $data['single_double']['normal-level'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][telecom]'.'[' .$k. ']',$data['single_double']['telecom'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][telecom-good]'.'[' .$k. ']', $data['single_double']['telecom-good'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][telecom-best]'.'[' .$k. ']', $data['single_double']['telecom-best'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][telecom-level]'.'[' .$k. ']', $data['single_double']['telecom-level'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal2]'.'[' .$k. ']', $data['single_double']['normal2'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal2-good]'.'[' .$k. ']', $data['single_double']['normal2-good'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal2-best]'.'[' .$k. ']', $data['single_double']['normal2-best'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[single_double][normal2-level]'.'[' .$k. ']', $data['single_double']['normal2-level'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="linghuo">
                    <table class="table table-narrow" width="100%">
                        <tr>
                            <th>价格区</th>
                            @foreach($items['area'] as $item)
                                <th>{{$item}}</th>
                            @endforeach
                        </tr>
                        @foreach($items['dan'] as $k=>$v)
                            <tr>
                                <td>
                                    {{App\Lemon\Dailian\Application\Lol\Price::fullDesc($k)['start_desc']}}-
                                    {{App\Lemon\Dailian\Application\Lol\Price::fullDesc($k)['end_desc']}}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal]'.'[' .$k. ']',$data['flexible']['normal'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal-good]'.'[' .$k. ']', $data['flexible']['normal-good'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal-best]'.'[' .$k. ']',$data['flexible']['normal-best'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal-level]'.'[' .$k. ']', $data['flexible']['normal-level'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][telecom]'.'[' .$k. ']',$data['flexible']['telecom'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][telecom-good]'.'[' .$k. ']', $data['flexible']['telecom-good'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][telecom-best]'.'[' .$k. ']', $data['flexible']['telecom-best'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][telecom-level]'.'[' .$k. ']', $data['flexible']['telecom-level'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal2]'.'[' .$k. ']', $data['flexible']['normal2'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal2-good]'.'[' .$k. ']', $data['flexible']['normal2-good'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal2-best]'.'[' .$k. ']', $data['flexible']['normal2-best'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                                <td>
                                    {!! Form::text('price[flexible][normal2-level]'.'[' .$k. ']', $data['flexible']['normal2-level'][$k], ['class'=>'form-control w72']) !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <!-- 分页 -->
            {!! Form::button('提交', ['class'=> 'btn btn-primary btn-sm', 'type'=> 'submit']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection
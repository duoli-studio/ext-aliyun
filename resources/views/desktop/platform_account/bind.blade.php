@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        发单账号绑定
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
                {!! Form::model($_desktop,['id' => 'form_bind', 'class' => 'form-horizontal']) !!}
                @if ($yi)
                    @foreach($yi as $k => $y)
                        <div class="form-group">
                            {!! Form::label('', '易代练账号', ['class' => 'col-lg-2 control-label strong validation']) !!}
                            <div class="col-lg-2">
                                {!! Form::select('yi[]', $yi, isset($yi_account[$k]) ? $k: null, [
						'placeholder' => '请选择易代练账号','class' => 'form-control'
					]) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
                @if ($baozi)
                    @foreach($baozi as $k => $y)
                        <div class="form-group">
                            {!! Form::label('', '电竞包子账号', ['class' => 'col-lg-2 control-label strong validation']) !!}
                            <div class="col-lg-2">
                                {!! Form::select('baozi[]', $baozi, isset($baozi_account[$k]) ? $k: null, [
                        'placeholder' => '请选择电竞包子账号','class' => 'form-control'
                    ]) !!}
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($yq)
                    @foreach($yq as $k => $y)
                        <div class="form-group">
                            {!! Form::label('', '17代练账号', ['class' => 'col-lg-2 control-label strong validation']) !!}
                            <div class="col-lg-2">
                                {!! Form::select('yq[]', $yq, isset($yq_account[$k]) ? $k: null, [
                        'placeholder' => '请选择17代练账号','class' => 'form-control'
                    ]) !!}
                            </div>
                        </div>
                    @endforeach
                @endif

                @if ($mao)
                    @foreach($mao as $k => $m)
                        <div class="form-group">
                            {!! Form::label('', '代练猫账号', ['class' => 'col-lg-2 control-label strong validation']) !!}
                            <div class="col-lg-2">
                                {!! Form::select('mao[]', $mao,  isset($mao_account[$k]) ? $k: null, [
                'placeholder' => '请选择代练猫账号','class' => 'form-control'
					]) !!}
                            </div>
                        </div>

                    @endforeach
                @endif
                @if ($mama)
                    @foreach($mama as $k => $m)
                        <div class="form-group">
                            {!! Form::label('', '代练妈妈账号', ['class' => 'col-lg-2 control-label strong validation']) !!}
                            <div class="col-lg-2">
                                {!! Form::select('mama[]', $mama,  isset($mama_account[$k]) ? $k: null, [
                'placeholder' => '请选择代练妈妈账号','class' => 'form-control'
					]) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
                @if ($tong)
                    @foreach($tong as $k => $t)
                        <div class="form-group">
                            {!! Form::label('', '代练通账号', ['class' => 'col-lg-2 control-label strong validation']) !!}
                            <div class="col-lg-2">
                                {!! Form::select('tong[]', $tong,  isset($tong_account[$k]) ? $k: null, [
                'placeholder' => '请选择代练通账号','class' => 'form-control'
					]) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="form-group">
                    {!! Form::label('', '', ['class' => 'col-lg-2 control-label strong validation']) !!}
                    <div class="col-lg-2">
                        {!! Form::button('绑定', ['class'=>'btn btn-info', 'type'=> 'submit']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
    </div>
@endsection
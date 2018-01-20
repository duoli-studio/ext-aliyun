@extends('system::tpl.develop')
@section('develop-main')
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(['id'=> 'form_auto']) !!}
            {!! Form::hidden('type', \Input::get('type')) !!}
            <div class="form-group">
                <label for="token">Token</label>
                ( String ) [token]
                <textarea id="token" name="token" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <button class="btn btn-info J_submit" type="submit" id="submit">设置token</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
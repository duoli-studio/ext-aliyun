@extends('lemon.template.desktop_angle')
@section('desktop-main')
    <div class="content-heading">
        {!! $title !!}
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{!! $title !!}</div>
                <div class="panel-body">
                    <div role="tabpanel">
                        <!-- Nav tabs-->
                        <ul role="tablist" class="nav nav-tabs">
							<?php $i = 0 ?>
                            @foreach($groups as $group_key => $group)
                                <li role="presentation" class="<?php echo($i++ == 0 ? 'active' : ''); ?>">
                                    <a href="#{!! $group_key !!}" aria-controls="{!! $group_key !!}" role="tab"
                                       data-toggle="tab">
                                        {!! isset($group['title']) ? $group['title'] : '_其他'  !!}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Tab panes -->
                    <div class="tab-content">
						<?php $i = 0 ?>
                        @foreach($groups as $group_key => $group)
                            <div role="tabpanel" class="tab-pane <?php echo($i++ == 0 ? 'active' : ''); ?>"
                                 id="{!! $group_key !!}">
                                @if (isset($group['_items']))
                                    {!! Form::open(['url' => $url,'id' => 'form_'.$group_key, 'class' => 'form-horizontal']) !!}

                                    @foreach($group['_items'] as $item_key => $item)
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label">
                                                {!! $item['_label'] !!}
                                                {!! $item['_tip'] !!}
                                            </label>
                                            <div class="col-lg-10">
                                                {!! $item['_render'] !!}
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="form-group">
                                        <div class="col-lg-10 col-lg-offset-2">
                                            {!! Form::button('提交',['class'=>'btn btn-info', 'type'=>'submit']) !!}
                                        </div>
                                    </div>
                                    {!!Form::close()!!}
                                    <script>
									requirejs(['jquery', 'jquery.validate'], function ($) {
										$(function () {
											$('#form_' + '{!! $group_key !!}').validate();
										})
									})
                                    </script>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <div>


            </div>
        </div>
    </div>
@endsection
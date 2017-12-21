<label>{!! Form::checkbox('status[]', 'over', null) !!} 订单完成</label>
<label>{!! Form::checkbox('status[]', 'ing', null) !!} 进行中</label>
<label>{!! Form::checkbox('status[]', 'canceled', null) !!} 已撤销</label>
<label>{!! Form::checkbox('status[]', 'refund', null) !!} 已退款</label>
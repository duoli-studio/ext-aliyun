@extends('lemon.template.shopnc')
@section('desktop-main')
	<div class="doc-container">
		@include('lemon.shopnc.header')
        <div class="doc-header">
            Form <span class="intro">Form 表单</span>
        </div>
		<h3 class="doc-title">标签</h3>
		<div class="mt10 clearfix">
			<div class="span-12">
				<div class="doc-emmet">label.strong.validation{label validation}</div>
				<div class="doc-jsGen">
					<label for="" class="strong validation">label validation</label>
				</div>
			</div>
			<div class="span-12 last">
				<div class="doc-emmet">label.strong.place{label place}</div>
				<div class="doc-jsGen">
					<label for="" class="strong place">label place</label>
				</div>
			</div>
		</div>
		<h3 class="doc-title">选择器</h3>
		<div class="mt10 clearfix">
			<div class="span-12">
				<div class="doc-emmet">
					select[name=key]>option{option1}
				</div>
				<div class="doc-jsGen">
					<select name="key" id="">
						<option value="">option1</option>
					</select>
				</div>
			</div>
		</div>
		<h3 class="doc-title">Input 输入框</h3>
		<div class="mt10 clearfix">
			<div class="span-12">
				<div class="doc-emmet">
					input:t[name=key]
				</div>
				<div class="doc-jsGen">
					<input type="text" name="key" id="">
				</div>
			</div>
			<div class="span-12 last">
				<div class="doc-emmet">
					(input:text.w24[id=txk$]+label[for=txk$]{text$})*3
				</div>
				<div class="doc-jsGen">
					<input type="text" name="" id="txk1" class="w24"><label for="txk1">text1</label>
					<input type="text" name="" id="txk2" class="w24"><label for="txk2">text2</label>
					<input type="text" name="" id="txk3" class="w24"><label for="txk3">text3</label>
				</div>
			</div>
		</div>
		<h3 class="doc-title">textarea 输入框</h3>
		<div class="mt10 clearfix">
			<div class="span-12">
				<div class="doc-emmet">
					textarea.w360[name=key][cols=50][rows=5]{placeholder}
				</div>
				<div class="doc-jsGen">
					<textarea name="key" id="" cols="50" rows="5" class="w360">placeholder</textarea>
				</div>
			</div>
		</div>
		<h3 class="doc-title">复选/单选 输入框</h3>
		<div class="mt10 clearfix">
			<div class="span-18">
				<div class="doc-emmet">
					div.form-element>ul.check-list>(li>input:checkbox[value=key$][id=ck$]+label[for=ck$]{checkbox$})*3
				</div>
				<div class="doc-jsGen">
					<div class="form-element">
						<ul class="check-list">
							<li><input type="checkbox" name="" id="ck1" value="key1" /><label for="ck1">checkbox1</label></li>
							<li><input type="checkbox" name="" id="ck2" value="key2" /><label for="ck2">checkbox2</label></li>
							<li><input type="checkbox" name="" id="ck3" value="key3" /><label for="ck3">checkbox3</label></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<h3 class="doc-title">按钮</h3>
		<div class="mt10 clearfix">
			<div class="span-8">
				<div class="doc-emmet">
					button.btn-sm>span{btn text}
				</div>
				<div class="doc-jsGen">
					<button class="btn-small"><span>btn text</span></button>
				</div>
			</div>
			<div class="span-8">
				<div class="doc-emmet">
					button.btn>span{btn default}
				</div>
				<div class="doc-jsGen">
					<button class="btn"><span>btn default</span></button>
				</div>
			</div>
			<div class="span-8 last">
				<div class="doc-emmet">
					button.btn-search>span{btn search}
				</div>
				<div class="doc-jsGen">
					<button class="btn-search"><span>btn search</span></button>
				</div>
			</div>
		</div>
		<div class="mt10 clearfix">
			<div class="span-8">
				<div class="doc-emmet">
					button.btn-add-nofloat>span{btn add nofloat}
				</div>
				<div class="doc-jsGen">
					<button class="btn-add-nofloat"><span>btn add nofloat</span></button>
				</div>
			</div>
			<div class="span-8">
				<div class="doc-emmet">
					button.btn-add>span{btn add}
				</div>
				<div class="doc-jsGen clearfix">
					<button class="btn-add"><span>btn add</span></button>
				</div>
			</div>
		</div>
	</div>
@endsection
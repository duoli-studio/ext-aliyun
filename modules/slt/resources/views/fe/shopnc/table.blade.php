@extends('lemon.template.shopnc')
@section('desktop-main')
	<div class="doc-container">
	@include('lemon.shopnc.header')
        <div class="doc-header">
            table <span class="intro">table 样式</span>
        </div>
	<h3 class="doc-title">table</h3>
	<div class="mt10 clearfix">
		<div class="span-24">
			<div class="doc-emmet">table.table>(tr.thead-space>th{表头}*6)+(tr>td{内容}*6)*4</div>
			<div class="doc-jsGen">
				<table class="table">
					<tr class="thead-space">
						<th>表头</th>
						<th>表头</th>
						<th>表头</th>
						<th>表头</th>
					</tr>
					<tr>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
					</tr>
					<tr>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
					</tr>
					<tr>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
					</tr>
					<tr>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="span-24">
			<h2>表格列表有样式</h2>
			<div class="doc-emmet">table.table.prompt>(tr.thead-space>th{表头}*6)+(tr>td{内容}*6)*4</div>
			<div class="doc-jsGen">
				<table class="table prompt">
					<tr class="thead-space">
						<th>表头</th>
						<th>表头</th>
						<th>表头</th>
						<th>表头</th>
					</tr>
					<tr>
						<td>
							<ul>
								<li>title1</li>
								<li>title2</li>
								<li>title3</li>
								<li>title4</li>
								<li>title5</li>
							</ul>
						</td>
						<td>内容</td>
						<td>内容</td>
						<td>内容</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	</div>
@endsection
<p class="alert alert-info" id="introduce">
	这里填写简单的源码示例
</p>
<hr>
<div class="row" id="code">
	<div class="col-md-12">
		<h4>代码示例</h4>
		<pre id="J_html">

        </pre>
		<pre id="J_script"></pre>
	</div>
</div>
<script id="J_scriptSource">
	require(['centrifuge', 'sockjs'], function (Centrifuge) {
		var centrifuge = new Centrifuge({
			url: 'http://localhost:1942',
			user: "1234",
			timestamp: "1518011936",
			token: "d4e877120326b79308f3bddcfad094ab8f9ba93b0fba0fdf62adebde659d0578"
		});

		centrifuge.subscribe("news", function(message) {
			console.log(message);
		});

		centrifuge.connect();
	})
</script>

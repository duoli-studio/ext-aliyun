<?php namespace Poppy\Framework\GraphQL;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GraphQLController extends Controller
{
	public function __construct(Request $request)
	{
		$route = $request->route();

		// Prevent schema middlewares to be applied to graphiql routes
		$routeName = is_object($route) ? $route->getName() : null;
		if (!is_null($routeName) && preg_match('/^graphql\.graphiql/', $routeName)) {
			return;
		}

		$defaultSchema = config('graphql.schema');
		if (is_array($route)) {
			$schema = array_get($route, '2.graphql_schema', $defaultSchema);
		}
		elseif (is_object($route)) {
			$schema = $route->parameter('graphql_schema', $defaultSchema);
		}
		else {
			$schema = $defaultSchema;
		}

		$middleware = config('graphql.middleware_schema.' . $schema, null);

		if ($middleware) {
			$this->middleware($middleware);
		}
	}

	public function query(Request $request, $schema = null)
	{
		$isBatch = !$request->has('query');
		$inputs  = $request->all();

		if (!$schema) {
			$schema = config('graphql.schema');
		}

		if (!$isBatch) {
			$data = $this->executeQuery($schema, $inputs);
		}
		else {
			$data = [];
			foreach ($inputs as $input) {
				$data[] = $this->executeQuery($schema, $input);
			}
		}

		$headers = config('graphql.headers', []);
		$options = config('graphql.json_encoding_options', 0);

		$errors     = !$isBatch ? array_get($data, 'errors', []) : [];
		$authorized = array_reduce($errors, function ($authorized, $error) {
			return !$authorized || array_get($error, 'message') === 'Unauthorized' ? false : true;
		}, true);
		if (!$authorized) {
			return response()->json($data, 403, $headers, $options);
		}

		return response()->json($data, 200, $headers, $options);
	}

	public function graphiql(Request $request, $schema = null)
	{
		$view = config('graphql.graphiql.view', 'graphql::graphiql');
		return view($view, [
			'schema' => $schema,
		]);
	}

	protected function executeQuery($schema, $input)
	{
		$variablesInputName = config('graphql.variables_input_name', 'variables');
		$query              = array_get($input, 'query');
		$variables          = array_get($input, $variablesInputName);
		if (is_string($variables)) {
			$variables = json_decode($variables, true);
		}
		$operationName = array_get($input, 'operationName');
		$context       = $this->queryContext($query, $variables, $schema);
		return app('graphql')->query($query, $variables, [
			'context'       => $context,
			'schema'        => $schema,
			'operationName' => $operationName,
		]);
	}

	protected function queryContext($query, $variables, $schema)
	{
		try {
			return app('auth')->user();
		} catch (\Exception $e) {
			return null;
		}
	}
}

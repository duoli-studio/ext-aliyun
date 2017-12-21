<?php namespace Poppy\Extension\Log\Http;

use Poppy\Extension\Log\Helper\L5Log;


class L5LogController
{

	public function index()
	{
		if (\Input::get('l')) {
			L5Log::setFile(base64_decode(\Input::get('l')));
		}

		if (\Input::get('dl')) {
			return \Response::download(storage_path() . '/logs/' . base64_decode(\Input::get('dl')));
		}
		elseif (\Input::has('del')) {
			\File::delete(storage_path() . '/logs/' . base64_decode(\Input::get('del')));
			return \Redirect::to(\Request::url());
		}


		$logs = L5Log::all();

		return view('l5-log::index', [
			'logs'         => $logs,
			'files'        => L5Log::getFiles(true),
			'current_file' => L5Log::getFileName(),
		]);
	}
}


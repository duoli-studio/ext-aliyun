<?php namespace System\Request\Develop;

use System\Classes\LogViewer;

class LogController extends InitController
{
	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function index()
	{
		if (\Input::get('l')) {
			LogViewer::setFile(base64_decode(\Input::get('l')));
		}

		if (\Input::get('dl')) {
			return \Response::download(storage_path() . '/logs/' . base64_decode(\Input::get('dl')));
		}
		elseif (\Input::has('del')) {
			\File::delete(storage_path() . '/logs/' . base64_decode(\Input::get('del')));

			return \Redirect::to(\Request::url());
		}

		$logs = LogViewer::all();

		return view('system::develop.log.index', [
			'logs'         => $logs,
			'files'        => LogViewer::getFiles(true),
			'current_file' => LogViewer::getFileName(),
		]);
	}
}


<?php namespace Order\Request\Web\Controllers;


/**
 * 命令行调用
 * Class ArtisanController
 * @package App\Http\Controllers\Support
 */
class ArtisanController extends Controller {


	public function getSyncDetailCrontab() {
		$cacheName = cache_name(__CLASS__, '_sync_detail_interval');
		$cache     = \Cache::get($cacheName);
		if (!$cache || ($cache && ($this->time - $cache) > 20)) {
			\Artisan::call('lemon:platform-sync-detail');
			$output = \Artisan::output();
			$disk   = \Storage::disk('storage');
			$disk->append('console/platform.log', $output);
			\Cache::forever($cacheName, LmEnv::time());
		}
	}
}

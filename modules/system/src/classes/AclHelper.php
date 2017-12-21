<?php namespace System\Classes;

use Poppy\Framework\Helper\CacheHelper;
use Poppy\Framework\Helper\FileHelper;
use Illuminate\Cache\TaggableStore;
use System\Models\PamAccount;


/**
 * 权限控制
 */
class AclHelper
{

	/**
	 * 获取缓存
	 * @param string      $slug  类型
	 * @param null|string $route 路由名称
	 * @return mixed
	 */
	public static function getTitleCache($slug, $route = null)
	{
		$cacheKey = CacheHelper::name(__CLASS__, 'title_' . $slug);

		if (!\Cache::has($cacheKey)) {
			$cacheData = self::permission($slug);
			$links     = [];
			if (is_array($cacheData)) {
				foreach ($cacheData as $key => $ctl) {
					$links[$key] = isset($ctl['title']) ? $ctl['title'] : '';
				}
			}
			\Cache::forever($cacheKey, $links);
		}
		$cache = \Cache::get($cacheKey);

		return kv($cache, $route);
	}


	public static function getPermissionCache($slug)
	{
		$cacheKey = CacheHelper::name(__CLASS__, 'permission_' . $slug);

		if (!\Cache::has($cacheKey)) {
			$cacheData = self::permission($slug);
			$links     = [];
			if (is_array($cacheData)) {
				foreach ($cacheData as $key => $ctl) {
					$links[$key] = true;
				}
			}
			\Cache::forever($cacheKey, $links);
		}
		$cache = \Cache::get($cacheKey);
		return $cache;
	}

	/**
	 * 获取菜单
	 * @param string     $slug
	 * @param PamAccount $user
	 * @param bool|true  $is_menu
	 * @return mixed|string
	 */
	public static function getMenuCache($slug, $user = null, $is_menu = true)
	{
		$tag = CacheHelper::name(__CLASS__, 'menu');
		$key = CacheHelper::name(__CLASS__, (is_null($user) ? 'menu' : 'menu_' . $user->account_name) . ('_is_menu_' . (int) $is_menu));

		// menu cache
		if (\Cache::getStore() instanceof TaggableStore) {
			return \Cache::tags($tag)->remember($key, \Config::get('cache.ttl'), function () use ($slug, $user, $is_menu) {
				return self::menu($slug, $user, $is_menu);
			});
		}
		else {
			return \Cache::remember($key, \Config::get('cache.ttl'), function () use ($slug, $user, $is_menu) {
				return self::menu($slug, $user, $is_menu);
			});
		}
	}

	public static function reCache()
	{
		$types = PamAccount::kvAccountType();
		foreach ($types as $index => $type) {
			\Cache::forget(CacheHelper::name(__CLASS__, 'title_' . $index));
			\Cache::forget(CacheHelper::name(__CLASS__, 'permission_' . $index));
		}

		// menu cache
		if (\Cache::getStore() instanceof TaggableStore) {
			$tag = CacheHelper::name(__CLASS__, 'menu');
			\Cache::tags($tag)->flush();
		}
	}

	/**
	 * 获取菜单
	 * @param string     $slug
	 * @param PamAccount $user
	 * @param bool|true  $is_menu
	 * @return mixed|string
	 */
	public static function menu($slug, $user = null, $is_menu = true)
	{
		if (!$slug) {
			return [];
		}
		// define file
		$file = self::rbacPath($slug) . '/_config.yaml';

		if (!is_file($file)) {
			return false;
		}

		// 菜单项目配置
		$menu = app('poppy.parse.yaml')->parse(FileHelper::get($file));
		if (!is_array($menu)) {
			return false;
		}


		// 子目录扫描并获取可以操作的项目
		$typeData  = [];
		$typeFiles = FileHelper::subFile(self::rbacPath($slug));
		if (is_array($typeFiles) && !empty($typeFiles)) {
			foreach ($typeFiles as $f) {
				$key            = basename($f, '.php');
				$typeData[$key] = self::operation($slug, $key, $user, $is_menu, false);
			}
		}

		// 格式化菜单项目
		foreach ($menu as $menu_group => &$group) {
			$menuLink   = [];
			$flat_links = [];

			if (is_array($group['groups']) && count($group['groups'])) {
				foreach ($group['groups'] as $route) {
					if (!isset($typeData[$route]) || empty($typeData[$route])) {
						continue;
					}
					$menuLink[$route] = $typeData[$route];
					foreach ($menuLink[$route]['links'] as &$link) {
						$link['url']                = (isset($link['is_url']) && $link['is_url']) ? $link['url'] : route_url($link['route'], null, $link['param']);
						$flat_links[$link['route']] = $link['url'];
					}
				}
			}

			$group['icon']       = isset($group['icon']) ? $group['icon'] : '';
			$group['links']      = $menuLink;
			$group['flat_links'] = $flat_links;
			$group['link_count'] = count($flat_links);

			if ($flat_links) {
				$firstLink    = current($flat_links);
				$group['url'] = $firstLink;
			}
		}

		// menu with
		// 可以通过 menu_with 属性来合并多个控制器, 便于组织
		foreach ($menu as $m_group_name => &$m_group) {
			foreach ($m_group['links'] as $m_route_name => $m_route) {
				if (isset($m_route['menu_with']) && $m_route['menu_with']) {
					if (
						isset($m_group['links'][$m_route['menu_with']])
						&&
						isset($m_group['links'][$m_route['menu_with']]['links'])
					) {
						$m_group['links'][$m_route['menu_with']]['links'] = array_merge($m_group['links'][$m_route['menu_with']]['links'], $m_route['links']);
						unset($m_group['links'][$m_route_name]);
					}
				}
			}
		}

		return $menu;
	}


	/**
	 * 获取子菜单
	 * @param      $menus
	 * @param null $route
	 * @return array
	 */
	public static function subMenu($menus, $route = null)
	{
		if (!$route) {
			return [];
		}
		$subMenu = [];
		foreach ($menus as $group => $menu) {
			if (in_array($route, array_keys($menu['flat_links']))) {
				$menu['group'] = $group;
				$subMenu       = $menu;
			}
		}

		if (isset($subMenu['links']) && is_array($subMenu['links'])) {
			foreach ($subMenu['links'] as $k => &$sub) {
				if (count($sub['links'])) {
					if (in_array($route, array_keys($sub['links']))) {
						$current = current($sub['links']);
						if (isset($current['url'])) {
							$sub['url'] = $current['url'];
						}
						else {
							$sub['url'] = '';
						}

						$subMenu['current_menu'] = $sub;
						$sub['active']           = ' active ';
						$sub['open']             = ' open ';
					}
					else {
						$sub['active'] = ' ';
						$sub['open']   = ' ';
					}
					foreach ($sub['links'] as $key => &$link) {
						if ($key == $route) {
							$subMenu['current_link'] = $link;
							$link['active']          = ' active ';
						}
						else {
							$link['active'] = ' ';
						}
					}
				}
				else {
					unset($subMenu['links'][$k]);
				}

			}
		}
		return $subMenu;
	}


	/**
	 * 权限
	 * @param string $slug
	 * @param null   $user
	 * @return array|bool
	 */
	public static function permission($slug, $user = null)
	{
		$dir = self::rbacPath($slug);
		if (!is_dir($dir)) {
			return false;
		}
		// 子目录扫描并获取可以操作的项目
		$typeData  = [];
		$typeFiles = FileHelper::subFile($dir);
		if (is_array($typeFiles) && !empty($typeFiles)) {
			foreach ($typeFiles as $f) {
				$ext = pathinfo($f, PATHINFO_EXTENSION);
				if ($ext != 'php') {
					continue;
				}
				$key       = basename($f, '.php');
				$operation = self::operation($slug, $key, $user, false, true);
				$typeData  = array_merge($typeData, $operation['links']);
			}
		}
		return $typeData;
	}

	/**
	 * 返回 rbac 权限的路径
	 * @param  string $slug
	 * @return string
	 */
	private static function rbacPath($slug)
	{
		return poppy_path($slug, 'Resources/Rbac');
	}

	/**
	 * 根据类型/ 路由 获取定义的数据
	 * @param string     $slug
	 * @param string     $filename
	 * @param PamAccount $user
	 * @param bool|true  $is_menu
	 * @param bool       $with_permission 是否包含权限
	 * @return array
	 */
	private static function operation($slug, $filename, $user = null, $is_menu = true, $with_permission = false)
	{

		$filePath = self::rbacPath($slug) . '/' . $filename . '.php';

		$define = [];
		if (file_exists($filePath)) {
			$define = FileHelper::readPhp($filePath);
		}
		else {
			return $define;
		}

		$acl = [
			'title'       => $define['title'],
			'icon'        => isset($define['icon']) ? $define['icon'] : '',
			'description' => isset($define['description']) ? $define['description'] : '',
			'menu_with'   => isset($define['menu_with']) ? $define['menu_with'] : '',
		];

		if (isset($define['operation'])) {
			$acl['links'] = [];

			foreach ($define['operation'] as $op_key => $op_define) {
				// 剔除非菜单项目

				if ($is_menu) {
					if (!isset($op_define['menu']) || !$op_define['menu'] || $op_define['menu'] == false) {
						continue;
					}
				}

				if (!$with_permission) { // 不包含权限
					if (isset($op_define['permission']) && $op_define['permission'] == true) {
						continue;
					}
				}

				// 组合路由
				$route = $define['route'] . '.' . $op_key;

				if ($user && !$user->capable($route)) {
					continue;
				}

				$singleDefine = [
					'title'       => $op_define['title'],
					'group_title' => $acl['title'],
					'route'       => $route,
					'description' => isset($op_define['description']) ? $op_define['description'] : '',
					'menu'        => (isset($op_define['menu']) && $op_define['menu']) ? $op_define['menu'] : false,
					'param'       => isset($op_define['param']) ? $op_define['param'] : '',
					'icon'        => isset($op_define['icon']) ? $op_define['icon'] : '',
				];

				if (isset($op_define['url'])) {
					$singleDefine['url']    = $op_define['url'];
					$singleDefine['is_url'] = 1;
				}

				$acl['links'][$route] = $singleDefine;
			}
		}

		return $acl;

	}
}
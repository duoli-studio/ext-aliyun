<?php

namespace System\Extension\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Poppy\Framework\Routing\Abstracts\Controller;
use Poppy\Framework\Validation\Rule;

/**
 * Class ExtensionsController.
 */
class ExtensionsController extends Controller
{
    /**
     * @var bool
     */
    protected $onlyValues = true;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function install(): JsonResponse
    {
        list($identification) = $this->validate($this->request, [
            'identification' => Rule::required(),
        ], [
            'identification.required' => '拓展标识必须填写',
        ]);
        if (!$this->extension->has($identification)) {
            return $this->response->json([
                'message' => '拓展不存在！',
            ])->setStatusCode(500);
        }
        $key = 'extension.' . $identification . '.require.install';
        $this->setting->set($key, true);
        $this->cache->tags('notadd')->flush();

        return $this->response->json([
            'message' => '添加到待安装列表成功!',
        ]);
    }

    /**
     * @param $identification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uninstall($identification): JsonResponse
    {
        $identification = Str::replaceFirst('-', '/', $identification);
        if (!$this->extension->has($identification)) {
            return $this->response->json([
                'message' => '拓展不存在！',
            ])->setStatusCode(500);
        }
        $key = 'extension.' . $identification . '.require.uninstall';
        $this->setting->set($key, true);
        $this->cache->tags('notadd')->flush();

        return $this->response->json([
            'message' => '添加到待安装列表成功!',
        ]);
    }
}

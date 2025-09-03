<?php

namespace App\Http\Controllers;

use App\Services\Installer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class InstallController extends Controller
{
    /**
     * 显示安装页面
     */
    public function index()
    {
        // 检查环境
        $environmentChecks = Installer::checkEnvironment();
        $page_title = '安装';
        
        return view('install.index', compact('environmentChecks', 'page_title'));
    }
    
    /**
     * 执行安装
     */
    public function install(Request $request)
    {
        // 验证表单数据
        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|integer',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'redis_host' => 'required|string',
            'redis_port' => 'required|integer',
            'redis_password' => 'nullable|string',
            'title' => 'required|string',
            'app_url' => 'required|url',
            'admin_path' => 'required|string|regex:/^\/[a-zA-Z0-9_-]+$/',
        ]);
        
        // 测试数据库连接
        $dbTestResult = Installer::testDatabase([
            'host' => $validated['db_host'],
            'port' => $validated['db_port'],
            'database' => $validated['db_database'],
            'username' => $validated['db_username'],
            'password' => $validated['db_password'] ?? '',
        ]);
        
        if ($dbTestResult !== true) {
            $errorMessage = '数据库连接失败: ' . ($dbTestResult['error'] ?? '请检查配置');
            return response()->json(['error' => $errorMessage], 400);
        }
        
        // 测试Redis连接
        if (!Installer::testRedis([
            'host' => $validated['redis_host'],
            'port' => $validated['redis_port'],
            'password' => $validated['redis_password'] ?? '',
        ])) {
            return response()->json(['error' => 'Redis连接失败，请检查配置'], 400);
        }
        $validated['cache_driver'] = 'redis';
        $validated['queue_connection'] = 'redis';
        
        // 执行安装
        $result = Installer::install($validated);
        
        if ($result === 'success') {
            return response()->json(['success' => true, 'message' => '安装成功！']);
        }
        
        return response()->json(['error' => $result], 500);
    }
}
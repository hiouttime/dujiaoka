<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfigService
{
    protected array $config = [];
    protected bool $loaded = false;

    public function get(string $key, $default = null)
    {
        if (!$this->loaded) {
            $this->loadConfig();
        }

        return data_get($this->config, $key, $default);
    }

    public function set(string $key, $value): void
    {
        data_set($this->config, $key, $value);
        $this->saveConfig();
    }

    public function all(): array
    {
        if (!$this->loaded) {
            $this->loadConfig();
        }

        return $this->config;
    }

    protected function loadConfig(): void
    {
        $this->config = Cache::get('system-setting', []);
        
        // Redis 没数据时从数据库读取一次
        if (empty($this->config)) {
            try {
                $settings = DB::table('settings')->get();
                $config = [];
                
                foreach ($settings as $setting) {
                    $payload = json_decode($setting->payload, true);
                    // 构建嵌套结构：config[group][name] = payload
                    if (!isset($config[$setting->group])) {
                        $config[$setting->group] = [];
                    }
                    $config[$setting->group][$setting->name] = $payload;
                }
                
                $this->config = $config;
                if (!empty($config)) {
                    Cache::put('system-setting', $config);
                }
            } catch (\Exception $e) {
                $this->config = [];
            }
        }
        
        $this->loaded = true;
    }

    protected function saveConfig(): void
    {
        Cache::put('system-setting', $this->config);
    }

    public function refresh(): void
    {
        Cache::forget('system-setting');
        $this->loaded = false;
        $this->config = [];
    }
}
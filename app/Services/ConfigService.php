<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

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
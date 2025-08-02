<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Themes\ThemeManager;

class ThemeCommand extends Command
{
    protected $signature = 'theme:manage {action?} {theme?}';
    protected $description = '管理主题';

    public function handle()
    {
        $action = $this->argument('action') ?: 'list';
        $themeManager = app(ThemeManager::class);

        switch ($action) {
            case 'list':
                $this->listThemes($themeManager);
                break;
            
            case 'info':
                $theme = $this->argument('theme');
                if (!$theme) {
                    $this->error('请指定主题名称');
                    return 1;
                }
                $this->showThemeInfo($themeManager, $theme);
                break;
            
            case 'activate':
                $theme = $this->argument('theme');
                if (!$theme) {
                    $this->error('请指定主题名称');
                    return 1;
                }
                $this->activateTheme($themeManager, $theme);
                break;
            
            case 'compile':
                $theme = $this->argument('theme') ?: $themeManager->getActiveTheme();
                $this->compileTheme($themeManager, $theme);
                break;
            
            default:
                $this->error("未知操作: {$action}");
                return 1;
        }

        return 0;
    }

    protected function listThemes(ThemeManager $themeManager): void
    {
        $themes = $themeManager->getAllThemes();
        $activeTheme = $themeManager->getActiveTheme();
        
        $this->info('可用主题:');
        $rows = [];
        
        foreach ($themes as $name => $theme) {
            $info = $themeManager->getThemeInfo($name);
            $rows[] = [
                $name === $activeTheme ? "✓ {$name}" : $name,
                $info['display_name'] ?? $name,
                $info['version'] ?? 'N/A',
                $info['author'] ?? 'N/A'
            ];
        }
        
        $this->table(['主题名称', '显示名称', '版本', '作者'], $rows);
    }

    protected function showThemeInfo(ThemeManager $themeManager, string $themeName): void
    {
        if (!$themeManager->hasTheme($themeName)) {
            $this->error("主题 [{$themeName}] 不存在");
            return;
        }

        $info = $themeManager->getThemeInfo($themeName);
        
        $this->info("主题信息: {$themeName}");
        $this->line("显示名称: " . ($info['display_name'] ?? $themeName));
        $this->line("描述: " . ($info['description'] ?? 'N/A'));
        $this->line("版本: " . ($info['version'] ?? 'N/A'));
        $this->line("作者: " . ($info['author'] ?? 'N/A'));
        
        if (isset($info['tags'])) {
            $this->line("标签: " . implode(', ', $info['tags']));
        }
        
        $configFields = $themeManager->getThemeConfigFields($themeName);
        if (!empty($configFields)) {
            $this->line("配置选项数量: " . count($configFields));
        }
    }

    protected function activateTheme(ThemeManager $themeManager, string $themeName): void
    {
        if (!$themeManager->hasTheme($themeName)) {
            $this->error("主题 [{$themeName}] 不存在");
            return;
        }

        try {
            $themeManager->setActiveTheme($themeName);
            $this->info("主题 [{$themeName}] 已激活");
        } catch (\Exception $e) {
            $this->error("激活主题失败: " . $e->getMessage());
        }
    }

    protected function compileTheme(ThemeManager $themeManager, string $themeName): void
    {
        if (!$themeManager->hasTheme($themeName)) {
            $this->error("主题 [{$themeName}] 不存在");
            return;
        }

        try {
            $this->info("正在编译主题资源...");
            $themeManager->compileThemeAssets($themeName);
            $this->info("主题 [{$themeName}] 资源编译完成");
        } catch (\Exception $e) {
            $this->error("编译主题失败: " . $e->getMessage());
        }
    }
}
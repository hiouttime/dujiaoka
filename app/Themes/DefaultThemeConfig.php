<?php

namespace App\Themes;

use App\Themes\Contracts\ThemeConfigInterface;
use Illuminate\Support\Facades\File;

/**
 * 默认主题配置类
 * 为没有自定义配置的主题提供基础配置支持
 */
class DefaultThemeConfig implements ThemeConfigInterface
{
    protected string $themeName;

    public function __construct(string $themeName)
    {
        $this->themeName = $themeName;
    }

    /**
     * 获取主题配置选项
     */
    public function getConfigFields(): array
    {
        return [
            'general' => [
                'title' => '基础设置',
                'fields' => [
                    'logo' => [
                        'type' => 'image',
                        'label' => '网站Logo',
                        'description' => '建议尺寸: 200x60像素',
                        'accept' => 'image/*',
                        'max_size' => '2MB'
                    ],
                    'favicon' => [
                        'type' => 'image',
                        'label' => '网站图标',
                        'description' => '建议尺寸: 32x32像素，格式: ico/png',
                        'accept' => 'image/*',
                        'max_size' => '1MB'
                    ],
                    'primary_color' => [
                        'type' => 'color',
                        'label' => '主题色',
                        'description' => '网站主要颜色',
                        'default' => '#007bff'
                    ],
                    'secondary_color' => [
                        'type' => 'color',
                        'label' => '辅助色',
                        'description' => '网站辅助颜色',
                        'default' => '#6c757d'
                    ]
                ]
            ],
            'layout' => [
                'title' => '布局设置',
                'fields' => [
                    'header_style' => [
                        'type' => 'select',
                        'label' => '头部样式',
                        'options' => [
                            'default' => '默认',
                            'minimal' => '简约',
                            'center' => '居中'
                        ],
                        'default' => 'default'
                    ],
                    'footer_text' => [
                        'type' => 'textarea',
                        'label' => '底部文字',
                        'description' => '支持HTML',
                        'rows' => 3
                    ]
                ]
            ]
        ];
    }

    /**
     * 获取主题默认配置值
     */
    public function getDefaultConfig(): array
    {
        $defaults = [];
        $fields = $this->getConfigFields();

        foreach ($fields as $section) {
            foreach ($section['fields'] as $key => $field) {
                if (isset($field['default'])) {
                    $defaults[$key] = $field['default'];
                }
            }
        }

        return $defaults;
    }

    /**
     * 验证配置值
     */
    public function validateConfig(array $config): array
    {
        $errors = [];
        $fields = $this->getConfigFields();

        foreach ($fields as $section) {
            foreach ($section['fields'] as $key => $field) {
                if (isset($config[$key])) {
                    $value = $config[$key];
                    
                    // 颜色验证
                    if ($field['type'] === 'color' && !preg_match('/^#[0-9a-fA-F]{6}$/', $value)) {
                        $errors[$key] = "{$field['label']} 格式不正确";
                    }
                    
                    // 图片文件验证
                    if ($field['type'] === 'image' && !empty($value)) {
                        if (!File::exists(public_path($value))) {
                            $errors[$key] = "{$field['label']} 文件不存在";
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * 获取主题信息
     */
    public function getThemeInfo(): array
    {
        return [
            'display_name' => ucfirst($this->themeName),
            'description' => "默认 {$this->themeName} 主题",
            'version' => '1.0.0',
            'author' => 'DuJiaoKa',
            'screenshot' => "/themes/{$this->themeName}/screenshot.png"
        ];
    }

    /**
     * 处理配置值
     */
    public function processConfig(array $config): array
    {
        // 处理文件上传等逻辑
        return $config;
    }
}
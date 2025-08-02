<?php

namespace App\Themes\Configs;

use App\Themes\Contracts\ThemeConfigInterface;

/**
 * Neon主题配置
 * 为Neon主题提供专门的配置选项
 */
class NeonThemeConfig implements ThemeConfigInterface
{
    /**
     * 获取主题配置选项
     */
    public function getConfigFields(): array
    {
        return [
            'branding' => [
                'title' => '品牌设置',
                'icon' => 'heroicon-o-home',
                'fields' => [
                    'logo' => [
                        'type' => 'image',
                        'label' => '网站Logo',
                        'description' => '建议尺寸: 200x60像素，支持PNG/JPG格式',
                        'accept' => 'image/*',
                        'max_size' => '2MB'
                    ],
                    'logo_dark' => [
                        'type' => 'image',
                        'label' => '深色模式Logo',
                        'description' => '深色模式下显示的Logo',
                        'accept' => 'image/*',
                        'max_size' => '2MB'
                    ],
                    'favicon' => [
                        'type' => 'image',
                        'label' => '网站图标',
                        'description' => '建议尺寸: 32x32像素',
                        'accept' => '.ico,.png',
                        'max_size' => '1MB'
                    ],
                    'site_name' => [
                        'type' => 'text',
                        'label' => '网站名称',
                        'description' => '显示在标题栏的网站名称'
                    ]
                ]
            ],
            'colors' => [
                'title' => '颜色主题',
                'icon' => 'heroicon-o-eye-dropper',
                'fields' => [
                    'primary_color' => [
                        'type' => 'color',
                        'label' => '主色调',
                        'description' => '网站主要按钮和链接颜色',
                        'default' => '#6366f1'
                    ],
                    'secondary_color' => [
                        'type' => 'color',
                        'label' => '次色调',
                        'description' => '辅助元素颜色',
                        'default' => '#ec4899'
                    ],
                    'accent_color' => [
                        'type' => 'color',
                        'label' => '强调色',
                        'description' => '用于高亮和提示的颜色',
                        'default' => '#10b981'
                    ],
                    'background_gradient' => [
                        'type' => 'gradient',
                        'label' => '背景渐变',
                        'description' => '页面背景渐变效果',
                        'default' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                    ]
                ]
            ],
            'layout' => [
                'title' => '布局设置',
                'icon' => 'heroicon-o-squares-plus',
                'fields' => [
                    'header_style' => [
                        'type' => 'select',
                        'label' => '头部样式',
                        'options' => [
                            'default' => '默认样式',
                            'minimal' => '简约样式',
                            'centered' => '居中样式',
                            'transparent' => '透明样式'
                        ],
                        'default' => 'default'
                    ],
                    'navigation_style' => [
                        'type' => 'select',
                        'label' => '导航样式',
                        'options' => [
                            'horizontal' => '水平导航',
                            'sidebar' => '侧边栏导航',
                            'dropdown' => '下拉菜单'
                        ],
                        'default' => 'horizontal'
                    ],
                    'card_style' => [
                        'type' => 'select',
                        'label' => '卡片样式',
                        'options' => [
                            'default' => '默认卡片',
                            'rounded' => '圆角卡片',
                            'shadow' => '阴影卡片',
                            'neon' => '霓虹效果'
                        ],
                        'default' => 'neon'
                    ],
                    'animation_enabled' => [
                        'type' => 'switch',
                        'label' => '启用动画',
                        'description' => '开启页面过渡动画效果',
                        'default' => true
                    ]
                ]
            ],
            'hero_section' => [
                'title' => '首页横幅',
                'icon' => 'heroicon-o-photo',
                'fields' => [
                    'hero_title' => [
                        'type' => 'text',
                        'label' => '主标题',
                        'description' => '首页大标题文字',
                        'default' => '欢迎来到我们的商店'
                    ],
                    'hero_subtitle' => [
                        'type' => 'text',
                        'label' => '副标题',
                        'description' => '首页副标题文字',
                        'default' => '为您提供优质的数字商品服务'
                    ],
                    'hero_background' => [
                        'type' => 'image',
                        'label' => '背景图片',
                        'description' => '首页横幅背景图，建议尺寸: 1920x1080',
                        'accept' => 'image/*',
                        'max_size' => '5MB'
                    ],
                    'hero_overlay_opacity' => [
                        'type' => 'range',
                        'label' => '背景透明度',
                        'description' => '背景图片的透明度',
                        'min' => 0,
                        'max' => 100,
                        'default' => 50
                    ],
                    'cta_button_text' => [
                        'type' => 'text',
                        'label' => '按钮文字',
                        'description' => '行动按钮的文字',
                        'default' => '立即购买'
                    ],
                    'cta_button_link' => [
                        'type' => 'text',
                        'label' => '按钮链接',
                        'description' => '行动按钮的跳转链接',
                        'default' => '#products'
                    ]
                ]
            ],
            'features' => [
                'title' => '功能特性',
                'icon' => 'heroicon-o-star',
                'fields' => [
                    'show_features' => [
                        'type' => 'switch',
                        'label' => '显示特性区块',
                        'description' => '在首页显示功能特性介绍',
                        'default' => true
                    ],
                    'features_title' => [
                        'type' => 'text',
                        'label' => '特性标题',
                        'default' => '我们的优势'
                    ],
                    'features_list' => [
                        'type' => 'repeater',
                        'label' => '特性列表',
                        'description' => '添加网站特性说明',
                        'fields' => [
                            'icon' => [
                                'type' => 'icon',
                                'label' => '图标',
                                'description' => 'FontAwesome图标类名'
                            ],
                            'title' => [
                                'type' => 'text',
                                'label' => '标题'
                            ],
                            'description' => [
                                'type' => 'textarea',
                                'label' => '描述',
                                'rows' => 2
                            ]
                        ],
                        'default' => [
                            [
                                'icon' => 'heroicon-o-lightning-bolt',
                                'title' => '极速发货',
                                'description' => '自动化发货系统，下单即发货'
                            ],
                            [
                                'icon' => 'heroicon-o-shield-check',
                                'title' => '安全保障',
                                'description' => '多重安全验证，保护您的权益'
                            ],
                            [
                                'icon' => 'heroicon-o-phone',
                                'title' => '24小时客服',
                                'description' => '专业客服团队，随时为您服务'
                            ]
                        ]
                    ]
                ]
            ],
            'footer' => [
                'title' => '页脚设置',
                'icon' => 'heroicon-o-minus',
                'fields' => [
                    'footer_text' => [
                        'type' => 'textarea',
                        'label' => '版权信息',
                        'description' => '页脚版权文字，支持HTML',
                        'rows' => 3,
                        'default' => '© 2024 DuJiaoKa. All rights reserved.'
                    ],
                    'footer_links' => [
                        'type' => 'repeater',
                        'label' => '页脚链接',
                        'fields' => [
                            'title' => [
                                'type' => 'text',
                                'label' => '链接文字'
                            ],
                            'url' => [
                                'type' => 'text',
                                'label' => '链接地址'
                            ],
                            'target' => [
                                'type' => 'select',
                                'label' => '打开方式',
                                'options' => [
                                    '_self' => '当前窗口',
                                    '_blank' => '新窗口'
                                ],
                                'default' => '_self'
                            ]
                        ]
                    ],
                    'social_links' => [
                        'type' => 'group',
                        'label' => '社交媒体',
                        'fields' => [
                            'weibo' => [
                                'type' => 'text',
                                'label' => '微博链接'
                            ],
                            'wechat' => [
                                'type' => 'image',
                                'label' => '微信二维码'
                            ],
                            'qq' => [
                                'type' => 'text',
                                'label' => 'QQ号'
                            ],
                            'telegram' => [
                                'type' => 'text',
                                'label' => 'Telegram链接'
                            ]
                        ]
                    ]
                ]
            ],
            'advanced' => [
                'title' => '高级设置',
                'icon' => 'heroicon-o-cog',
                'fields' => [
                    'custom_css' => [
                        'type' => 'code',
                        'label' => '自定义CSS',
                        'description' => '添加自定义样式代码',
                        'language' => 'css',
                        'rows' => 10
                    ],
                    'custom_js' => [
                        'type' => 'code',
                        'label' => '自定义JavaScript',
                        'description' => '添加自定义脚本代码',
                        'language' => 'javascript',
                        'rows' => 10
                    ],
                    'google_analytics' => [
                        'type' => 'text',
                        'label' => 'Google Analytics ID',
                        'description' => '如: GA_MEASUREMENT_ID'
                    ],
                    'custom_meta' => [
                        'type' => 'textarea',
                        'label' => '自定义Meta标签',
                        'description' => '添加自定义的meta标签',
                        'rows' => 5
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
                
                // 处理嵌套字段的默认值
                if ($field['type'] === 'group' && isset($field['fields'])) {
                    foreach ($field['fields'] as $subKey => $subField) {
                        if (isset($subField['default'])) {
                            $defaults[$key][$subKey] = $subField['default'];
                        }
                    }
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
        
        // 颜色验证
        $colorFields = ['primary_color', 'secondary_color', 'accent_color'];
        foreach ($colorFields as $field) {
            if (isset($config[$field]) && !preg_match('/^#[0-9a-fA-F]{6}$/', $config[$field])) {
                $errors[$field] = '颜色格式不正确，请使用十六进制格式 (如: #ffffff)';
            }
        }

        // URL验证
        if (isset($config['cta_button_link']) && !empty($config['cta_button_link'])) {
            if (!filter_var($config['cta_button_link'], FILTER_VALIDATE_URL) && !str_starts_with($config['cta_button_link'], '#')) {
                $errors['cta_button_link'] = '链接格式不正确';
            }
        }

        // 范围验证
        if (isset($config['hero_overlay_opacity'])) {
            $opacity = (int)$config['hero_overlay_opacity'];
            if ($opacity < 0 || $opacity > 100) {
                $errors['hero_overlay_opacity'] = '透明度必须在0-100之间';
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
            'display_name' => 'Neon 霓虹主题',
            'description' => '现代化的霓虹风格主题，支持深色模式和丰富的自定义选项',
            'version' => '2.0.0',
            'author' => 'Riniba',
            'author_url' => 'https://github.com/riniba',
            'screenshot' => '/themes/neon/screenshot.png',
            'tags' => ['现代', '霓虹', '深色模式', '响应式'],
            'min_php_version' => '8.1',
            'requires' => [
                'laravel' => '^11.0'
            ]
        ];
    }

    /**
     * 处理配置值
     */
    public function processConfig(array $config): array
    {
        // 处理图片上传
        $imageFields = ['logo', 'logo_dark', 'favicon', 'hero_background'];
        foreach ($imageFields as $field) {
            if (isset($config[$field]) && is_uploaded_file($config[$field])) {
                // 这里处理文件上传逻辑
                // $config[$field] = $this->handleImageUpload($config[$field], $field);
            }
        }

        // 处理CSS压缩
        if (isset($config['custom_css'])) {
            $config['custom_css'] = trim($config['custom_css']);
        }

        return $config;
    }
}
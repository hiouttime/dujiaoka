<?php

namespace App\Themes\Contracts;

/**
 * 主题配置接口
 * 所有主题都应该实现此接口来提供配置选项
 */
interface ThemeConfigInterface
{
    /**
     * 获取主题配置选项
     * 返回配置字段的定义，用于在后台生成设置表单
     *
     * @return array
     */
    public function getConfigFields(): array;

    /**
     * 获取主题默认配置值
     *
     * @return array
     */
    public function getDefaultConfig(): array;

    /**
     * 验证配置值
     *
     * @param array $config
     * @return array 验证错误信息，空数组表示验证通过
     */
    public function validateConfig(array $config): array;

    /**
     * 获取主题信息
     *
     * @return array
     */
    public function getThemeInfo(): array;

    /**
     * 处理配置值（例如文件上传、图片处理等）
     *
     * @param array $config
     * @return array 处理后的配置值
     */
    public function processConfig(array $config): array;
}
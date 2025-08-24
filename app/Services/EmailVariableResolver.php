<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;

class EmailVariableResolver
{
    public static function resolve(string $template, array $data = []): string
    {
        $template = self::parseVariables($template, $data);
        $template = self::parseFormatted($template, $data);
        $template = self::parseConditions($template, $data);
        return $template;
    }

    public static function createContext(Order $order): array
    {
        return [
            'site' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
                'email' => config('mail.from.address'),
                'year' => date('Y'),
            ],
            'order' => [
                'id' => $order->order_sn,
                'amount' => $order->actual_price,
                'quantity' => $order->total_quantity,
                'status' => self::getStatusText($order->status),
                'created_at' => $order->created_at,
                'goods_summary' => $order->goods_summary,
                'is_paid' => $order->status >= Order::STATUS_COMPLETED,
                'is_failed' => $order->status == Order::STATUS_FAILURE,
            ],
            'customer' => [
                'email' => $order->email,
                'name' => $order->user ? $order->user->nickname : '客户',
                'is_registered' => $order->user_id ? true : false,
            ],
        ];
    }

    private static function parseVariables(string $template, array $data): string
    {
        return preg_replace_callback('/\{\{([^|}]+)\}\}/', function ($matches) use ($data) {
            $path = trim($matches[1]);
            return self::getValue($data, $path) ?? $matches[0];
        }, $template);
    }

    private static function parseFormatted(string $template, array $data): string
    {
        return preg_replace_callback('/\{\{([^|}]+)\s*\|\s*([^}]+)\}\}/', function ($matches) use ($data) {
            $path = trim($matches[1]);
            $format = trim($matches[2]);
            $value = self::getValue($data, $path);
            return self::format($value, $format);
        }, $template);
    }

    private static function parseConditions(string $template, array $data): string
    {
        return preg_replace_callback('/\{\{#if\s+([^}]+)\}\}(.*?)\{\{\/if\}\}/s', function ($matches) use ($data) {
            $condition = trim($matches[1]);
            $content = $matches[2];
            $value = self::getValue($data, $condition);
            
            if (strpos($content, '{{else}}') !== false) {
                list($ifContent, $elseContent) = explode('{{else}}', $content, 2);
                return $value ? trim($ifContent) : trim($elseContent);
            }
            
            return $value ? trim($content) : '';
        }, $template);
    }

    private static function getValue(array $data, string $path)
    {
        $keys = explode('.', $path);
        $value = $data;
        
        foreach ($keys as $key) {
            if (is_array($value) && isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }
        
        return $value;
    }

    private static function format($value, string $format)
    {
        if ($value === null) return '';
        
        switch ($format) {
            case 'money':
                return number_format((float)$value, 2) . ' ¥';
            case 'date':
                return $value instanceof Carbon ? $value->format('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($value));
            default:
                return $value;
        }
    }

    private static function getStatusText(int $status): string
    {
        return match($status) {
            Order::STATUS_PENDING => '待处理',
            Order::STATUS_PROCESSING => '处理中', 
            Order::STATUS_COMPLETED => '已完成',
            Order::STATUS_FAILURE => '失败',
            default => '未知'
        };
    }
}
<?php
/**
 * The file was created by Assimon.
 *
 */

namespace App\Services;


use App\Models\Emailtpl;
use App\Models\Order;
use Carbon\Carbon;

class Email
{
    /**
     * 静态邮件模板映射
     */
    private static $templates = [
        'card_send_user_email' => '购买收据',
        'manual_send_manage_mail' => '新订单通知',
        'failed_order' => '订单失败',
        'completed_order' => '订单完成',
        'pending_order' => '订单待处理'
    ];

    public function getTemplate(string $token): Emailtpl
    {
        return cache()->remember("email_{$token}", 86400, function () use ($token) {
            $dbTemplate = Emailtpl::where('tpl_token', $token)->first();
            
            if (isset(self::$templates[$token])) {
                $template = $this->loadTemplate($token);
                if ($dbTemplate) {
                    $template->tpl_name = $dbTemplate->tpl_name;
                }
                return $template;
            }
            
            return $dbTemplate;
        });
    }

    private function loadTemplate(string $token): Emailtpl
    {
        $path = resource_path("email-templates/{$token}.html");
        $content = file_exists($path) ? file_get_contents($path) : '<p>模板不存在</p>';
        
        $template = new Emailtpl();
        $template->tpl_name = self::$templates[$token];
        $template->tpl_content = $content;
        $template->tpl_token = $token;
        
        return $template;
    }

    public static function getTemplates(): array
    {
        return self::$templates;
    }

    public function parse(string $template, Order $order): string
    {
        $context = EmailVariableResolver::createContext($order);
        return EmailVariableResolver::resolve($template, $context);
    }


    public function getEmail(string $token, Order $order): array
    {
        $template = $this->getTemplate($token);
        
        if (!$template) {
            throw new \Exception("模板 {$token} 不存在");
        }
        
        return [
            'title' => $this->parse($template->tpl_name, $order),
            'content' => $this->parse($template->tpl_content, $order),
        ];
    }
}

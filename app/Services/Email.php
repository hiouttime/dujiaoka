<?php
/**
 * The file was created by Assimon.
 *
 */

namespace App\Services;


use App\Models\Emailtpl;

class Email
{

    /**
     * 通过邮件标识获得邮件模板
     *
     * @param string $token 邮件标识
     * @return Emailtpl
     *
     */
    public function detailByToken(string $token): Emailtpl
    {
        $tpl = Emailtpl::query()->where('tpl_token', $token)->first();
        return $tpl;
    }

}

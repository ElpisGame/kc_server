<?php


namespace App\Http\Controllers\WechatEventHandlers;


use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Text;

class TextHandler implements EventHandlerInterface
{
    public function handle($payload = null)
    {
        return new Text("下单链接：https://user.chonglala.com");
    }
}

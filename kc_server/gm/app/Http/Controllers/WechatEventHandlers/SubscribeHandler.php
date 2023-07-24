<?php


namespace App\Http\Controllers\WechatEventHandlers;


use App\Models\User;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class SubscribeHandler implements EventHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle($payload = null)
    {
        $openid = $payload['FromUserName'];
        #检查是否为新用户
        $user = User::query()->where("openid", $openid)->first();
        if (empty($user)) {
            $user = new User;
            $user->uuid = Uuid::uuid4()->toString();
            $user->openid = $openid;
            $user->save();
        }
        if (!$user->uuid) {
            $user->uuid = Uuid::uuid4()->toString();
        }
        #获取微信的用户信息
        $app = Factory::officialAccount(config("wechat.officialAccount"));
        $response = $app->user->get($user->openid);
        Log::info(json_encode($response));
        $fields = collect($response)->only(["unionid", "nickname", "headimgurl", "language", "sex", "province", "city", "country"])->toArray();
        $user->update($fields);
        #保存二维码携带的内容到缓存
        if (isset($payload['EventKey'])) {
            $user->qrscene = $payload['EventKey'];
            if (Str::contains("account_", $user->qrscene)) {
                $account_id = explode("account_", $user->qrscene)[1];
                $user->account_id = $account_id;
                $user->save();
            } else {
                $uuid = $payload['EventKey'];
                Cache::put("qrcode.uuid.{$uuid}", $payload, now()->addMinutes(5));
            }
        }
        /*公众号给用户推送新消息*/
        $items = [
            new NewsItem([
                'title' => '立即下单',
                'description' => '',
                'url' => 'https://user.chonglala.com',
                'image' => 'https://mmbiz.qpic.cn/mmbiz_png/iabEibpW7vibUMG5uZQBCKh9uePXS5O6MaXmsrOswVHxFmhAFGYiaAJlc5PmCNialWdPyqJEOjDN72K85fNH5ISnDZQ/0?wx_fmt=png',
            ]),
            new NewsItem([
                'title' => '宠物可以快递吗？',
                'description' => '',
                'url' => 'https://baijiahao.baidu.com/s?id=1600412510441762992',
                'image' => 'https://mmbiz.qpic.cn/mmbiz_png/iabEibpW7vibUMG5uZQBCKh9uePXS5O6MaXZOSLOFh6g5I9SPg8Z2aR1t2QhJDP1N64xSvicSkOibUkbdVLNSFbLQ2w/0?wx_fmt=png',
            ]),
        ];
        $event = $payload['Event'];
        if ($event == "SCAN") {
            //推送到企业微信
            $data = [
                "msgtype" => "markdown",
                "markdown" => [
                    "content" => "## 宠拉拉扫码 > [{$user->id}] {$user->nickname} {$user->province}{$user->city}{$user->district}",
                    "mentioned_list" => ["@all"]
                ]
            ];
            Http::post("https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=26ecddfd-2e48-4195-97d8-9cf205d13ecb", $data);

            return new News($items);
        } else if ($event == "subscribe") {
            //推送到企业微信
            $data = [
                "msgtype" => "markdown",
                "markdown" => [
                    "content" => "## 宠拉拉关注 > [{$user->id}] {$user->nickname} {$user->province}{$user->city}{$user->district}",
                    "mentioned_list" => ["@all"]
                ]
            ];
            Http::post("https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=26ecddfd-2e48-4195-97d8-9cf205d13ecb", $data);

            $user->unsubscribe = 0;
            $user->update();
            return new News($items);
        } else if ($event == "unsubscribe") {
            //推送到企业微信
            $data = [
                "msgtype" => "markdown",
                "markdown" => [
                    "content" => "## 宠拉拉取关 > [{$user->id}] {$user->nickname}",
                    "mentioned_list" => ["@all"]
                ]
            ];
            Http::post("https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=26ecddfd-2e48-4195-97d8-9cf205d13ecb", $data);
            $user->unsubscribe = 1;
            $user->save();
        } else if ($event == "LOCATION") {
            $user->lat = $payload['Latitude'];
            $user->lng = $payload['Longitude'];
            $user->update();
        } else if ($event == "CLICK" && $payload['EventKey'] == "customer") {
            return new Image("EvcXmAzgWv8KL2MxArMR2rewOIWKNw8Nwqw1uQJfNbc");
        }
    }
}

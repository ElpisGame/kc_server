<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Bid;
use App\Models\Order;
use App\Models\User;
use App\Models\WechatPayment;
use App\Models\WechatRefund;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class AccountHelper
{
    public static function treeAccounts($id)
    {
        return Account::query()
            ->where("id", $id)
            ->with(['children' => function ($query) {
                $query->orderByDesc("id");
            }])
            ->get();
    }

    public static function flatAccounts($accounts, &$array)
    {
        foreach ($accounts as $account) {
            $array[] = $account;
            if ($account->children) {
                AccountHelper::flatAccounts($account->children, $array);
            }
        }
    }
}

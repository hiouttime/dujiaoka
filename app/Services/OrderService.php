<?php

namespace App\Services;

use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\GoodsServiceInterface;
use App\Services\Contracts\CouponServiceInterface;
use App\Exceptions\RuleValidationException;
use App\Models\BaseModel;
use App\Models\Coupon;
use App\Models\Goods;
use App\Models\Carmis;
use App\Models\Order;
use App\Rules\SearchPwd;
use App\Rules\VerifyImg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        protected GoodsServiceInterface $goodsService,
        protected CouponServiceInterface $couponService
    ) {}

    public function validateCreateOrder(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'gid' => 'required',
            'email' => ['required', 'email'],
            'payway' => ['required', 'integer'],
            'search_pwd' => [new SearchPwd()],
            'by_amount' => ['required', 'integer', 'min:1'],
            'img_verify_code' => [new VerifyImg()],
        ], [
            'by_amount.required' => __('dujiaoka.prompt.buy_amount_format_error'),
            'by_amount.integer' => __('dujiaoka.prompt.buy_amount_format_error'),
            'by_amount.min' => __('dujiaoka.prompt.buy_amount_format_error'),
            'payway.required' => __('dujiaoka.prompt.please_select_mode_of_payment'),
            'payway.integer' => __('dujiaoka.prompt.please_select_mode_of_payment'),
            'email.required' => __('dujiaoka.prompt.email_format_error'),
            'email.email' => __('dujiaoka.prompt.email_format_error'),
            'gid.required' => __('dujiaoka.prompt.goods_does_not_exist'),
        ]);

        if ($validator->fails()) {
            throw new RuleValidationException($validator->errors()->first());
        }

        if (config_get('system.is_open_geetest') == BaseModel::STATUS_OPEN) {
            $geetestValidator = Validator::make($request->all(), [
                'geetest_challenge' => 'geetest',
            ], [
                'geetest' => __('dujiaoka.prompt.geetest_validate_fail')
            ]);

            if ($geetestValidator->fails()) {
                throw new RuleValidationException(__('dujiaoka.prompt.geetest_validate_fail'));
            }
        }

        $limit = config_get('system.order_ip_limits', 0);
        if ($limit > 0) {
            $count = Order::where('buy_ip', $request->getClientIp())
                ->where('status', Order::STATUS_WAIT_PAY)
                ->count();

            if ($count >= $limit) {
                throw new RuleValidationException(__('dujiaoka.prompt.order_ip_limits'));
            }
        }
    }

    public function validateGoods(Request $request): Goods
    {
        $goods = $this->goodsService->detail($request->input('gid'));
        $this->goodsService->validateGoodsStatus($goods);

        if ($goods->buy_limit_num > 0 && $request->input('by_amount') > $goods->buy_limit_num) {
            throw new RuleValidationException(__('dujiaoka.prompt.purchase_limit_exceeded'));
        }

        if ($request->input('by_amount') > $goods->stock) {
            throw new RuleValidationException(__('dujiaoka.prompt.inventory_shortage'));
        }

        if ($request->input('carmi_id')) {
            if (!$this->goodsService->checkCarmiBelong($goods['id'], $request->input('carmi_id'))) {
                throw new RuleValidationException(__('dujiaoka.prompt.preselect_unable'));
            }
        }

        return $goods;
    }

    public function validateLoopCarmis(Request $request)
    {
        $carmis = Carmis::query()
            ->where('goods_id', $request->input('gid'))
            ->where('status', Carmis::STATUS_UNSOLD)
            ->where('is_loop', true)
            ->count();

        if ($carmis > 0 && $request->input('by_amount') > 1) {
            throw new RuleValidationException(__('dujiaoka.prompt.loop_carmis_limit'));
        }

        return $carmis;
    }

    public function validateCoupon(Request $request): ?Coupon
    {
        if ($request->filled('coupon_code')) {
            $coupon = $this->couponService->withHasGoods($request->input('coupon_code'), $request->input('gid'));

            if (empty($coupon)) {
                throw new RuleValidationException(__('dujiaoka.prompt.coupon_does_not_exist'));
            }

            if ($coupon->ret <= 0) {
                throw new RuleValidationException(__('dujiaoka.prompt.coupon_lack_of_available_opportunities'));
            }

            return $coupon;
        }

        return null;
    }

    public function validateChargeInput(Goods $goods, Request $request): string
    {
        $otherIpt = '';

        if ($goods->type == Goods::MANUAL_PROCESSING && !empty($goods->other_ipu_cnf)) {
            $formatIpt = format_charge_input($goods->other_ipu_cnf);
            foreach ($formatIpt as $item) {
                if ($item['rule'] && !$request->filled($item['field'])) {
                    $errMessage = $item['desc'] . __('dujiaoka.prompt.can_not_be_empty');
                    throw new RuleValidationException($errMessage);
                }
                $otherIpt .= $item['desc'] . ':' . $request->input($item['field']) . PHP_EOL;
            }
        }

        return $otherIpt;
    }

    public function detailOrderSN(string $orderSN): ?Order
    {
        return Order::query()->with(['coupon', 'pay', 'goods'])->where('order_sn', $orderSN)->first();
    }

    public function expiredOrderSN(string $orderSN): bool
    {
        return Order::query()->where('order_sn', $orderSN)->update(['status' => Order::STATUS_EXPIRED]);
    }

    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function withEmailAndPassword(string $email, string $searchPwd = '')
    {
        return Order::query()
            ->where('email', $email)
            ->when(!empty($searchPwd), function ($query) use ($searchPwd) {
                $query->where('search_pwd', $searchPwd);
            })
            ->orderBy('created_at', 'DESC')
            ->take(5)
            ->get();
    }

    public function byOrderSNS(array $orderSNS)
    {
        return Order::query()
            ->whereIn('order_sn', $orderSNS)
            ->orderBy('created_at', 'DESC')
            ->take(5)
            ->get();
    }
}
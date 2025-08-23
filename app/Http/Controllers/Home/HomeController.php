<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\BaseController;
use App\Models\Pay;
use App\Models\Goods;
use App\Models\Articles;
use App\Services\Shop;
use App\Services\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Germey\Geetest\Geetest;

class HomeController extends BaseController
{

    /**
     * 商品服务层.
     * @var \App\Services\Shop
     */
    private $goodsService;

    /**
     * 支付服务层
     * @var \App\Services\Payment
     */
    private $payService;

    public function __construct(Shop $goodsService, Payment $payService)
    {
        $this->goodsService = $goodsService;
        $this->payService = $payService;
    }

    /**
     * 首页.
     *
     * @param Request $request
     *
     */
    public function index(Request $request)
    {
        $goods = $this->goodsService->withGroup();
        $serviceType = [
            Goods::AUTOMATIC_DELIVERY => ["color" => "success", "icon" => "&#xe7cf;", "type" => "automatic_delivery"],
            Goods::MANUAL_PROCESSING => ["color" => "warning", "icon" => "&#xe74b;", "type" => "manual_processing"],
            Goods::AUTOMATIC_PROCESSING => ["color" => "info", "icon" => "&#xe7db;", "type" => "automatic_processing"],
            ];
        $articles = Articles::select('title', 'link', 'updated_at')
        ->take(8)
        ->orderBy('updated_at', 'desc')
        ->get();
        return $this->render('static_pages/home',
        [
            'data' => $goods, 
            'articles' => $articles,
            'types' => $serviceType,
            ],
        __('dujiaoka.page-title.home'));
    }

    /**
     * 商品详情
     *
     * @param any $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function buy($id)
    {
        if(!is_numeric($id))
            return $this->err(__('dujiaoka.prompt.the_goods_is_not_on_the_shelves'));
        try {
            $goods = $this->goodsService->detail($id);
            if(empty($goods))
                return $this->err(__('dujiaoka.prompt.the_goods_is_not_on_the_shelves'));
            $this->goodsService->validatorGoodsStatus($goods);
            $goods->need_login = $goods->require_login && !Auth::guard('web')->check();
            
            // 加载关联文章
            $goods->relatedArticles = $goods->articles()->select('articles.id', 'articles.title', 'articles.link', 'articles.content')->get();
            // 有没有优惠码可以展示
            if (count($goods->coupon)) {
                $goods->open_coupon = 1;
            }
            $formatGoods = $this->goodsService->format($goods);
            // 加载支付方式.
            $client = \App\Models\Pay::CLIENT_PC;
            if (app('Jenssegers\Agent')->isMobile()) {
                $client = \App\Models\Pay::CLIENT_MOBILE;
            }
            $formatGoods->payways = $this->payService->pays($client);
            if (!empty($formatGoods->payment_limit)) {
                $formatGoods->payways = array_filter($formatGoods->payways, function($way) use ($formatGoods) {
                    return in_array($way['id'], $formatGoods->payment_limit);
                });
            }
             if($goods->preselection > 0)
                $formatGoods->selectable = $this->goodsService->getSelectableCarmis($id);
            return $this->render('static_pages/buy', $formatGoods, $formatGoods->gd_name);
        } catch (RuleValidationException $ruleValidationException) {
            return $this->err($ruleValidationException->getMessage());
        }

    }

    /**
     * 极验行为验证
     *
     * @param Request $request
     *
     */
    public function geetest(Request $request)
    {
        $data = [
            'user_id' => @Auth::user()?@Auth::user()->id:'UnLoginUser',
            'client_type' => 'web',
            'ip_address' => \Illuminate\Support\Facades\Request::ip()
        ];
        $status = Geetest::preProcess($data);
        session()->put('gtserver', $status);
        session()->put('user_id', $data['user_id']);
        return Geetest::getResponseStr();
    }



}

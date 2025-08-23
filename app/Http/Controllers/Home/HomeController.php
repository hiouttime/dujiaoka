<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\BaseController;
use App\Models\Pay;
use App\Models\Goods;
use App\Models\Articles;
use App\Services\Shop;
use App\Services\Payment;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\QueryException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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

    /**
     * 安装页面
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function install(Request $request)
    {
        return view('common/install');
    }

    /**
     * 执行安装
     *
     * @param Request $request
     *
     */
    public function doInstall(Request $request)
    {
        try {
            $dbConfig = config('database');
            $mysqlDB = [
                'host' => $request->input('db_host'),
                'port' => $request->input('db_port'),
                'database' => $request->input('db_database'),
                'username' => $request->input('db_username'),
                'password' => $request->input('db_password'),
            ];
            $dbConfig['connections']['mysql'] = array_merge($dbConfig['connections']['mysql'], $mysqlDB);
            // Redis
            $redisDB = [
                'host' => $request->input('redis_host'),
                'password' => $request->input('redis_password', 'null'),
                'port' => $request->input('redis_port'),
            ];
            $dbConfig['redis']['default'] = array_merge($dbConfig['redis']['default'], $redisDB);
            config(['database' => $dbConfig]);
            DB::purge();
            // db测试
            DB::connection()->select('select 1 limit 1');
            // redis测试
            Redis::set('dujiaoka_com', 'ok');
            Redis::get('dujiaoka_com');
            // 获得文件模板
            $envExamplePath = base_path() . DIRECTORY_SEPARATOR . '.env.example';
            $envPath =  base_path() . DIRECTORY_SEPARATOR . '.env';
            $installLock = base_path() . DIRECTORY_SEPARATOR . 'install.lock';
            $installSql = database_path() . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'install.sql';
            $envTemp = file_get_contents($envExamplePath);
            $postData = $request->all();
            // 临时写入key
            $postData['app_key'] = 'base64:' . base64_encode(
                    Encrypter::generateKey(config('app.cipher'))
                );
            foreach ($postData as $key => $item) {
                $envTemp = str_replace('{' . $key . '}', $item, $envTemp);
            }
            // 写入配置
            file_put_contents($envPath, $envTemp);
            // 导入sql
            DB::unprepared(file_get_contents($installSql));
            // 写入安装锁
            file_put_contents($installLock, 'install ok');
            return 'success';
        } catch (\RedisException $exception) {
            return 'Redis配置错误 :' . $exception->getMessage();
        } catch (QueryException $exception) {
            return '数据库配置错误 :' . $exception->getMessage();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }


}

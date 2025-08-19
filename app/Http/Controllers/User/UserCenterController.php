<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FrontUser;
use App\Models\UserBalanceRecord;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $user = Auth::guard('web')->user();
        
        // 获取用户统计信息
        $stats = [
            'total_orders' => $user->orders()->count(),
            'completed_orders' => $user->orders()->where('status', Order::STATUS_COMPLETED)->count(),
            'total_spent' => $user->total_spent,
            'current_balance' => $user->balance,
        ];

        // 获取最近订单
        $recentOrders = $user->orders()
            ->with(['orderItems', 'pay'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 获取最近余额记录
        $recentBalanceRecords = $user->balanceRecords()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 获取下一个等级信息
        $nextLevel = $user->level ? $user->level->getNextLevel() : null;
        $upgradeProgress = 0;
        if ($nextLevel) {
            $currentLevelSpent = $user->level->min_spent;
            $nextLevelSpent = $nextLevel->min_spent;
            $userSpent = $user->total_spent;
            
            if ($nextLevelSpent > $currentLevelSpent) {
                $upgradeProgress = min(100, (($userSpent - $currentLevelSpent) / ($nextLevelSpent - $currentLevelSpent)) * 100);
            }
        }

        return view('themes.morpho.views.user.center', compact(
            'user', 'stats', 'recentOrders', 'recentBalanceRecords', 'nextLevel', 'upgradeProgress'
        ));
    }

    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('themes.morpho.views.user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();

        $request->validate([
            'nickname' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($request->only(['nickname', 'phone']));

        return back()->with('success', '个人信息更新成功！');
    }

    public function changePassword()
    {
        return view('themes.morpho.views.user.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::guard('web')->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '密码修改成功！');
    }

    public function orders(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $query = $user->orders()->with(['orderItems', 'pay']);

        // 状态筛选
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 日期筛选
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('themes.morpho.views.user.orders', compact('orders'));
    }

    public function orderDetail($orderSn)
    {
        $user = Auth::guard('web')->user();
        
        $order = $user->orders()
            ->where('order_sn', $orderSn)
            ->with(['orderItems.goods', 'pay'])
            ->firstOrFail();

        return view('themes.morpho.views.user.order-detail', compact('order'));
    }

    public function balance()
    {
        $user = Auth::guard('web')->user();
        
        $records = $user->balanceRecords()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // 统计信息
        $stats = [
            'total_recharge' => $user->balanceRecords()->where('type', 'recharge')->sum('amount'),
            'total_consume' => abs($user->balanceRecords()->where('type', 'consume')->sum('amount')),
            'total_refund' => $user->balanceRecords()->where('type', 'refund')->sum('amount'),
            'current_balance' => $user->balance,
        ];

        return view('themes.morpho.views.user.balance', compact('records', 'stats'));
    }

    public function recharge()
    {
        return view('themes.morpho.views.user.recharge');
    }

    public function processRecharge(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'pay_id' => ['required', 'exists:pays,id'],
        ]);

        $user = Auth::guard('web')->user();
        
        // 创建充值订单
        $order = Order::create([
            'order_sn' => 'R' . date('YmdHis') . rand(1000, 9999),
            'user_id' => $user->id,
            'email' => $user->email,
            'total_price' => $request->amount,
            'actual_price' => $request->amount,
            'coupon_discount_price' => 0,
            'user_discount_rate' => 1.00,
            'user_discount_amount' => 0,
            'payment_method' => Order::PAYMENT_ONLINE,
            'balance_used' => 0,
            'status' => Order::STATUS_WAIT_PAY,
            'pay_id' => $request->pay_id,
            'buy_ip' => $request->ip(),
        ]);

        // 创建订单项
        $order->orderItems()->create([
            'goods_id' => 0,
            'goods_name' => '余额充值',
            'goods_price' => $request->amount,
            'quantity' => 1,
            'total_price' => $request->amount,
            'type' => 0, // 特殊类型表示充值
            'info' => '用户余额充值'
        ]);

        return redirect()->route('pay.checkout', $order->order_sn);
    }

    public function levelInfo()
    {
        $user = Auth::guard('web')->user();
        $allLevels = \App\Models\UserLevel::getActiveLevels();
        
        return view('themes.morpho.views.user.level-info', compact('user', 'allLevels'));
    }
}
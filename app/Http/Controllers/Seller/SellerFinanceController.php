<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderFinancial;
use App\Services\FinancialSettlementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerFinanceController extends Controller
{
    public function __construct(
        protected FinancialSettlementService $settlementService
    ) {}

    /**
     * Hiển thị bảng điều khiển tài chính, ví Shop và lịch sử đối soát dòng tiền.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();
        $store = $user->store;

        if (! $store) {
            return redirect()->route('seller.register')->with('info', 'Bạn chưa có gian hàng trên ShopMart. Hãy hoàn tất đăng ký để bắt đầu kinh doanh!');
        }

        $wallet = $this->settlementService->getOrCreateStoreWallet($store);

        // Danh sách đối soát tài chính các đơn hàng
        $financialsQuery = OrderFinancial::with(['order.user', 'order.items.product'])
            ->where('store_id', $store->id)
            ->latest();

        $escrowStatus = $request->query('status');
        if ($escrowStatus && in_array($escrowStatus, ['holding', 'settled', 'cancelled'])) {
            $financialsQuery->where('escrow_status', $escrowStatus);
        }

        $financials = $financialsQuery->paginate(12)->withQueryString();

        // Thống kê nhanh
        $stats = OrderFinancial::where('store_id', $store->id)
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(gross_merchandise_amount) as total_gross,
                SUM(shop_discount) as total_shop_discount,
                SUM(payment_fee) as total_payment_fee,
                SUM(commission_fee) as total_commission_fee,
                SUM(shop_earning) as total_shop_earning,
                SUM(CASE WHEN escrow_status = "holding" THEN shop_earning ELSE 0 END) as total_escrow_holding,
                SUM(CASE WHEN escrow_status = "settled" THEN shop_earning ELSE 0 END) as total_settled
            ')->first();

        $transactions = $wallet->transactions()->take(10)->get();

        return view('seller.finances.index', compact('store', 'wallet', 'financials', 'stats', 'transactions', 'escrowStatus'));
    }

    /**
     * Yêu cầu rút tiền từ số dư khả dụng về tài khoản ngân hàng.
     */
    public function withdraw(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:100',
        ], [
            'amount.min' => 'Số tiền rút tối thiểu là 50.000₫.',
            'bank_name.required' => 'Vui lòng chọn hoặc nhập tên ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản nhận tiền.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ]);

        $store = auth()->user()->store;
        if (! $store) {
            return back()->with('error', 'Cửa hàng không tồn tại.');
        }

        $wallet = $this->settlementService->getOrCreateStoreWallet($store);
        $amount = (float) $request->input('amount');

        if ((float) $wallet->balance < $amount) {
            return back()->with('error', 'Số dư khả dụng không đủ để thực hiện yêu cầu rút tiền.');
        }

        $balanceBefore = (float) $wallet->balance;
        $balanceAfter = $balanceBefore - $amount;

        $wallet->update([
            'balance' => $balanceAfter,
            'total_withdrawn' => (float) $wallet->total_withdrawn + $amount,
            'bank_name' => $request->input('bank_name'),
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_account_name' => $request->input('bank_account_name'),
        ]);

        $wallet->transactions()->create([
            'transaction_code' => 'WDR-'.strtoupper(Str::random(10)),
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => "Yêu cầu rút tiền về {$request->input('bank_name')} ({$request->input('bank_account_number')})",
            'status' => 'completed',
        ]);

        return back()->with('success', 'Yêu cầu rút tiền '.number_format($amount, 0, ',', '.').'₫ đã được tiếp nhận và xử lý thành công!');
    }
}

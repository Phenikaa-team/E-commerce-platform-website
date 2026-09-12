<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherPageController extends Controller
{
    /**
     * Display the public Shopee-style Voucher Portal.
     */
    public function index(Request $request): View
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('min_order_value', 'asc')
            ->get();

        $freeshipCoupons = $coupons->filter(function ($c) {
            return str_contains(strtoupper($c->code), 'FREESHIP') || str_contains(strtolower($c->name), 'vận chuyển');
        });

        $mallCoupons = $coupons->filter(function ($c) {
            return str_contains(strtoupper($c->code), 'MALL');
        });

        $categoryCoupons = $coupons->filter(function ($c) {
            return str_contains(strtoupper($c->code), 'TECH') || str_contains(strtoupper($c->code), 'DIENTU') || str_contains(strtoupper($c->code), 'FASHION');
        });

        return view('vouchers', compact('coupons', 'freeshipCoupons', 'mallCoupons', 'categoryCoupons'));
    }
}

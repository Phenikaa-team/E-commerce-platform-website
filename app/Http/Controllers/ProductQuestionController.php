<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    /**
     * Store a new question submitted by a user or guest.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:60'],
            'question' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $user = auth()->user();
        $customerName = $user ? $user->name : ($validated['customer_name'] ?? 'Khách hàng');

        $product->questions()->create([
            'user_id' => $user?->id,
            'customer_name' => $customerName,
            'question' => trim($validated['question']),
            'is_visible' => true,
        ]);

        return redirect()->to(url()->previous().'#qa')->with('success', 'Câu hỏi của bạn đã được gửi tới người bán thành công!');
    }

    /**
     * Store responds to a question.
     */
    public function answer(Request $request, ProductQuestion $question): RedirectResponse
    {
        $user = auth()->user();
        $product = $question->product;

        // Ensure user is seller and owns the store of this product
        if (! $user || ! $user->store || $user->store->id !== $product->store_id) {
            abort(403, 'Bạn không có quyền trả lời câu hỏi của sản phẩm này.');
        }

        $validated = $request->validate([
            'answer' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $question->update([
            'answer' => trim($validated['answer']),
            'answered_at' => now(),
        ]);

        return back()->with('success', 'Đã phản hồi câu hỏi thành công!');
    }

    /**
     * Store or question author toggles visibility of a question (show/hide).
     */
    public function toggleVisibility(Request $request, ProductQuestion $question): RedirectResponse
    {
        $user = auth()->user();
        $product = $question->product;

        $isOwner = $user && $user->store && $user->store->id === $product->store_id;
        $isAuthor = $user && $question->user_id && $user->id === $question->user_id;

        if (! $isOwner && ! $isAuthor) {
            abort(403, 'Bạn không có quyền ẩn hoặc hiện câu hỏi này.');
        }

        $question->update([
            'is_visible' => ! $question->is_visible,
        ]);

        $statusText = $question->is_visible ? 'hiển thị' : 'ẩn';

        return redirect()->to(url()->previous().'#qa')->with('success', "Đã {$statusText} câu hỏi thành công!");
    }

    /**
     * Delete a question (allowed for the author or store owner).
     */
    public function destroy(ProductQuestion $question): RedirectResponse
    {
        $user = auth()->user();
        $product = $question->product;

        $isOwner = $user && $user->store && $user->store->id === $product->store_id;
        $isAuthor = $user && $question->user_id && $user->id === $question->user_id;

        if (! $isOwner && ! $isAuthor) {
            abort(403, 'Bạn không có quyền xóa câu hỏi này.');
        }

        $question->delete();

        return redirect()->to(url()->previous().'#qa')->with('success', 'Đã xóa câu hỏi thành công.');
    }
}

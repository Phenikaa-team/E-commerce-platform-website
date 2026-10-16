<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminFaqController extends Controller
{
    public function index(Request $request): View
    {
        $categoryFilter = $request->query('category');
        $search = $request->query('search');

        $query = Faq::query();

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%")
                    ->orWhere('prompt', 'like', "%{$search}%");
            });
        }

        $faqs = $query->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Faq::categories();

        return view('admin.faqs.index', compact('faqs', 'categories', 'categoryFilter', 'search'));
    }

    public function create(): View
    {
        $categories = Faq::categories();
        $faq = new Faq(['sort_order' => Faq::max('sort_order') + 1, 'is_active' => true]);

        return view('admin.faqs.form', compact('faq', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:50'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'prompt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? ((int) Faq::max('sort_order') + 1);

        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('success', 'Đã thêm câu hỏi trợ giúp mới thành công.');
    }

    public function edit(Faq $faq): View
    {
        $categories = Faq::categories();

        return view('admin.faqs.form', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:50'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'prompt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('success', 'Đã cập nhật câu hỏi trợ giúp thành công.');
    }

    public function toggle(Faq $faq): RedirectResponse
    {
        $faq->update(['is_active' => ! $faq->is_active]);

        return back()->with('success', 'Đã đổi trạng thái câu hỏi trợ giúp.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'Đã xóa câu hỏi trợ giúp.');
    }
}

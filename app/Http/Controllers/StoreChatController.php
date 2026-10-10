<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreChatConversation;
use App\Models\StoreChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StoreChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_if($user->isAdmin(), 403);
        $validated = $request->validate([
            'store_q' => ['nullable', 'string', 'max:100'],
        ]);
        $storeSearch = trim($validated['store_q'] ?? '');

        $conversations = StoreChatConversation::where('buyer_id', $user->id)
            ->with(['store', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get();
        $stores = $this->availableStores($user->id, $storeSearch);

        return view('chat.index', [
            'conversations' => $conversations,
            'conversation' => null,
            'messages' => collect(),
            'isSeller' => false,
            'stores' => $stores,
            'storeSearch' => $storeSearch,
        ]);
    }

    public function start(Request $request, Store $store): RedirectResponse
    {
        $user = $request->user();
        abort_if($user->isAdmin() || $user->store?->is($store), 403);
        abort_unless($store->status === 'active', 404);

        $conversation = StoreChatConversation::firstOrCreate([
            'store_id' => $store->id,
            'buyer_id' => $user->id,
        ]);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Request $request, int $conversationId): View
    {
        $conversation = $this->buyerConversation($conversationId);
        $conversation->update(['buyer_unread_count' => 0]);
        $validated = $request->validate([
            'store_q' => ['nullable', 'string', 'max:100'],
        ]);

        return $this->renderConversation($conversation, false, trim($validated['store_q'] ?? ''));
    }

    public function sellerIndex(): View
    {
        $store = auth()->user()->store;
        abort_unless($store, 403);

        $conversations = StoreChatConversation::where('store_id', $store->id)
            ->with(['buyer', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('chat.index', [
            'conversations' => $conversations,
            'conversation' => null,
            'messages' => collect(),
            'isSeller' => true,
        ]);
    }

    public function sellerShow(int $conversationId): View
    {
        $conversation = $this->sellerConversation($conversationId);
        $conversation->update(['seller_unread_count' => 0]);

        return $this->renderConversation($conversation, true);
    }

    public function messages(Request $request, int $conversationId): JsonResponse
    {
        $conversation = $this->authorizedConversation($request, $conversationId);
        $isSeller = $request->routeIs('seller.chats.messages');
        $conversation->update([$isSeller ? 'seller_unread_count' : 'buyer_unread_count' => 0]);

        $validated = $request->validate([
            'after' => ['sometimes', 'integer', 'min:0'],
        ]);

        return response()->json(
            $conversation->messages()
                ->where('id', '>', $validated['after'] ?? 0)
                ->with('sender:id,name')
                ->oldest('id')
                ->limit(50)
                ->get()
                ->map(fn (StoreChatMessage $message): array => $this->serializeMessage($message))
        );
    }

    public function send(Request $request, int $conversationId): JsonResponse
    {
        $conversation = $this->authorizedConversation($request, $conversationId);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000', 'not_regex:/^\s*$/u'],
        ]);

        $isSeller = $request->routeIs('seller.chats.send');
        $message = DB::transaction(function () use ($conversation, $request, $validated, $isSeller): StoreChatMessage {
            $message = $conversation->messages()->create([
                'sender_id' => $request->user()->id,
                'body' => trim($validated['body']),
            ]);

            $conversation->update(['last_message_at' => $message->created_at]);
            $conversation->increment($isSeller ? 'buyer_unread_count' : 'seller_unread_count');

            return $message;
        });

        return response()->json($this->serializeMessage($message->load('sender:id,name')), 201);
    }

    private function renderConversation(StoreChatConversation $conversation, bool $isSeller, string $storeSearch = ''): View
    {
        $conversation->load($isSeller ? 'buyer' : 'store');
        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->latest('id')
            ->limit(60)
            ->get()
            ->reverse()
            ->values();

        $conversations = StoreChatConversation::query()
            ->where($isSeller ? 'store_id' : 'buyer_id', $isSeller ? $conversation->store_id : $conversation->buyer_id)
            ->with($isSeller ? ['buyer', 'lastMessage.sender'] : ['store', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get();

        $stores = $isSeller ? collect() : $this->availableStores($conversation->buyer_id, $storeSearch);

        return view('chat.index', compact('conversations', 'conversation', 'messages', 'isSeller', 'stores', 'storeSearch'));
    }

    private function availableStores(int $userId, string $storeSearch): LengthAwarePaginator
    {
        return Store::query()
            ->where('status', 'active')
            ->where('user_id', '!=', $userId)
            ->when($storeSearch !== '', fn ($query) => $query->where('name', 'like', "%{$storeSearch}%"))
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();
    }

    private function authorizedConversation(Request $request, int $conversationId): StoreChatConversation
    {
        return $request->routeIs('seller.chats.*')
            ? $this->sellerConversation($conversationId)
            : $this->buyerConversation($conversationId);
    }

    private function buyerConversation(int $conversationId): StoreChatConversation
    {
        return StoreChatConversation::where('buyer_id', auth()->id())->findOrFail($conversationId);
    }

    private function sellerConversation(int $conversationId): StoreChatConversation
    {
        $store = auth()->user()->store;
        abort_unless($store, 403);

        return StoreChatConversation::where('store_id', $store->id)->findOrFail($conversationId);
    }

    /** @return array{id: int, sender_id: int, sender_name: string, body: string, created_at: string} */
    private function serializeMessage(StoreChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
        ];
    }
}

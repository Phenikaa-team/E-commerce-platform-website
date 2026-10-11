<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupportChatController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()->isAdmin(), 403);

        $conversation = SupportConversation::where('customer_id', $request->user()->id)->first();
        if ($conversation) {
            $conversation->update(['customer_unread_count' => 0]);

            return $this->renderConversation($conversation, false);
        }

        return view('support.index', [
            'conversations' => collect(),
            'conversation' => null,
            'messages' => collect(),
            'isAdmin' => false,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        abort_if($request->user()->isAdmin(), 403);

        $conversation = SupportConversation::firstOrCreate([
            'customer_id' => $request->user()->id,
        ]);

        return redirect()->route('support.show', $conversation->id);
    }

    public function show(Request $request, int $conversationId): View
    {
        $conversation = $this->customerConversation($conversationId, $request->user()->id);
        $conversation->update(['customer_unread_count' => 0]);

        return $this->renderConversation($conversation, false);
    }

    public function adminIndex(): View
    {
        $conversations = SupportConversation::with(['customer', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('support.index', [
            'conversations' => $conversations,
            'conversation' => null,
            'messages' => collect(),
            'isAdmin' => true,
        ]);
    }

    public function adminShow(int $conversationId): View
    {
        $conversation = SupportConversation::findOrFail($conversationId);
        $conversation->update(['admin_unread_count' => 0]);

        return $this->renderConversation($conversation, true);
    }

    public function messages(Request $request, int $conversationId): JsonResponse
    {
        $conversation = $this->authorizedConversation($request, $conversationId);
        $isAdmin = $request->routeIs('admin.support-chats.messages');
        $conversation->update([$isAdmin ? 'admin_unread_count' : 'customer_unread_count' => 0]);
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
                ->map(fn (SupportMessage $message): array => $this->serializeMessage($message))
        );
    }

    public function send(Request $request, int $conversationId): JsonResponse
    {
        $conversation = $this->authorizedConversation($request, $conversationId);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000', 'not_regex:/^\s*$/u'],
        ]);
        $isAdmin = $request->routeIs('admin.support-chats.send');

        $message = DB::transaction(function () use ($conversation, $request, $validated, $isAdmin): SupportMessage {
            $message = $conversation->messages()->create([
                'sender_id' => $request->user()->id,
                'body' => trim($validated['body']),
            ]);

            $conversation->update(['last_message_at' => $message->created_at]);
            $conversation->increment($isAdmin ? 'customer_unread_count' : 'admin_unread_count');

            return $message;
        });

        return response()->json($this->serializeMessage($message->load('sender:id,name')), 201);
    }

    private function renderConversation(SupportConversation $conversation, bool $isAdmin): View
    {
        $conversation->load('customer');
        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->latest('id')
            ->limit(60)
            ->get()
            ->reverse()
            ->values();
        $conversations = $this->conversationList($isAdmin, $conversation->customer_id);

        return view('support.index', compact('conversations', 'conversation', 'messages', 'isAdmin'));
    }

    private function conversationList(bool $isAdmin, ?int $customerId = null): Collection|LengthAwarePaginator
    {
        if ($isAdmin) {
            return SupportConversation::with(['customer', 'lastMessage.sender'])
                ->orderByDesc('last_message_at')
                ->paginate(20);
        }

        return SupportConversation::where('customer_id', $customerId ?? auth()->id())
            ->with(['customer', 'lastMessage.sender'])
            ->get();
    }

    private function authorizedConversation(Request $request, int $conversationId): SupportConversation
    {
        return $request->routeIs('admin.support-chats.*')
            ? SupportConversation::findOrFail($conversationId)
            : $this->customerConversation($conversationId, $request->user()->id);
    }

    private function customerConversation(int $conversationId, int $customerId): SupportConversation
    {
        return SupportConversation::where('customer_id', $customerId)->findOrFail($conversationId);
    }

    /** @return array{id: int, sender_id: int, sender_name: string, body: string, created_at: string} */
    private function serializeMessage(SupportMessage $message): array
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

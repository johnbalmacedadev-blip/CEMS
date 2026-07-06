<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    private const ONLINE_TTL_SECONDS = 120;

    /**
     * Sync chat messages
     *
     * Poll for new team chat messages and online users. Used by the live chat widget (every 3 seconds).
     *
     * @group Live Team Chat
     * @authenticated
     *
     * @queryParam after integer optional Return only messages with ID greater than this value. Example: 42
     *
     * @response 200 {"messages":[],"online_users":[],"latest_id":0,"current_user":{"id":1,"name":"Admin","initials":"A"}}
     */
    public function sync(Request $request)
    {
        $user = Auth::user();
        $this->touchPresence($user);

        $afterId = (int) $request->query('after', 0);

        if ($afterId > 0) {
            $messages = ChatMessage::with('user')
                ->where('id', '>', $afterId)
                ->orderBy('id')
                ->limit(100)
                ->get();
        } else {
            $messages = ChatMessage::with('user')
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->sortBy('id')
                ->values();
        }

        $messages = $messages->map(fn (ChatMessage $m) => $m->toChatArray());

        return response()->json([
            'messages' => $messages,
            'online_users' => $this->onlineUsers(),
            'latest_id' => ChatMessage::max('id') ?? 0,
            'current_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'initials' => ChatMessage::initials($user->name),
            ],
        ]);
    }

    /**
     * Send a chat message
     *
     * Post a text message, link, or file attachment to the team chat.
     *
     * @group Live Team Chat
     * @authenticated
     *
     * @bodyParam body string optional Message text (max 5000 chars). Example: Good morning team!
     * @bodyParam link_url string optional URL to share. Example: https://example.com
     * @bodyParam attachment file optional File attachment (jpg, png, pdf, doc, xls, zip — max 10MB).
     *
     * @response 201 {"message":{"id":1,"body":"Hello","user":{}}}
     * @response 422 {"message":"Enter a message, link, or file."}
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'body' => 'nullable|string|max:5000',
            'link_url' => 'nullable|url|max:2048',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip',
        ]);

        $body = trim($validated['body'] ?? '');
        $linkUrl = trim($validated['link_url'] ?? '');

        if ($body === '' && $linkUrl === '' && !$request->hasFile('attachment')) {
            return response()->json(['message' => 'Enter a message, link, or file.'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentMime = $file->getClientMimeType();
        }

        $message = ChatMessage::create([
            'user_id' => Auth::id(),
            'body' => $body !== '' ? $body : null,
            'link_url' => $linkUrl !== '' ? $linkUrl : null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
        ]);

        $message->load('user');
        $this->touchPresence(Auth::user());

        return response()->json([
            'message' => $message->toChatArray(),
        ], 201);
    }

    /**
     * Chat presence heartbeat
     *
     * Keeps the current user marked as online in team chat.
     *
     * @group Live Team Chat
     * @authenticated
     *
     * @response 200 {"online_users":[{"id":1,"name":"Admin","initials":"A"}]}
     */
    public function heartbeat()
    {
        $this->touchPresence(Auth::user());

        return response()->json([
            'online_users' => $this->onlineUsers(),
        ]);
    }

    private function touchPresence($user): void
    {
        $online = Cache::get('chat_online_users', []);
        $online[(string) $user->id] = [
            'id' => $user->id,
            'name' => $user->name,
            'initials' => ChatMessage::initials($user->name),
            'last_seen' => now()->timestamp,
        ];

        $cutoff = now()->subSeconds(self::ONLINE_TTL_SECONDS)->timestamp;
        foreach ($online as $id => $data) {
            if (($data['last_seen'] ?? 0) < $cutoff) {
                unset($online[$id]);
            }
        }

        Cache::put('chat_online_users', $online, now()->addMinutes(10));
    }

    private function onlineUsers(): array
    {
        $online = Cache::get('chat_online_users', []);
        $cutoff = now()->subSeconds(self::ONLINE_TTL_SECONDS)->timestamp;
        $users = [];

        foreach ($online as $data) {
            if (($data['last_seen'] ?? 0) >= $cutoff) {
                $users[] = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'initials' => $data['initials'],
                ];
            }
        }

        usort($users, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $users;
    }
}

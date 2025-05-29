<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Telegram;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    protected $telegramToken;

    public function __construct()
    {
        // Use the correct env variable name
        $this->telegramToken = env('TELEGRAM_BOT_TOKEN');
    }

    public function handleWebhook(Request $request)
    {
        try {
            Log::info('📩 Webhook request received:', $request->all());

            $message = $request->input('message');

            // If we have a message with text, process it as a command
            if ($message && isset($message['text'])) {
                Log::info('Processing command:', ['text' => $message['text']]);
                $this->handleCommands($message);

                return response()->json([
                    'status' => true,
                    'message' => '✅ Command processed successfully'
                ]);
            }

            // Handle order notifications
            $chatidbot = $request->input('chatidbot');
            $username = $request->input('username');
            $total = $request->input('total');
            $appKey = $request->input('app_key');

            if ($chatidbot && $username && $total) {
                // ... existing order handling code ...
            }

            return response()->json([
                'status' => true,
                'message' => '✅ Webhook received'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Webhook error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function handleCommands($message)
    {
        try {
            $chatId = $message['chat']['id'] ?? null;
            $text = $message['text'] ?? '';
            $reply = '';

            if (!$chatId) {
                Log::error('No chat ID found in message.');
                return;
            }

            if ($text === '/start') {
                $reply = "🎉 សូមស្វាគមន៍មកកាន់ OrderItem Bot!\n\n";
                $reply .= "សូមវាយអរក្ស៖ /generate_key ដើម្បីបានលេខកូដយកទៅកំណត់ក្នុង System អ្នក";
            } elseif ($text === '/generate_key') {
                Log::info('Generating key for chat ID: ' . $chatId);

                $uniqueCode = $this->generateUniqueCode();

                $existingUser = Telegram::where('chatBotID', $chatId)->first();

                if ($existingUser) {
                    $reply = "⚠️ អ្នកមានលេខកូដរួចហើយ:\n";
                    $reply .= "======||======||======\n";
                    $reply .= "{$existingUser->app_key}\n";
                    $reply .= "======||======||======";
                    $reply .= "\nសូមរក្សាលេខកូដនេះឲ្យបានសុវត្ថិភាព។ អ្នកអាចប្រើវាដើម្បីភ្ជាប់ Telegram Bot របស់អ្នកទៅកាន់គណនីរបស់អ្នក។";
                } else {
                    // Get username or fallback to chat ID
                    $tel_username = $message['chat']['username'] ?? ('user_' . $chatId);

                    $telegram = new Telegram();
                    $telegram->app_key = $uniqueCode;
                    $telegram->chatBotID = $chatId;
                    $telegram->tel_username = $tel_username;
                    $telegram->save();

                    $reply = "🔑 នេះជាលេខកូដសំងាត់របស់អ្នក៖\n";
                    $reply .= "======||======||======\n";
                    $reply .= "{$uniqueCode}\n";
                    $reply .= "======||======||======";
                    $reply .= "\nសូមរក្សាលេខកូដនេះឲ្យបានសុវត្ថិភាព។ អ្នកអាចប្រើវាដើម្បីភ្ជាប់ Telegram Bot របស់អ្នកទៅកាន់គណនីរបស់អ្នក។";
                }
            }

            if ($reply) {
                Log::info('Sending reply: ' . $reply);
                $this->sendMessage($chatId, $reply);
            }
        } catch (\Exception $e) {
            Log::error('Command handling error: ' . $e->getMessage());
            if (isset($chatId)) {
                $this->sendMessage($chatId, "❌ មានបញ្ហាក្នុងការបង្កើតលេខកូដ។ សូមព្យាយាមម្តងទៀត។");
            }
        }
    }

    private function generateUniqueCode($length = 30)
    {
        return Str::random($length) . time();
    }


    private function sendMessage($chatId, $text)
    {
        try {
            Log::info('Sending message:', ['chat_id' => $chatId, 'text' => $text]);
            $response = Http::post("https://api.telegram.org/bot{$this->telegramToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ]);

            $result = $response->json();
            Log::info('Telegram API Response:', $result);

            if (!$response->successful()) {
                Log::error('Telegram API error: ' . ($result['description'] ?? 'Unknown error'));
                throw new \Exception('Failed to send message: ' . ($result['description'] ?? 'Unknown error'));
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            // Don't throw further to avoid breaking webhook response
            // Optionally, you can return false or log more details here
            return false;
        }
    }

    // Existing methods for web interface
    public function showVerifyForm()
    {
        return inertia('Telegram/VerifyKey');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        $user = Auth::user();
        $telegram = Telegram::where('app_key', $request->key)->first();

        if ($telegram) {
            if ($telegram->user_id) {
                return back()->with('success', 'linked');
            }
            $telegram->user_id = $user->id;
            $telegram->save();
            return back()->with('success', 'user-updated');
        }
        return back()->with('success', 'invalid');
    }


    // private function sendMessage($chatId, $text)
    // {
    //     $botToken = env('TELEGRAM_BOT_TOKEN');
    //     $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    //     $payload = [
    //         'chat_id' => $chatId,
    //         'text' => $text,
    //     ];

    //     // Send request to Telegram
    //     $client = new \GuzzleHttp\Client();
    //     $client->post($url, ['json' => $payload]);
    // }
}

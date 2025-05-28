<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Telegram;
use Illuminate\Support\Facades\Auth;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {      
            $data = $request->all();
            // Log the incoming data (for debugging)
            Log::info('Telegram Webhook Received:', $data);

            if (isset($data['app_key'])) {
                $app_key = $data['app_key'];
                $chatBotID = $data['chatBotID'];
                $tel_username = $data['tel_username'];

                Telegram::create([
                    'app_key' => $app_key,
                    'chatBotID' => $chatBotID,
                    'tel_username' => $tel_username,
                    'user_id' => null,
                ]);

            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error("Telegram Webhook Received failed: " . $e->getMessage());
        }
    }

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
        $key = $request->key;

        $telegram = Telegram::where('app_key', $key)->first();
        if ($telegram) {
            if ($telegram->user_id) {
                return back()->with('success', 'linked');
            }
            // Update the telegram_keys table with the user's ID
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

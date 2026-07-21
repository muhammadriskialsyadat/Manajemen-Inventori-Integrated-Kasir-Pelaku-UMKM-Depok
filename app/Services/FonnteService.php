<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $token;
    private string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = (string) config('services.fonnte.token', '');
    }

    public function sendMessage(string $target, string $message): bool
    {
        if (empty($this->token) || empty($target)) {
            Log::warning('Fonnte: token or target is empty — message not sent', [
                'target' => $target,
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl, [
                'target'  => $target,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Fonnte: HTTP error when sending message', [
                'target'   => $target,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('Fonnte: exception when sending message', [
                'target' => $target,
                'error'  => $e->getMessage(),
            ]);

            return false;
        }
    }
}
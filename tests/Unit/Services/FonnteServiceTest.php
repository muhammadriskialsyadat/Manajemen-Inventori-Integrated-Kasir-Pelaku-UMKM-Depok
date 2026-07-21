<?php

namespace Tests\Unit\Services;

use App\Services\FonnteService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FonnteServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('FONNTE_TOKEN');
        unset($_ENV['FONNTE_TOKEN'], $_SERVER['FONNTE_TOKEN']);

        parent::tearDown();
    }

    private function setToken(string $token): void
    {
        putenv("FONNTE_TOKEN={$token}");
        $_ENV['FONNTE_TOKEN'] = $token;
        $_SERVER['FONNTE_TOKEN'] = $token;
    }

    public function test_returns_false_and_sends_nothing_when_token_is_empty(): void
    {
        $this->setToken('');
        Http::fake();

        $result = (new FonnteService)->sendMessage('08123456789', 'Halo');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_returns_false_and_sends_nothing_when_target_is_empty(): void
    {
        $this->setToken('secret-token');
        Http::fake();

        $result = (new FonnteService)->sendMessage('', 'Halo');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_sends_message_and_returns_true_on_successful_response(): void
    {
        $this->setToken('secret-token');
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $result = (new FonnteService)->sendMessage('08123456789', 'Halo dunia');

        $this->assertTrue($result);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization', 'secret-token')
                && $request['target'] === '08123456789'
                && $request['message'] === 'Halo dunia';
        });
    }

    public function test_returns_false_when_gateway_responds_with_error_status(): void
    {
        $this->setToken('secret-token');
        Http::fake([
            'api.fonnte.com/*' => Http::response('Server error', 500),
        ]);

        $result = (new FonnteService)->sendMessage('08123456789', 'Halo');

        $this->assertFalse($result);
    }

    public function test_returns_false_when_http_client_throws(): void
    {
        $this->setToken('secret-token');
        Http::fake(function () {
            throw new \RuntimeException('connection refused');
        });

        $result = (new FonnteService)->sendMessage('08123456789', 'Halo');

        $this->assertFalse($result);
    }
}

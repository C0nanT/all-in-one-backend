<?php

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Modules\TransportCard\Models\TransportCardBalance;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

// Testes desativados para evitar flood de requisições para a API, sem previsão de uso.

// beforeEach(function (): void {
//     Config::set('tacom.base_url', 'https://tacom-test.example.com');
//     Config::set('services.tacom', [
//         'username' => 'test-user',
//         'password' => 'test-pass',
//         'card_number' => '036200002681705',
//         'cpf' => '04357575125',
//     ]);
// });

// describe('when authenticated', function (): void {
//     beforeEach(function (): void {
//         $this->user = User::factory()->create();
//         Sanctum::actingAs($this->user, ['*']);
//     });

//     test('get balance returns from cache when record exists for today', function (): void {
//         TransportCardBalance::query()->create([
//             'snapshot_date' => Carbon::today(),
//             'balance' => 50.00,
//             'card_number' => '03620000268170',
//             'raw_response' => null,
//         ]);

//         $response = $this->getJson('/api/transport-card/balance');

//         $response->assertSuccessful()
//             ->assertJsonPath('balance', 50)
//             ->assertJsonPath('from_cache', true)
//             ->assertJsonPath('card_number', '03620000268170')
//             ->assertJsonStructure(['balance', 'updated_at', 'from_cache', 'card_number', 'last_used_at', 'owner_name']);

//         Http::assertNothingSent();
//     });

//     test('get balance fetches from api when no cache for today', function (): void {
//         Http::fake(function (Request $request) {
//             if (str_contains($request->url(), 'auth2/login')) {
//                 return Http::response(['access_token' => 'fake-token']);
//             }
//             if (str_contains($request->url(), 'findCartao')) {
//                 return Http::response([
//                     'saldo' => 23.09,
//                     'codExternoCartao' => '03620000268170',
//                     'dataUsoCartao' => '2026-03-11T14:25:53.000+00:00',
//                     'dependenteTitular' => [
//                         'dependente' => [
//                             'nome' => 'CONAN CAMPOS SOUZA TORRES',
//                         ],
//                     ],
//                 ]);
//             }

//             return Http::response('Not found', 404);
//         });

//         $response = $this->getJson('/api/transport-card/balance');

//         $response->assertSuccessful()
//             ->assertJsonPath('balance', 23.09)
//             ->assertJsonPath('from_cache', false)
//             ->assertJsonPath('card_number', '03620000268170')
//             ->assertJsonPath('owner_name', 'CONAN CAMPOS SOUZA TORRES')
//             ->assertJsonStructure(['balance', 'updated_at', 'from_cache', 'card_number', 'last_used_at', 'owner_name']);

//         $this->assertDatabaseHas('transport_card_balances', ['balance' => 23.09]);
//     });

//     test('get balance with refresh=1 forces api fetch even when cache exists', function (): void {
//         TransportCardBalance::query()->create([
//             'snapshot_date' => Carbon::today(),
//             'balance' => 10.00,
//             'card_number' => '03620000268170',
//             'raw_response' => null,
//         ]);

//         Http::fake(function (Request $request) {
//             if (str_contains($request->url(), 'auth2/login')) {
//                 return Http::response(['access_token' => 'fake-token']);
//             }
//             if (str_contains($request->url(), 'findCartao')) {
//                 return Http::response([
//                     'saldo' => 99.50,
//                     'codExternoCartao' => '03620000268170',
//                     'dependenteTitular' => [
//                         'dependente' => [
//                             'nome' => 'CONAN CAMPOS SOUZA TORRES',
//                         ],
//                     ],
//                 ]);
//             }

//             return Http::response('Not found', 404);
//         });

//         $response = $this->getJson('/api/transport-card/balance?refresh=1');

//         $response->assertSuccessful()
//             ->assertJsonPath('balance', 99.50)
//             ->assertJsonPath('from_cache', false)
//             ->assertJsonPath('card_number', '03620000268170')
//             ->assertJsonPath('owner_name', 'CONAN CAMPOS SOUZA TORRES');

//         $this->assertDatabaseHas('transport_card_balances', ['balance' => 99.50]);
//     });

//     test('post refresh always fetches from api and updates cache', function (): void {
//         TransportCardBalance::query()->create([
//             'snapshot_date' => Carbon::today(),
//             'balance' => 5.00,
//             'card_number' => '03620000268170',
//             'raw_response' => null,
//         ]);

//         Http::fake(function (Request $request) {
//             if (str_contains($request->url(), 'auth2/login')) {
//                 return Http::response(['access_token' => 'fake-token']);
//             }
//             if (str_contains($request->url(), 'findCartao')) {
//                 return Http::response([
//                     'saldo' => 42.75,
//                     'codExternoCartao' => '03620000268170',
//                     'dependenteTitular' => [
//                         'dependente' => [
//                             'nome' => 'CONAN CAMPOS SOUZA TORRES',
//                         ],
//                     ],
//                 ]);
//             }

//             return Http::response('Not found', 404);
//         });

//         $response = $this->postJson('/api/transport-card/refresh');

//         $response->assertSuccessful()
//             ->assertJsonPath('balance', 42.75)
//             ->assertJsonPath('from_cache', false)
//             ->assertJsonPath('card_number', '03620000268170')
//             ->assertJsonPath('owner_name', 'CONAN CAMPOS SOUZA TORRES');

//         $this->assertDatabaseHas('transport_card_balances', ['balance' => 42.75]);
//     });

//     test('balance returns 502 when tacom api fails', function (): void {
//         Http::fake([
//             '*' => Http::response('Server Error', 500),
//         ]);

//         $response = $this->getJson('/api/transport-card/balance');

//         $response->assertStatus(502)
//             ->assertJsonPath('message', 'Unable to fetch transport card balance. Please try again later.');
//     });

//     test('refresh returns 502 when tacom api fails', function (): void {
//         Http::fake([
//             '*' => Http::response('Server Error', 500),
//         ]);

//         $response = $this->postJson('/api/transport-card/refresh');

//         $response->assertStatus(502)
//             ->assertJsonPath('message', 'Unable to fetch transport card balance. Please try again later.');
//     });
// });

// describe('when unauthenticated', function (): void {
//     beforeEach(function (): void {
//         Config::set('tacom.base_url', 'https://tacom-test.example.com');
//         Config::set('services.tacom', [
//             'username' => 'test-user',
//             'password' => 'test-pass',
//             'card_number' => '036200002681705',
//             'cpf' => '04357575125',
//         ]);
//     });

//     test('balance returns 401', function (): void {
//         $response = $this->getJson('/api/transport-card/balance');

//         $response->assertUnauthorized();
//     });
// });

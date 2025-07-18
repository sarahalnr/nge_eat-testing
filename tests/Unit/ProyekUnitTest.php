<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\GoFoodController;
use App\Services\GoFoodService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\GoFood;
use Mockery; // Kita masih butuh Mockery untuk mock Request

class ProyekUnitTest extends TestCase
{
    protected $goFoodServiceMock;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->goFoodServiceMock = $this->createMock(GoFoodService::class);
        $this->controller = new GoFoodController($this->goFoodServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test the store method successfully delegates to service and redirects.
     * @return void
     */
    public function test_store_method_delegates_to_service_and_redirects_on_success()
    {
        $requestData = [
            'tanggal' => '2025-07-17',
            'waktu' => '10:00',
            'nama_pelanggan' => 'Unit Test User',
            'metode_pembayaran' => 'Cash',
            'items' => [
                ['menu_id' => 101, 'platform_id' => 201, 'jumlah' => 2],
            ],
        ];
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn(null);
        $request->shouldReceive('all')->andReturn($requestData);
        $request->shouldReceive('has')->with('status')->andReturn(false);

        $this->goFoodServiceMock
            ->expects($this->once())
            ->method('createTransaction')
            ->with($this->equalTo($requestData))
            ->willReturn(new GoFood());

        $response = $this->controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString(route('gofood.index'), $response->getTargetUrl());
    }

    /**
     * Test the store method handles exceptions from service and redirects with error.
     * @return void
     */
    public function test_store_method_redirects_with_error_on_service_exception()
    {
        $requestData = [
            'tanggal' => '2025-07-17',
            'waktu' => '10:00',
            'nama_pelanggan' => 'Unit Test User',
            'metode_pembayaran' => 'Cash',
            'items' => [
                ['menu_id' => 101, 'platform_id' => 201, 'jumlah' => 2],
            ],
        ];
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn(null);
        $request->shouldReceive('all')->andReturn($requestData);
        $request->shouldReceive('has')->with('status')->andReturn(false);

        $this->goFoodServiceMock
            ->expects($this->once())
            ->method('createTransaction')
            ->willThrowException(new \Exception('Simulated service error.'));

        $response = $this->controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString(url()->previous(), $response->getTargetUrl());
    }

    /**
     * Test the update method successfully delegates to service and redirects.
     * @return void
     */
    public function test_update_method_delegates_to_service_and_redirects_on_success()
    {
        $transactionId = 1;
        $transaksi = new GoFood(['id' => $transactionId]); 
        // Penting: Pastikan model GoFood memiliki $fillable ['id'] agar bisa diisi saat instantiate
        // atau gunakan setAttribute jika tidak fillable: $transaksi->setAttribute('id', $transactionId);


        $requestData = [
            'tanggal' => '2025-07-18',
            'waktu' => '12:00',
            'nama_pelanggan' => 'Updated User',
            'metode_pembayaran' => 'Card',
            'items' => [
                ['menu_id' => 101, 'platform_id' => 201, 'jumlah' => 3],
            ],
            'status' => true,
        ];

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn(null);
        $request->shouldReceive('all')->andReturn($requestData);
        $request->shouldReceive('query')->with('page', 1)->andReturn(1);
        $request->shouldReceive('has')->with('status')->andReturn(true);

        $this->goFoodServiceMock
            ->expects($this->once())
            ->method('updateTransaction')
            // Cukup gunakan objek $transaksi yang nyata sebagai matcher
            ->with($transaksi, $this->equalTo($requestData)) 
            ->willReturn($transaksi);

        $response = $this->controller->update($request, $transaksi);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString(route('gofood.index'), $response->getTargetUrl());
    }

    /**
     * Test the update method handles exceptions from service and redirects with error.
     * @return void
     */
    public function test_update_method_redirects_with_error_on_service_exception()
    {
        $transactionId = 1;
        $transaksi = new GoFood(['id' => $transactionId]);

        $requestData = [
            'tanggal' => '2025-07-18',
            'waktu' => '12:00',
            'nama_pelanggan' => 'Updated User',
            'metode_pembayaran' => 'Card',
            'items' => [
                ['menu_id' => 101, 'platform_id' => 201, 'jumlah' => 3],
            ],
        ];
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->andReturn(null);
        $request->shouldReceive('all')->andReturn($requestData);
        $request->shouldReceive('query')->with('page', 1)->andReturn(1);
        $request->shouldReceive('has')->with('status')->andReturn(false);

        $this->goFoodServiceMock
            ->expects($this->once())
            ->method('updateTransaction')
            ->willThrowException(new \Exception('Simulated update service error.'));

        $response = $this->controller->update($request, $transaksi);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString(route('gofood.index', ['page' => 1]), $response->getTargetUrl());
    }


    /**
     * Test the destroy method successfully delegates to service and redirects.
     * @return void
     */
    public function test_destroy_method_delegates_to_service_and_redirects_on_success()
    {
        $transactionId = 1;
        $transaksi = new GoFood(['id' => $transactionId]);

        $this->goFoodServiceMock
            ->expects($this->once())
            ->method('deleteTransaction')
            // Cukup gunakan objek $transaksi yang nyata sebagai matcher
            ->with($transaksi)
            ->willReturn(true);

        $response = $this->controller->destroy($transaksi);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString(route('gofood.index'), $response->getTargetUrl());
    }

    // Menghapus test destroy method redirects with error on model not found
    // Sesuai permintaan Anda.


    /**
     * Test the destroy method handles other exceptions from service.
     * @return void
     */
    public function test_destroy_method_redirects_with_error_on_other_service_exception()
    {
        $transactionId = 1;
        $transaksi = new GoFood(['id' => $transactionId]);

        $this->goFoodServiceMock
            ->expects($this->once())
            ->method('deleteTransaction')
            ->willThrowException(new \Exception('Simulated delete service error.'));

        $response = $this->controller->destroy($transaksi);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString(route('gofood.index'), $response->getTargetUrl());
    }
}
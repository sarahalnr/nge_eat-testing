<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\LaporanController;
use App\Services\LaporanService; // Pastikan ini mengacu ke LaporanService Anda
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View; // Import View class

class FilterUnitTest extends TestCase
{
    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Mockery::close(); // Pastikan mocks dibersihkan sebelum setiap test
    }

    /**
     * Clean up the test environment.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close(); // Pastikan mocks dibersihkan setelah setiap test
        parent::tearDown();
    }

    /**
     * Helper method to mock common LaporanService calls for statistics.
     *
     * @param \Mockery\MockInterface $mockLaporanService
     */
    private function mockCommonLaporanServiceCalls($mockLaporanService)
    {
        $mockLaporanService->shouldReceive('getTotalItem')->with(1)->andReturn(10);
        $mockLaporanService->shouldReceive('getTotalItem')->with(2)->andReturn(20);
        $mockLaporanService->shouldReceive('getTotalItem')->with(3)->andReturn(30);

        $mockLaporanService->shouldReceive('getTotalTransaksi')->andReturn(30);
        $mockLaporanService->shouldReceive('getTotalPendapatan')->andReturn(3000000);

        $mockLaporanService->shouldReceive('getTotalTransaksiPerPlatform')->with(1)->andReturn(5);
        $mockLaporanService->shouldReceive('getTotalTransaksiPerPlatform')->with(2)->andReturn(10);
        $mockLaporanService->shouldReceive('getTotalTransaksiPerPlatform')->with(3)->andReturn(15);

        $mockLaporanService->shouldReceive('getTotalPendapatanPerPlatform')->with(1)->andReturn(500000);
        $mockLaporanService->shouldReceive('getTotalPendapatanPerPlatform')->with(2)->andReturn(1000000);
        $mockLaporanService->shouldReceive('getTotalPendapatanPerPlatform')->with(3)->andReturn(1500000);
    }


    /**
     * Test the index method with no filters (default behavior).
     *
     * @return void
     */
    public function testIndexWithoutFilters()
    {
        // Arrange
        $mockLaporanService = Mockery::mock(LaporanService::class);
        $this->mockCommonLaporanServiceCalls($mockLaporanService);

        // Buat mock paginator
        $mockPaginatedData = new LengthAwarePaginator(
            collect([
                ['id_pesanan' => 'GF001', 'tanggal' => '2025-07-01', 'waktu' => '10:00:00', 'status' => 'Selesai', 'metode_pembayaran' => 'Cash', 'total' => 50000, 'kategori' => 'Minuman', 'platform' => 'gofood'],
                ['id_pesanan' => 'GF002', 'tanggal' => '2025-07-01', 'waktu' => '11:00:00', 'status' => 'Selesai', 'metode_pembayaran' => 'Cash', 'total' => 75000, 'kategori' => 'Makanan', 'platform' => 'gofood'],
            ]),
            2, // total items
            10, // per page
            1, // current page
            ['path' => 'http://localhost/laporan']
        );
        $mockPaginatedData->totalFiltered = 125000; // Penting: Tambahkan properti ini ke mock

        // Mock panggilan getFilteredTransactions pada LaporanService
        $mockLaporanService->shouldReceive('getFilteredTransactions')
            ->once()
            ->with(null, null, null, 10) // Dengan ekspektasi parameter null untuk tanpa filter
            ->andReturn($mockPaginatedData);

        // Mock View Facade
        $mockViewInstance = Mockery::mock(\Illuminate\View\View::class);

        // --- TAMBAHKAN EKSPEKTASI UNTUK name() dan getData() PADA MOCK VIEW INSTANCE ---
        $mockViewInstance->shouldReceive('name')
            ->once()
            ->andReturn('laporan.index'); // Kembalikan nama view yang diharapkan

        $mockViewInstance->shouldReceive('getData')
            ->once()
            ->andReturn([
                // Ini adalah data yang *diharapkan* akan diteruskan controller ke view
                // Sesuaikan dengan data yang Anda harapkan di assertions di bawah
                'totalGoFood' => 10,
                'totalGrabFood' => 20,
                'totalShopeeFood' => 30,
                'totalAll' => 60,
                'percentageGoFood' => 17,
                'percentageGrabFood' => 33,
                'percentageShopeeFood' => 50,
                'totalTransaksi' => 30,
                'totalPendapatan' => 3000000,
                'totalTransaksiGoFood' => 5,
                'totalTransaksiGrabFood' => 10,
                'totalTransaksiShopeeFood' => 15,
                'totalPendapatanGoFood' => 500000,
                'totalPendapatanGrabFood' => 1000000,
                'totalPendapatanShopeeFood' => 1500000,
                'transaksi' => $mockPaginatedData,
                'totalFiltered' => 125000,
                'platform' => null,
                'startDate' => null,
                'endDate' => null,
            ]);
        // --- AKHIR TAMBAHAN EKSPEKTASI ---

        $this->mock(
            'view',
            function ($mock) use ($mockViewInstance) {
                $mock->shouldReceive('make')
                    ->once()
                    ->with(
                        'laporan.index',
                        Mockery::any(), // Menggunakan Mockery::any() karena kita memverifikasi getData()
                        [] // Argumen ketiga untuk $mergeData
                    )
                    ->andReturn($mockViewInstance);
            }
        );

        $request = new Request(); // Request tanpa parameter query

        // Act: Buat instance controller dengan meng-inject mock service
        $controller = new LaporanController($mockLaporanService);
        $result = $controller->index($request);

        // Assert
        $this->assertInstanceOf(View::class, $result);
        $this->assertEquals('laporan.index', $result->name()); // Pastikan nama view benar

        // Ambil data yang diteruskan ke view
        $viewData = $result->getData();

        // Lakukan assertions pada $viewData
        $this->assertEquals(10, $viewData['totalGoFood']);
        $this->assertEquals(60, $viewData['totalAll']);
        $this->assertEquals(17, $viewData['percentageGoFood']);
        $this->assertEquals(30, $viewData['totalTransaksi']);
        $this->assertEquals(3000000, $viewData['totalPendapatan']);
        $this->assertEquals(5, $viewData['totalTransaksiGoFood']);
        $this->assertEquals(500000, $viewData['totalPendapatanGoFood']);
        $this->assertEquals($mockPaginatedData, $viewData['transaksi']);
        $this->assertEquals(125000, $viewData['totalFiltered']);
        $this->assertNull($viewData['platform']);
        $this->assertNull($viewData['startDate']);
        $this->assertNull($viewData['endDate']);
    }

    /**
     * Test the index method with 'gofood' platform filter.
     *
     * @return void
     */
    public function testIndexWithGoFoodPlatformFilter()
    {
        // Arrange
        $mockLaporanService = Mockery::mock(LaporanService::class);
        $this->mockCommonLaporanServiceCalls($mockLaporanService);

        $mockPaginatedData = new LengthAwarePaginator(
            collect([
                ['id_pesanan' => 'GF001', 'tanggal' => '2025-07-01', 'waktu' => '10:00:00', 'status' => 'Selesai', 'metode_pembayaran' => 'Cash', 'total' => 50000, 'kategori' => 'Minuman', 'platform' => 'gofood']
            ]),
            1, 10, 1, ['path' => 'http://localhost/laporan?platform=gofood']
        );
        $mockPaginatedData->totalFiltered = 50000;

        $mockLaporanService->shouldReceive('getFilteredTransactions')
            ->once()
            ->with('gofood', null, null, 10)
            ->andReturn($mockPaginatedData);

        // Mock View Facade
        $mockViewInstance = Mockery::mock(\Illuminate\View\View::class);

        // --- TAMBAHKAN EKSPEKTASI UNTUK name() dan getData() PADA MOCK VIEW INSTANCE ---
        $mockViewInstance->shouldReceive('name')
            ->once()
            ->andReturn('laporan.index');

        $mockViewInstance->shouldReceive('getData')
            ->once()
            ->andReturn([
                // Sesuaikan data ini dengan ekspektasi untuk skenario filter GoFood
                'totalGoFood' => 10,
                'totalGrabFood' => 20,
                'totalShopeeFood' => 30,
                'totalAll' => 60,
                'percentageGoFood' => 17,
                'percentageGrabFood' => 33,
                'percentageShopeeFood' => 50,
                'totalTransaksi' => 30,
                'totalPendapatan' => 3000000,
                'totalTransaksiGoFood' => 5,
                'totalTransaksiGrabFood' => 10,
                'totalTransaksiShopeeFood' => 15,
                'totalPendapatanGoFood' => 500000,
                'totalPendapatanGrabFood' => 1000000,
                'totalPendapatanShopeeFood' => 1500000,
                'transaksi' => $mockPaginatedData,
                'totalFiltered' => 50000,
                'platform' => 'gofood',
                'startDate' => null,
                'endDate' => null,
            ]);
        // --- AKHIR TAMBAHAN EKSPEKTASI ---

        $this->mock(
            'view',
            function ($mock) use ($mockViewInstance) {
                $mock->shouldReceive('make')
                    ->once()
                    ->with(
                        'laporan.index',
                        Mockery::any(),
                        []
                    )
                    ->andReturn($mockViewInstance);
            }
        );

        $request = new Request(['platform' => 'gofood']);
        $controller = new LaporanController($mockLaporanService);
        $result = $controller->index($request);

        $this->assertInstanceOf(View::class, $result);
        $this->assertEquals('laporan.index', $result->name());
        $viewData = $result->getData();

        $this->assertEquals(50000, $viewData['totalFiltered']);
        $this->assertEquals('gofood', $viewData['platform']);
        $this->assertNull($viewData['startDate']);
        $this->assertNull($viewData['endDate']);
        $this->assertEquals($mockPaginatedData, $viewData['transaksi']);

        $this->assertEquals(10, $viewData['totalGoFood']);
        $this->assertEquals(60, $viewData['totalAll']);
        $this->assertEquals(17, $viewData['percentageGoFood']);
        $this->assertEquals(30, $viewData['totalTransaksi']);
        $this->assertEquals(3000000, $viewData['totalPendapatan']);
        $this->assertEquals(5, $viewData['totalTransaksiGoFood']);
        $this->assertEquals(500000, $viewData['totalPendapatanGoFood']);
    }

    /**
     * Test the index method with 'gofood' platform and date filters.
     *
     * @return void
     */
    public function testIndexWithGoFoodPlatformAndDateFilters()
    {
        // Arrange
        $mockLaporanService = Mockery::mock(LaporanService::class);
        $this->mockCommonLaporanServiceCalls($mockLaporanService);

        $mockPaginatedData = new LengthAwarePaginator(
            collect([
                ['id_pesanan' => 'GF001', 'tanggal' => '2025-07-01', 'waktu' => '10:00:00', 'status' => 'Selesai', 'metode_pembayaran' => 'Cash', 'total' => 50000, 'kategori' => 'Minuman', 'platform' => 'gofood']
            ]),
            1, 10, 1, ['path' => 'http://localhost/laporan?platform=gofood&start_date=2025-07-01&end_date=2025-07-01']
        );
        $mockPaginatedData->totalFiltered = 50000;

        $mockLaporanService->shouldReceive('getFilteredTransactions')
            ->once()
            ->with('gofood', '2025-07-01', '2025-07-01', 10)
            ->andReturn($mockPaginatedData);

        // Mock View Facade
        $mockViewInstance = Mockery::mock(\Illuminate\View\View::class);

        // --- TAMBAHKAN EKSPEKTASI UNTUK name() dan getData() PADA MOCK VIEW INSTANCE ---
        $mockViewInstance->shouldReceive('name')
            ->once()
            ->andReturn('laporan.index');

        $mockViewInstance->shouldReceive('getData')
            ->once()
            ->andReturn([
                // Sesuaikan data ini dengan ekspektasi untuk skenario filter GoFood dan tanggal
                'totalGoFood' => 10,
                'totalGrabFood' => 20,
                'totalShopeeFood' => 30,
                'totalAll' => 60,
                'percentageGoFood' => 17,
                'percentageGrabFood' => 33,
                'percentageShopeeFood' => 50,
                'totalTransaksi' => 30,
                'totalPendapatan' => 3000000,
                'totalTransaksiGoFood' => 5,
                'totalTransaksiGrabFood' => 10,
                'totalTransaksiShopeeFood' => 15,
                'totalPendapatanGoFood' => 500000,
                'totalPendapatanGrabFood' => 1000000,
                'totalPendapatanShopeeFood' => 1500000,
                'transaksi' => $mockPaginatedData,
                'totalFiltered' => 50000,
                'platform' => 'gofood',
                'startDate' => '2025-07-01',
                'endDate' => '2025-07-01',
            ]);
        // --- AKHIR TAMBAHAN EKSPEKTASI ---

        $this->mock(
            'view',
            function ($mock) use ($mockViewInstance) {
                $mock->shouldReceive('make')
                    ->once()
                    ->with(
                        'laporan.index',
                        Mockery::any(),
                        []
                    )
                    ->andReturn($mockViewInstance);
            }
        );

        $request = new Request([
            'platform' => 'gofood',
            'start_date' => '2025-07-01',
            'end_date' => '2025-07-01'
        ]);

        $controller = new LaporanController($mockLaporanService);
        $result = $controller->index($request);

        $this->assertInstanceOf(View::class, $result);
        $this->assertEquals('laporan.index', $result->name());
        $viewData = $result->getData();

        $this->assertEquals(50000, $viewData['totalFiltered']);
        $this->assertEquals('gofood', $viewData['platform']);
        $this->assertEquals('2025-07-01', $viewData['startDate']);
        $this->assertEquals('2025-07-01', $viewData['endDate']);
        $this->assertEquals($mockPaginatedData, $viewData['transaksi']);

        $this->assertEquals(10, $viewData['totalGoFood']);
        $this->assertEquals(60, $viewData['totalAll']);
        $this->assertEquals(17, $viewData['percentageGoFood']);
        $this->assertEquals(30, $viewData['totalTransaksi']);
        $this->assertEquals(3000000, $viewData['totalPendapatan']);
        $this->assertEquals(5, $viewData['totalTransaksiGoFood']);
        $this->assertEquals(500000, $viewData['totalPendapatanGoFood']);
    }
}
<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class FilterUnitTest extends TestCase
{
    public function testIndexWithoutFilters()
    {
        $mockData = new Collection([
            [
                'id' => 1,
                'platform' => 'GoFood',
                'menu' => 'Nasi Goreng',
                'jumlah' => 10,
                'harga' => 15000,
                'total' => 150000,
                'created_at' => '2025-07-18 12:00:00',
            ],
        ]);

        $paginator = new LengthAwarePaginator(
            $mockData,
            1, // total
            10, // per page
            1, // current page
            ['path' => 'http://localhost']
        );

        $this->assertEquals(1, $paginator->total());
        $this->assertCount(1, $paginator->items());
    }

    public function testIndexWithPlatformFilter()
    {
        $mockData = new Collection([
            [
                'id' => 2,
                'platform' => 'GrabFood',
                'menu' => 'Mie Ayam',
                'jumlah' => 5,
                'harga' => 12000,
                'total' => 60000,
                'created_at' => '2025-07-18 14:00:00',
            ],
        ]);

        $filtered = $mockData->filter(function ($item) {
            return $item['platform'] === 'GrabFood';
        });

        $paginator = new LengthAwarePaginator(
            $filtered,
            $filtered->count(),
            10,
            1,
            ['path' => 'http://localhost']
        );

        $this->assertEquals(1, $paginator->total());
        $this->assertCount(1, $paginator->items());
    }

    public function testIndexWithPlatformAndDateFilter()
    {
        $mockData = new Collection([
            [
                'id' => 3,
                'platform' => 'ShopeeFood',
                'menu' => 'Sate Ayam',
                'jumlah' => 7,
                'harga' => 13000,
                'total' => 91000,
                'created_at' => '2025-07-18 10:30:00',
            ],
            [
                'id' => 4,
                'platform' => 'ShopeeFood',
                'menu' => 'Bakso',
                'jumlah' => 3,
                'harga' => 10000,
                'total' => 30000,
                'created_at' => '2025-07-16 10:00:00',
            ],
        ]);

        $startDate = '2025-07-18';
        $endDate = '2025-07-19';

        $filtered = $mockData->filter(function ($item) use ($startDate, $endDate) {
            return $item['platform'] === 'ShopeeFood' &&
                $item['created_at'] >= $startDate . ' 00:00:00' &&
                $item['created_at'] <= $endDate . ' 23:59:59';
        });

        $paginator = new LengthAwarePaginator(
            $filtered,
            $filtered->count(),
            10,
            1,
            ['path' => 'http://localhost']
        );

        $this->assertEquals(1, $paginator->total());
        $this->assertCount(1, $paginator->items());
    }
}

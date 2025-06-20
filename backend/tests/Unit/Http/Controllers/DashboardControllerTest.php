<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\DashboardController;
use App\Interfaces\DashboardRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(DashboardRepositoryInterface::class);
        $this->app->instance(DashboardRepositoryInterface::class, $this->repositoryMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSalesSummaryReturnsJsonResponseWithCorrectData()
    {
        $mockData = [
            'total_orders' => 21350,
            'total_revenue' => 817860.05,
            'top_pizzas' => [
                ['name' => 'Classic Deluxe', 'total_quantity' => 2500],
                ['name' => 'Barbeque Chicken', 'total_quantity' => 2000],
            ],
        ];
        $this->repositoryMock
            ->shouldReceive('getSalesSummary')
            ->once()
            ->andReturn($mockData);

        $controller = new DashboardController($this->repositoryMock);

        $response = $controller->salesSummary();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = $response->getData(true);
        $this->assertEquals(21350, $responseData['total_orders']);
        $this->assertEquals(817860.05, $responseData['total_revenue']);
        $this->assertCount(2, $responseData['top_pizzas']);
        $this->assertEquals('Classic Deluxe', $responseData['top_pizzas'][0]['name']);
        $this->assertEquals(2500, $responseData['top_pizzas'][0]['total_quantity']);
    }
}

<?php

namespace Tests\Unit;

use App\Models\Progress;
use App\Models\User;
use App\Repositories\ProgressRepository;
use App\Repositories\QAndARepository;
use App\Services\ProgressService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ProgressServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testListAnsweredReturnsArray()
    {
        $progress1 = new Progress();
        $progress1->q_and_A_id = 1;
        $progress1->answered = true;
        $progress2 = new Progress();
        $progress2->answered = true;

        $collection = Collection::make(
            [
                $progress1,
                $progress2
            ]
        );
        $progressRepository = Mockery::mock(
            ProgressRepository::class
        )->shouldReceive("listAnswered")
            ->once()
            ->andReturn($collection)
            ->getMock();

        $qAndARepository = Mockery::mock(QAndARepository::class);

        $user = new User();

        /**
         * @var ProgressService $progressService
         */
        $progressService = Mockery::mock(
            ProgressService::class,
            [$progressRepository, $qAndARepository]
        )->shouldReceive("listAnswered")
            ->once()
            ->passthru()
            ->getMock();

        $response = $progressService->listAnswered($user);

        $this->assertCount(2,$response);
    }
}

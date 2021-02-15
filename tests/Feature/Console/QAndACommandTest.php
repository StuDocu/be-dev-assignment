<?php
declare(strict_types=1);

namespace Tests\Feature\Console;

use Tests\TestCase;
use App\Console\Commands\QAndA;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QAndACommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_qanda_command(): void
    {
        $this->assertTrue(class_exists(QAndA::class));
    }

    /** @test */
    public function user_should_be_able_to_add_a_question(): void
    {
        $this->artisan('qanda:interactive')
            ->expectsQuestion('Select what you want to do?', 'Create a new question')
            ->expectsQuestion('What is your question?', 'Who was the first man to walk on the moon?')
            ->expectsQuestion('What is your answer?', 'Neil Armstrong')
            ->expectsOutput('Your question is stored successfully.')
            ->expectsQuestion('Do you want to add another question?', 'n')
            ->assertExitCode(0);

        $this->assertDatabaseHas('questions', [
            'question' => 'Who was the first man to walk on the moon?',
            'answer' => 'Neil Armstrong',
        ]);
    }
}

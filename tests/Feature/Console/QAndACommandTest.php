<?php
declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Questionnaire\Models\Question;
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
            ->expectsQuestion('Do you want to add another question? (y/n)', 'n')
            ->assertExitCode(0);

        $this->assertDatabaseHas('questions', [
            'question' => 'Who was the first man to walk on the moon?',
            'answer' => 'Neil Armstrong',
        ]);
    }

    /** @test */
    public function user_should_be_able_to_practise_question(): void
    {
        $question1 = factory(Question::class, 1)->create([
            'question' => 'question 1',
            'answer' => 'A1'
        ]);

        $question2 = factory(Question::class, 1)->create([
            'question' => 'question 2'
        ]);

        $this->artisan('qanda:interactive')
            ->expectsQuestion('Select what you want to do?', 'Practise the questions')
            ->expectsQuestion('Select a question to practise?', 'question 1')
            ->expectsQuestion('What is your answer?', 'A1')
            ->expectsOutput('Your answer is stored successfully.')
            ->expectsQuestion('Do you want to practise another question? (y/n)', 'n')
            ->expectsOutput('Here is your progress')
            ->assertExitCode(0);

        $this->assertDatabaseHas('user_answers', [
            'question_id' => 2,
            'answered' => 1,
        ]);
    }
}

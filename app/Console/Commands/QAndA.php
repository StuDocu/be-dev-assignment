<?php

namespace App\Console\Commands;

use App\Questionnaire\Configuration;
use App\Questionnaire\Question;
use Illuminate\Console\Command;

class QAndA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs an interactive command line based Q And A system.';

    /**
     * @var Question
     */
    private $question;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Question $question)
    {
        parent::__construct();

        $this->question = $question;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Hello world!');

        $answer = $this->choice('Select what you want to do?', Configuration::CHOICES);

        switch ($answer) {
            case Configuration::CHOICES[0]:
                $this->createNewQuestion();
                break;
            case Configuration::CHOICES[1]:
                $this->practiseQuestions();
                break;
        }
    }

    private function createNewQuestion(): void
    {
        do {
            $this->askQuestion();
            $anotherQuestionInput = $this->ask('Do you want to add another question? (y/n)');
            if ($anotherQuestionInput === 'y') {
                $this->askQuestion();
            } else {
                break;
            }
        } while (true);
    }

    private function askQuestion(): void
    {
        $questionInput = $this->ask('What is your question?');
        if (!$questionInput) {
            $this->error('This is required field.');

            exit(0);
        }

        $answer = $this->ask('What is your answer?');
        if (!$answer) {
            $this->error('This is required field.');

            exit(0);
        }

        $this->question->save($questionInput, $answer);

        $this->line('Your question is stored successfully.');
    }

    private function practiseQuestions()
    {
        do {
            $this->chooseAQuestion();
            $anotherQuestionInput = $this->ask('Do you want to practise another question? (y/n)');
            if ($anotherQuestionInput === 'y') {
                $this->chooseAQuestion();
            } else {
                $this->line('Here is your progress');
                $this->printProgress();
                break;
            }
        } while (true);
    }

    private function chooseAQuestion(): void
    {
        $questions = $this->question->listQuestions();

        if (!$questions) {
            $this->error('You do NOT have any questions to practise.');

            exit(0);
        }

        $selectedQuestion = $this->choice('Select a question to practise?', $questions);

        $userAnswer = $this->ask('What is your answer?');

        $result = $this->question->storeAnswer($selectedQuestion, $userAnswer);

        if ($result) {
            $this->line('Your answer is stored successfully.');
        } else {
            $this->error('Something went wrong while storing you answer.');
        }
    }

    private function printProgress(): void
    {
        $headers = ['Question', 'Answered'];

        $this->table($headers, $this->question->listUserAnswers());
    }

}

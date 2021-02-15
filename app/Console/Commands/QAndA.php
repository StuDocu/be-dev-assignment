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

        if ($answer === Configuration::CHOICES[0]) {
            do {
                $this->askQuestion();
                $anotherQuestionInput = $this->ask('Do you want to add another question?');
                if ($anotherQuestionInput === 'y') {
                    $this->askQuestion();
                } else {
                    break;
                }
            } while (true);
        }
    }

    private function askQuestion(): void
    {
        $questionInput = $this->ask('What is your question?');
        $answer = $this->ask('What is your answer?');

        $this->question->save($questionInput, $answer);

        $this->line('Your question is stored successfully.');
    }
}

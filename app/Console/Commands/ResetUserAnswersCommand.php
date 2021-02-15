<?php

namespace App\Console\Commands;

use App\Questionnaire\Question;
use Illuminate\Console\Command;

class ResetUserAnswersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all the user answers';

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
        $confirm = $this->confirm('Do you really want to reset?');

        if ($confirm) {
            $this->question->resetUserAnswers();
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Exceptions\QAndANotFound;
use App\Models\User;
use App\Services\ProgressService;
use App\Services\QAndAService;
use App\Services\UserService;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param UserService $userService
     * @param QAndAService $qAndAService
     * @param ProgressService $progressService
     * @return mixed
     */
    public function handle(
        UserService $userService,
        QAndAService $qAndAService,
        ProgressService $progressService
    ) {
        $this->info("First lets determine your identity");

        $email = $this->ask('User email');
        $password = $this->secret('User password');

        $user = $userService->getByEmailAndPassword(
            $email,
            $password
        );

        if (!$user) {
            $this->alert('The email/password is incorrect, can\'t continue');
            return;
        }

        $option = '';

        while($option != 'Exit') {
            $option = $this->choseOption('Exit');
            switch ($option) {
                case 'Create a new question':
                    $this->createQAndA(
                        $qAndAService,
                        $user
                    );
                    break;
                case 'View previously entered questions - answers':
                    $this->viewQAndAs(
                        $qAndAService,
                        $progressService,
                        $user
                    );
                    break;

                case 'Exit':
                default:
                    break;
            }
        }
    }

    /**
     * Create a new QAndA entry
     *
     * @param QAndAService $qAndAService
     * @param User $user
     */
    private function createQAndA(
        QAndAService $qAndAService,
        User $user
    ) {
        $question = $this->ask('Input the question');
        $answer = $this->ask('Input the answer');

        $qAndA = $qAndAService->createAndSave(
            $question,
            $answer,
            $user
        );

        if (!$qAndA) {
            $this->alert("The question/answer cannot be created, try again");
        }
    }

    /**
     * Show questions and allow the User to answer them
     *
     * @param QAndAService $qAndAService
     * @param ProgressService $progressService
     * @param User $user
     */
    private function viewQAndAs(
        QAndAService $qAndAService,
        ProgressService $progressService,
        User $user
    ): void {
        $exit = false;
        $attempted = [];
        while($exit != true) {
            $answeredQuestions = $progressService->listAnswered($user);
            $answered = count($answeredQuestions);
            $questions = $qAndAService->getQuestionsOfUser($user);

            $this->info("0.- Exit practice");
            foreach ($questions as $key => $question) {
                $answerStatus = '';
                if (in_array($key, $answeredQuestions))
                    $answerStatus = ' - (Answered)';

                $this->info("$key.- $question$answerStatus");
            }

            $questionId = $this->ask('Id of question to practice?');

            if ($questionId == 0) {
                $exit = true;
                continue;
            }

            if (in_array($questionId, $answeredQuestions)) {
                if ($this->confirm('You have already answered this question correctly, skip?'))
                    continue;
                else
                    $answered--;
            }

            if (!in_array($questionId, $attempted)) {
                $attempted[] = $questionId;
            }

            $answer = $this->ask('Answer');

            try {
                $questionAnswered = $qAndAService->checkAnswerById($questionId, $answer);
                if ($questionAnswered)
                    $answered++;

                $progressOfQuestion = $progressService->getByQAndAId($questionId);
                if (!$progressOfQuestion)
                    $progressService->createAndSave($questionAnswered, $questionId);
                else
                    $progressService->update($progressOfQuestion, $questionAnswered);
            } catch (QAndANotFound $exception) {
                $this->alert("The question with the provided id cannot be located, make sure it hasn't been deleted");
            }

            if ($answered == count($questions)) {
                $this->info("You have already answered all the questions right, you'll be prompted out");
                $exit = true;
            } else if (count($attempted) == count($questions)) {
                $exit = $this->confirm('You have attempted all the questions but still have erroneous answers. Want to exit?');
            }
        }
    }

    /**
     * Present the main option menu
     *
     * @param string $defaultIndex
     * @return string
     */
    private function choseOption(string $defaultIndex): string
    {
        return $this->choice(
            'Chose an action',
            [
                'Create a new question and answer',
                'View previously entered questions - answers',
                'Exit'
            ],
            $defaultIndex,
            $maxAttempts = 3,
            $allowMultipleSelections = false
        );
    }
}

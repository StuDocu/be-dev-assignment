<?php

namespace App\Console\Commands;

use App\Exceptions\QAndANotFound;
use App\Models\User;
use App\Services\ProgressService;
use App\Services\QAndAService;
use App\Services\UserService;
use Illuminate\Console\Command;

class QAndAReset extends Command
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

        if (!$this->confirm('Are you sure you want to delete your progress?'))
            return;

        $progressService->deleteFromUser($user);
    }
}
<?php

namespace App\Services;

use App\Models\Progress;
use App\Models\QAndA;
use App\Models\User;
use App\Repositories\ProgressRepository;
use App\Repositories\QAndARepository;
use Exception;

class ProgressService
{
    /**
     * @var ProgressRepository
     */
    private $progressRepository;

    /**
     * @var QAndARepository
     */
    private $qAndARepository;

    /**
     * ProgressService constructor.
     * @param ProgressRepository $progressRepository
     * @param QAndARepository $qAndARepository
     */
    public function __construct(
        ProgressRepository $progressRepository,
        QAndARepository $qAndARepository
    ) {
        $this->progressRepository = $progressRepository;
        $this->qAndARepository = $qAndARepository;
    }

    /**
     * Creates a new Progress object and saves it to the DB
     *
     * @param bool $answered
     * @param int $questionId
     * @return Progress|null
     */
    public function createAndSave(bool $answered, int $questionId): ?Progress
    {
        $qAndA = $this->qAndARepository->getById($questionId);
        $progress = new Progress();
        $progress->answered = $answered;
        $progress->qAndA()->associate($qAndA);

        $progress->save();

        return $progress;
    }

    /**
     * Updates an existing Progress object
     *
     * @param Progress $progress
     * @param bool $answered
     * @return Progress|null
     */
    public function update(Progress $progress, bool $answered): ?Progress
    {
        $progress->answered = $answered;

        $progress->save();

        return $progress;
    }

    /**
     * Retrieves the Progress of a specific Question(QAndA)
     *
     * @param int $questionId
     * @return Progress|null
     */
    public function getByQAndAId(int $questionId): ?Progress
    {
        return $this->progressRepository->getByQuestionId($questionId);
    }

    /**
     * Retrieves an array that contains the questions
     * that have been answered correctly
     *
     * @param User $user
     * @return array
     */
    public function listAnswered(User $user): array
    {
        $qAndAs = $user->qAndAs()->get();
        $questions = [];
        foreach ($qAndAs as $qAndA) {
            $questions[] = $qAndA->id;
        }
        try {
            $answersProgress = $this->progressRepository->listAnswered($questions);
            $answers = [];
            foreach ($answersProgress as $answer) {
                $answers[] = $answer->q_and_a_id;
            }

            return $answers;
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * Delete the progress from the user
     *
     * @param User $user
     * @return void
     */
    public function deleteFromUser(User $user): void
    {
        $qAndAs = $user->qAndAs()->get();
        $questions = [];
        foreach ($qAndAs as $qAndA) {
            $questions[] = $qAndA->id;
        }

        $this->progressRepository->deleteFromQuestionIds($questions);
    }
}
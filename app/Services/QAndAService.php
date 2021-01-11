<?php

namespace App\Services;

use App\Exceptions\QAndANotFound;
use App\Models\QAndA;
use App\Models\User;
use App\Repositories\QAndARepository;

class QAndAService
{
    /**
     * @var QAndARepository
     */
    private $qAndARepository;

    /**
     * QAndAService constructor.
     * @param QAndARepository $qAndARepository
     */
    public function __construct(QAndARepository $qAndARepository)
    {
        $this->qAndARepository = $qAndARepository;
    }

    /**
     * Creates and saves a new QAndA object
     *
     * @param string $question
     * @param string $answer
     * @param User $user
     * @return QAndA|null
     */
    public function createAndSave(
        string $question,
        string $answer,
        User $user
    ): ?QAndA {
        $qAndA = new QAndA();

        $qAndA->question = $question;
        $qAndA->answer = $answer;

        $user->qAndAs()->save($qAndA);

        return $qAndA;
    }

    /**
     * Retrieves a list of Questions(QAndA) that belong to a User
     *
     * @param User $user
     * @return array
     */
    public function getQuestionsOfUser(User $user): array
    {
        $qAndAs = $user->qAndAs()->get();
        $questions = [];
        foreach ($qAndAs as $qAndA) {
            $questions[$qAndA->id] = $qAndA->question;
        }

        return $questions;
    }

    /**
     * Check that an answer is correct for a specific question
     *
     * @param int $id
     * @param string $answer
     * @return bool
     *
     * @throws QAndANotFound
     */
    public function checkAnswerById(int $id, string $answer): bool
    {
        $question = $this->qAndARepository->getById($id);

        if (!$question) {
            throw new QAndANotFound(
                "The Question has not been found",
                404
            );
        }

        if (strcasecmp(trim($question->answer), trim($answer)) == 0) {
            return true;
        }

        return false;
    }
}
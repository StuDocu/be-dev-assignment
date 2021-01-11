<?php

namespace App\Repositories;

use App\Models\Progress;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgressRepository
{
    /**
     * List all the questions that have been answered correctly
     *
     * @param array $questionIds
     * @return mixed
     */
    public function listAnswered(array $questionIds): Collection
    {
        return Progress::whereIn('q_and_a_id', $questionIds)
            ->where('answered', true)
            ->get();
    }

    /**
     * Delete the Progresses with the supplied question ids
     *
     * @param array $questionIds
     */
    public function deleteFromQuestionIds(array $questionIds): void
    {
        Progress::whereIn('q_and_a_id', $questionIds)->delete();
    }

    /**
     * Retrieves the Progress of a question by the question Id
     * @param int $questionId
     * @return Progress|null
     */
    public function getByQuestionId(int $questionId): ?Progress
    {
        try {
            $progress = Progress::where('q_and_a_id', '=', $questionId)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return null;
        }

        return $progress;
    }
}
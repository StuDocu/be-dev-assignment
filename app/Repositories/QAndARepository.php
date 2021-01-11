<?php

namespace App\Repositories;

use App\Models\QAndA;

class QAndARepository
{
    /**
     * Retrieves a QAndA by its id
     *
     * @param int $id
     * @return QAndA|null
     */
    public function getById(int $id): ?QAndA
    {
        try {
            $qAndA = QAndA::find($id);
        } catch (\Exception $exception) {
            return null;
        }

        return $qAndA;
    }
}
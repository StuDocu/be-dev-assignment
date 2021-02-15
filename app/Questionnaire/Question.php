<?php
declare(strict_types=1);

namespace App\Questionnaire;

final class Question
{
    public function save(string $question, string $answer)
    {
        $questionModel = new Models\Question();

        $questionModel->question = $question;
        $questionModel->answer = $answer;

        $questionModel->save();
    }
}

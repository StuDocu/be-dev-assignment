<?php
declare(strict_types=1);

namespace App\Questionnaire;

final class Question
{
    public function save(string $question, string $answer)
    {
        return Models\Question::create([
            'question' => $question,
            'answer' => $answer
        ]);
    }

    public function listQuestions(): array
    {
        $result = [];
        foreach (Models\Question::all() as $key => $item) {
            $result[$key] = $item->getAttribute('question');
        }

        return $result;
    }

    public function storeAnswer(string $selectedQuestion, string $userAnswer)
    {
        $question = Models\Question::where('question', $selectedQuestion)->first();

        if (!$question) {
            return false;
        }

        return Models\UserAnswer::updateOrCreate(
            [
                'question_id' => $question->getAttribute('id'),
                'answered' => ($userAnswer === $question->getAttribute('answer'))
            ],
            [
                'question_id' => $question->getAttribute('id'),
            ]
        );
    }

    public function listUserAnswers()
    {
        $result = [];
        foreach (Models\Question::all() as $question) {
            $result[] = [
                'question' => $question->getAttribute('question'),
                'answered' => $question->userAnswer()->count(),
            ];
        }

        return $result;
    }
}

<?php

namespace App\Interfaces;

interface iquizInterface
{
    public function createQuiz(array $data);
    public function updateQuiz(int $id, array $data);
    public function deleteQuiz(int $id);
    public function getQuiz(int $id);
    public function addQuestion(int $quizId, array $questionData);
    public function updateQuestion(int $questionId, array $questionData);
    public function deleteQuestion(int $questionId);
    public function addAnswers(int $questionId, array $answers);
    public function updateAnswer(int $answerId, array $answerData);
    public function deleteAnswer(int $answerId);
    public function startQuizAttempt(int $enrollmentId, int $customerId);
    public function submitQuizAttempt(int $attemptId, array $answers);
    public function getQuizAttempts(int $customerId, int $quizId = null);
    public function getAttemptResults(int $attemptId);
    public function canAttemptQuiz(int $customerId, int $quizId);
    public function getQuizStatistics(int $quizId);
}

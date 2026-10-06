<?php

declare(strict_types=1);

namespace Tests\Integration\Classification;

use JevPHP\Classification\ChoiceAnswer;
use JevPHP\Classification\ChoiceType;
use JevPHP\Classification\JevClassifier;
use JevPHP\Classification\JevConfig;
use JevPHP\Classification\LayaConfig;
use JevPHP\Classification\NoulCriteria;
use JevPHP\Classification\NoulType;
use JevPHP\Classification\ScoreAnswer;
use JevPHP\Classification\ScoreCriteria;
use JevPHP\Classification\ScoreType;

it('can generate a noul answer with no criteria', function (JevConfig|LayaConfig $config) {
    $classifier = new JevClassifier($config);
    $questions = [
        'is_urgent' => new NoulType('Does this convey urgency?'),
    ];
    $response = $classifier->askQuestions('Help! My payouts have been failing for 3 days.', $questions);

    expect($response['is_urgent']->isTrue(0.7))->toBeTrue('Got a different response: '.json_encode($response));
    expect($response['is_urgent']->inputTokens)->toBeGreaterThan(0);
    expect($response['is_urgent']->outputTokens)->toBeGreaterThanOrEqual(0);
})->with([
    'Jev' => [new JevConfig()],
    'Laya' => [new LayaConfig()],
]);

it('can generate a noul answer with some criteria', function (JevConfig|LayaConfig $config) {
    $classifier = new JevClassifier($config);
    $questions = [
        'is_urgent' => new NoulType(
            'Does this convey urgency?',
            new NoulCriteria('Explicitly time-sensitive', 'No urgency expressed')),
    ];
    $response = $classifier->askQuestions('Help! My payouts have been failing for 3 days.', $questions);

    expect($response['is_urgent']->isTrue(0.7))->toBeTrue('Got a different response: '.json_encode($response));
    expect($response['is_urgent']->inputTokens)->toBeGreaterThan(0);
    expect($response['is_urgent']->outputTokens)->toBeGreaterThanOrEqual(0);
})->with([
    'Jev' => [new JevConfig()],
    'Laya' => [new LayaConfig()],
]);

it('can generate a choice answer with some criteria', function (JevConfig|LayaConfig $config) {
    $classifier = new JevClassifier($config);
    $questions = [
        'department' => new ChoiceType(
            'Which team should handle this?',
            [
                'billing' => 'Payments, invoicing, refunds',
                'technical' => 'Bugs, outages, integrations',
                'sales' => 'Pricing, upgrades, new accounts',
            ]
        ),
    ];
    $response = $classifier->askQuestions('Help! My payouts have been failing for 3 days.', $questions);

    $expected = [
        'department' => new ChoiceAnswer(
            choice: 'billing',
            probabilities: [
                'billing' => 0.88,
                'technical' => 0.12,
                'sales' => 0.0,
            ],
            confidence: 0.81
        ),
    ];

    expect($expected['department']->isSimilarTo($response['department']))->toBeTrue('Got a different response: '.json_encode($response));
    expect($response['department']->inputTokens)->toBeGreaterThan(0);
    expect($response['department']->outputTokens)->toBeGreaterThanOrEqual(0);
})->with([
    'Jev' => [new JevConfig()],
    'Laya' => [new LayaConfig()],
]);

it('can generate a score answer with some criteria', function (JevConfig|LayaConfig $config) {
    $classifier = new JevClassifier($config);
    $questions = [
        'frustration' => new ScoreType(
            'How frustrated is the customer?',
            new ScoreCriteria(['Calm', 'Frustrated', 'Very angry'])
        ),
    ];
    $response = $classifier->askQuestions('Help! My payouts have been failing for 3 days.', $questions);

    $expected = [
        'frustration' => new ScoreAnswer(
            score: 1.05,
            legend: [
                '0' => 'Calm',
                '1' => 'Frustrated',
                '2' => 'Very angry',
            ],
            probabilities: [
                '0' => 0.0,
                '1' => 0.95,
                '2' => 0.05,
            ],
            confidence: 0.92,
        ),
    ];

    expect($expected['frustration']->isSimilarTo($response['frustration']))->toBeTrue('Got a different response: '.json_encode($response));
    expect($response['frustration']->inputTokens)->toBeGreaterThan(0);
    expect($response['frustration']->outputTokens)->toBeGreaterThanOrEqual(0);
})->with([
    'Jev' => [new JevConfig()],
    'Laya' => [new LayaConfig()],
]);

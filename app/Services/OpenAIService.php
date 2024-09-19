<?php

// app/Services/OpenAIService.php
namespace App\Services;

use OpenAI;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function analyzeConclusion($conclusion)
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an assistant that analyzes student grades and provides insights.'
            ],
            [
                'role' => 'user',
                'content' => "Analyze this conclusion: " . json_encode($conclusion) .
                    ". Provide insights about the subjects needing more attention and where better performance is required. Keep it short as the analysis has a max_token of 100, precise, and sound professional."
            ]
        ];

        $response = $this->client->chat()->create([
            'model' => 'gpt-4o-mini', // or 'gpt-4o-mini' if that's available
            'messages' => $messages,
            'max_tokens' => 100,
        ]);

        return $response['choices'][0]['message']['content'] ?? 'No analysis provided.';
    }
}

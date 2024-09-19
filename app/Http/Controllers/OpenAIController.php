<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class OpenAIController extends Controller
{
    public function getCompletion(Request $request)
    {
        try {
            // Fetch the prompt from the request (optional, for dynamic prompts)
            $userPrompt = $request->input('prompt', 'Write a haiku about recursion in programming.');

            // Initialize OpenAI client with your API key
            $openai = OpenAI::client(apiKey: env('OPENAI_API_KEY'));

            // Make the API call
            $response = $openai->chat()->create([
                'model' => 'gpt-3.5-turbo',  // Or 'gpt-3.5-turbo', depending on your model choice
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            // Extract the response
            $message = $response['choices'][0]['message']['content'];

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}

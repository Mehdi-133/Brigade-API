<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
        $this->model  = config('services.groq.model', 'llama3-8b-8192');
    }

    public function analyzeDish(array $dietaryTags, string $dishName, array $ingredients): array
    {
        $prompt = $this->buildPrompt($dietaryTags, $dishName, $ingredients);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model'       => $this->model,
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a nutrition expert AI.'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'temperature' => 0.2,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Groq API error: ' . $response->body());
        }

        $output = trim($response->json()['choices'][0]['message']['content'] ?? '');
        $output = preg_replace('/```json|```/i', '', $output);
        $result = json_decode(trim($output), true);

        if (!$result || !isset($result['score'])) {
            throw new \RuntimeException('Invalid AI response: ' . $output);
        }

        return $result;
    }

    private function buildPrompt(array $dietaryTags, string $dishName, array $ingredients): string
    {
        $ingredientList = collect($ingredients)->map(function ($ingredient) {
            $tags = implode(', ', $ingredient['tags'] ?? []);
            return $ingredient['name'] . ($tags ? " (tags: {$tags})" : '');
        })->toArray();

        return
            "User dietary restrictions: " . json_encode($dietaryTags) . "\n" .
            "Dish name: {$dishName}\n" .
            "Ingredients: " . json_encode($ingredientList) . "\n\n" .
            "Rules:\n" .
            "- vegan: no animal products\n" .
            "- no_sugar: avoid sugar\n" .
            "- no_cholesterol: avoid cholesterol\n" .
            "- gluten_free: avoid gluten\n" .
            "- dairy_free: avoid dairy\n" .
            "- halal: halal only\n\n" .
            "Ingredient tags: contains_meat, contains_sugar, contains_cholesterol, contains_gluten, contains_lactose.\n\n" .
            "Return ONLY valid JSON (no markdown, no explanation). Format exactly like this:\n" .
            "{\"score\": number (0-10), \"warning_message\": string|null, \"status\": \"pending\"|\"improved\"|\"rejected\"}";
    }
}

<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Groq Client Service
 * 
 * Handles communication with Groq AI API for bill analysis.
 */
class GroqClient
{
    private string $apiKey;
    private string $baseUrl = 'https://api.groq.com/openai/v1';
    private string $model = 'llama-3.1-8b-instant';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        
        if (empty($this->apiKey)) {
            throw new AiException('GROQ_API_KEY is not configured');
        }
    }

    /**
     * Send chat request to Groq API
     * 
     * @param array $messages
     * @param array $options
     * @return array
     * @throws AiException
     */
    public function chat(array $messages, array $options = []): array
    {
        $model = $options['model'] ?? $this->model;
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 4000;

        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];

        // Note: Groq API doesn't support response_format parameter
        // We rely on prompt engineering to get JSON responses

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post(
                $this->baseUrl . '/chat/completions',
                $payload
            );

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'error' => $error,
                ]);
                throw new AiException(
                    'Groq API request failed: ' . ($error['error']['message'] ?? $response->body())
                );
            }

            $data = $response->json();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                throw new AiException('Invalid response format from Groq API');
            }

            return $data;
        } catch (\Exception $e) {
            if ($e instanceof AiException) {
                throw $e;
            }
            
            Log::error('Groq API request exception', [
                'message' => $e->getMessage(),
            ]);
            
            throw new AiException('Failed to communicate with Groq API: ' . $e->getMessage());
        }
    }

    /**
     * Extract content from API response
     * 
     * @param array $response
     * @return string
     */
    public function getResponseContent(array $response): string
    {
        return $response['choices'][0]['message']['content'] ?? '';
    }
}

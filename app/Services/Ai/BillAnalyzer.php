<?php

namespace App\Services\Ai;

use App\Models\Bill;

/**
 * Bill Analyzer Service
 * 
 * Orchestrates the AI analysis process by building prompts,
 * calling the AI service, and parsing responses.
 */
class BillAnalyzer
{
    public function __construct(
        private GroqClient $groqClient,
        private PromptBuilder $promptBuilder,
        private ResponseParser $responseParser
    ) {}

    /**
     * Analyze bill using AI
     * 
     * Builds the prompt, calls Groq API, and parses the response.
     * 
     * @param Bill $bill
     * @return array
     * @throws AiException
     */
    public function analyze(Bill $bill): array
    {
        if (empty($bill->raw_text)) {
            throw new AiException('Bill has no raw text to analyze');
        }

        // Build prompt
        $prompt = $this->promptBuilder->buildBillAnalysisPrompt($bill->raw_text);

        // Call Groq API
        $messages = [
            [
                'role' => 'system',
                'content' => 'Anda adalah ahli analisis tagihan rumah sakit. Selalu kembalikan response dalam format JSON yang valid.',
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ];

        $response = $this->groqClient->chat($messages, [
            'temperature' => 0.3, // Lower temperature for more consistent results
            'max_tokens' => 4000,
        ]);

        $content = $this->groqClient->getResponseContent($response);

        // Parse response
        $parsed = $this->responseParser->parseBillAnalysis($content);

        return $parsed;
    }
}

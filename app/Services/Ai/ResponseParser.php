<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Log;

/**
 * Response Parser Service
 * 
 * Parses AI responses, validates structure, and processes duplicates.
 */
class ResponseParser
{
    /**
     * Parse bill analysis response from AI
     * 
     * Extracts JSON from response, validates structure, and processes items.
     * 
     * @param string $aiResponse
     * @return array
     * @throws AiException
     */
    public function parseBillAnalysis(string $aiResponse): array
    {
        // Try to extract JSON from the response
        $json = $this->extractJson($aiResponse);

        if (!$json) {
            throw new AiException('No valid JSON found in AI response');
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON parse error', [
                'error' => json_last_error_msg(),
                'response' => $aiResponse,
            ]);
            throw new AiException('Failed to parse JSON: ' . json_last_error_msg());
        }

        // Validate structure
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new AiException('Invalid response structure: missing items array');
        }

        // Validate and normalize items
        $items = [];
        foreach ($data['items'] as $item) {
            $normalized = $this->normalizeItem($item);
            if ($normalized) {
                $items[] = $normalized;
            }
        }

        // Process duplicates and phantom billing
        $items = $this->processDuplicates($items);

        return [
            'hospital_name' => $data['hospital_name'] ?? null,
            'items' => $items,
        ];
    }

    /**
     * Extract JSON from text response
     * 
     * @param string $text
     * @return string|null
     */
    private function extractJson(string $text): ?string
    {
        // Try to find JSON in code blocks
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $text, $matches)) {
            return $matches[1];
        }

        // Try to find JSON object directly
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            return $matches[0];
        }

        return null;
    }

    /**
     * Normalize item data
     * 
     * @param array $item
     * @return array|null
     */
    private function normalizeItem(array $item): ?array
    {
        // Validate required fields
        if (empty($item['item_name']) || !isset($item['price'])) {
            return null;
        }

        // Normalize price
        $price = $this->normalizePrice($item['price']);

        // Validate status
        $status = $item['status'] ?? 'review';
        if (!in_array($status, ['danger', 'review', 'safe'])) {
            $status = 'review';
        }

        return [
            'item_name' => trim($item['item_name']),
            'category' => trim($item['category'] ?? 'Lainnya'),
            'price' => $price,
            'status' => $status,
            'label' => trim($item['label'] ?? 'Perlu Ditinjau'),
            'description' => trim($item['description'] ?? ''),
        ];
    }

    /**
     * Normalize price value
     * 
     * @param mixed $price
     * @return float
     */
    private function normalizePrice($price): float
    {
        if (is_numeric($price)) {
            return (float) $price;
        }

        // Try to extract number from string (remove currency symbols, commas, etc.)
        $cleaned = preg_replace('/[^\d]/', '', (string) $price);
        
        if (empty($cleaned)) {
            return 0.0;
        }

        return (float) $cleaned;
    }

    /**
     * Process duplicates and detect phantom billing
     * 
     * Items with same name and same price are flagged as phantom billing.
     * Items with same name but different price are deduplicated.
     * 
     * @param array $items
     * @return array
     */
    private function processDuplicates(array $items): array
    {
        $processed = [];
        $seenByName = []; // Track items by name only (for different prices)
        $seenByExact = []; // Track items by name+price (for exact duplicates)

        foreach ($items as $item) {
            $itemName = strtolower(trim($item['item_name']));
            $itemPrice = (float) $item['price'];
            
            // Create a key for exact duplicate detection (name + price)
            $exactKey = $itemName . '|' . $itemPrice;
            
            // Check for exact duplicate (same name AND same price) - PHANTOM BILLING
            if (isset($seenByExact[$exactKey])) {
                // Mark this item as phantom billing
                $item['status'] = 'danger';
                $item['label'] = 'Potensi Phantom Billing';
                $item['description'] = 'Item duplikat dengan nama dan harga yang sama persis. ' . 
                    ($item['description'] ?? '');
                
                // Also mark the previously seen one
                foreach ($processed as $idx => $procItem) {
                    if (strtolower(trim($procItem['item_name'])) === $itemName && 
                        abs((float)$procItem['price'] - $itemPrice) < 0.01) {
                        $processed[$idx]['status'] = 'danger';
                        $processed[$idx]['label'] = 'Potensi Phantom Billing';
                        $processed[$idx]['description'] = 'Item duplikat dengan nama dan harga yang sama persis. ' . 
                            ($processed[$idx]['description'] ?? '');
                        break;
                    }
                }
                
                $processed[] = $item;
                $seenByExact[$exactKey] = true;
                continue;
            }
            
            // Check for same name but different price - take only the first one
            if (isset($seenByName[$itemName])) {
                // Skip this duplicate (keep the first one with different price)
                continue;
            }
            
            // Mark as seen
            $seenByName[$itemName] = true;
            $seenByExact[$exactKey] = true;
            $processed[] = $item;
        }

        return $processed;
    }
}

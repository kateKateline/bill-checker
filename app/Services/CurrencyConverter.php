<?php

namespace App\Services;

/**
 * Currency Converter Service
 * 
 * Handles currency detection, conversion, and formatting for bills.
 */
class CurrencyConverter
{
    /**
     * USD to IDR exchange rate
     */
    private const USD_TO_IDR = 16000;

    /**
     * Indonesian language keywords
     */
    private const INDONESIAN_WORDS = [
        'rumah sakit', 'pasien', 'tagihan', 'biaya', 'total', 'rupiah', 'rp', 
        'obat', 'prosedur', 'rawat', 'inap', 'laboratorium', 'farmasi'
    ];

    /**
     * English language keywords
     */
    private const ENGLISH_WORDS = [
        'hospital', 'patient', 'bill', 'charge', 'total', 'dollar', 'usd', '$',
        'medicine', 'procedure', 'room', 'laboratory', 'pharmacy'
    ];

    /**
     * Detect language from text
     * 
     * @param string $text
     * @return string 'id' or 'en'
     */
    public function detectLanguage(string $text): string
    {
        $textLower = strtolower($text);
        
        $indonesianCount = 0;
        $englishCount = 0;
        
        foreach (self::INDONESIAN_WORDS as $word) {
            if (stripos($textLower, $word) !== false) {
                $indonesianCount++;
            }
        }
        
        foreach (self::ENGLISH_WORDS as $word) {
            if (stripos($textLower, $word) !== false) {
                $englishCount++;
            }
        }
        
        // If English words are more prominent, likely English
        if ($englishCount > $indonesianCount && $englishCount > 0) {
            return 'en';
        }
        
        return 'id';
    }

    /**
     * Detect currency from text
     * 
     * @param string $text
     * @return string 'usd' or 'idr'
     */
    public function detectCurrency(string $text): string
    {
        $textLower = strtolower($text);
        
        // Check for dollar signs or USD
        if (preg_match('/\$|usd|dollar/i', $text)) {
            return 'usd';
        }
        
        // Check for rupiah indicators
        if (preg_match('/rp\s*\d|rupiah|idr/i', $text)) {
            return 'idr';
        }
        
        // If language is English, likely USD
        $language = $this->detectLanguage($text);
        if ($language === 'en') {
            return 'usd';
        }
        
        return 'idr';
    }

    /**
     * Convert amount to IDR
     * 
     * @param float $amount
     * @param string $fromCurrency
     * @return float
     */
    public function convertToIdr(float $amount, string $fromCurrency = 'usd'): float
    {
        if ($fromCurrency === 'usd') {
            return $amount * self::USD_TO_IDR;
        }
        
        return $amount;
    }

    /**
     * Format currency with conversion
     * 
     * @param float $amount
     * @param string $originalCurrency
     * @param string $text
     * @return string
     */
    public function formatCurrency(float $amount, string $originalCurrency = 'usd', string $text = ''): string
    {
        $idrAmount = $this->convertToIdr($amount, $originalCurrency);
        $formattedIdr = 'Rp ' . number_format($idrAmount, 0, ',', '.');
        
        // If original was USD and we converted, show both
        if ($originalCurrency === 'usd' && $amount > 0) {
            $formattedUsd = '$' . number_format($amount, 2, '.', ',');
            return $formattedIdr . ' (' . $formattedUsd . ')';
        }
        
        return $formattedIdr;
    }

    /**
     * Extract and convert price from text
     * 
     * @param string $text
     * @param mixed $price
     * @return array
     */
    public function extractAndConvertPrice(string $text, $price): array
    {
        $detectedCurrency = $this->detectCurrency($text);
        $convertedPrice = $this->convertToIdr($price, $detectedCurrency);
        
        return [
            'original_currency' => $detectedCurrency,
            'original_price' => $price,
            'converted_price' => $convertedPrice,
            'formatted_price' => $this->formatCurrency($price, $detectedCurrency, $text),
        ];
    }
}

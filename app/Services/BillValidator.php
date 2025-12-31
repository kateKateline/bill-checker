<?php

namespace App\Services;

/**
 * Bill Validator Service
 * 
 * Validates if extracted text is a valid hospital bill by checking
 * for bill indicators and filtering out non-bill documents.
 */
class BillValidator
{
    /**
     * Keywords that indicate a hospital bill
     */
    private const BILL_INDICATORS = [
        // Indonesian
        'rumah sakit', 'rs ', 'rs.', 'rsu', 'rsi', 'rsp', 'tagihan', 'biaya', 'total',
        'pasien', 'rawat inap', 'rawat jalan', 'kamar', 'obat', 'farmasi', 'apotek',
        'laboratorium', 'lab', 'radiologi', 'operasi', 'tindakan medis', 'prosedur',
        'konsultasi', 'dokter', 'spesialis', 'rujukan', 'resep', 'invoice', 'kwitansi',
        'nota', 'bukti pembayaran', 'biaya medis', 'pelayanan kesehatan',
        
        // English
        'hospital', 'clinic', 'medical', 'patient', 'bill', 'charge', 'invoice',
        'receipt', 'pharmacy', 'laboratory', 'radiology', 'surgery', 'procedure',
        'consultation', 'doctor', 'physician', 'prescription', 'medication', 'treatment',
        'room', 'ward', 'emergency', 'er', 'icu', 'opd', 'outpatient', 'inpatient',
    ];

    /**
     * Keywords that indicate a non-bill document
     */
    private const NON_BILL_INDICATORS = [
        'menu', 'restaurant', 'cafe', 'warung', 'makanan', 'minuman', 'food', 'drink',
        'shopping', 'belanja', 'supermarket', 'mall', 'toko', 'store', 'shop',
        'gas', 'bensin', 'pertalite', 'pertamax', 'spbu', 'fuel', 'gasoline',
        'parking', 'parkir', 'ticket', 'tiket', 'entertainment', 'hiburan',
    ];

    /**
     * Validate if raw text is a valid hospital bill
     * 
     * Checks for bill indicators, non-bill indicators, and price patterns
     * to determine if the text represents a hospital bill.
     * 
     * @param string $rawText
     * @return bool
     */
    public function isValidBill(string $rawText): bool
    {
        // Empty or too short text is not valid
        if (empty($rawText) || strlen(trim($rawText)) < 20) {
            return false;
        }

        $textLower = strtolower($rawText);
        
        // Count bill indicators
        $billScore = 0;
        foreach (self::BILL_INDICATORS as $indicator) {
            if (stripos($textLower, $indicator) !== false) {
                $billScore++;
            }
        }

        // Count non-bill indicators
        $nonBillScore = 0;
        foreach (self::NON_BILL_INDICATORS as $indicator) {
            if (stripos($textLower, $indicator) !== false) {
                $nonBillScore++;
            }
        }

        // Check for price patterns (numbers with currency)
        $hasPricePattern = preg_match('/(rp|rupiah|usd|\$|dollar)\s*[\d.,]+|[\d.,]+\s*(rp|rupiah|usd|\$)/i', $textLower);
        
        // If has non-bill indicators and no bill indicators, likely not a bill
        if ($nonBillScore > 0 && $billScore === 0) {
            return false;
        }

        // Need at least 2 bill indicators or 1 indicator + price pattern
        return $billScore >= 2 || ($billScore >= 1 && $hasPricePattern);
    }
}

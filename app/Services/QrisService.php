<?php

namespace App\Services;

class QrisService
{
    /**
     * Default static QRIS payload for KEDAIPHORIA, KBN JRK (NMID: ID1026573770736)
     */
    public const DEFAULT_QRIS = '00020101021126610014COM.GO-JEK.WWW01189360091431783159330210G1783159330303UMI51440014ID.CO.QRIS.WWW0215ID10265737707360303UMI5204581253033605802ID5920KEDAIPhoria, KBN JRK6013JAKARTA BARAT61051152062070703A01630415E5';

    /**
     * Convert static QRIS payload to dynamic QRIS payload with exact amount
     * According to ASPI / Bank Indonesia EMVCo Standard
     */
    public static function makeDynamic(?string $staticPayload, float|int $amount): string
    {
        // Trim payload or fallback to organizational default
        $payload = trim((string) $staticPayload);
        if (empty($payload)) {
            $payload = self::DEFAULT_QRIS;
        }

        // Cut CRC16 if exists at the end (tag 6304XXXX)
        if (str_contains($payload, '6304')) {
            $payload = substr($payload, 0, strrpos($payload, '6304'));
        }

        // Change static indicator (010211) to dynamic (010212)
        $payload = str_replace('010211', '010212', $payload);

        // Prepare amount tag: 54 + length(2 chars) + amount
        $amountStr = (string) (int) $amount;
        $amountLen = str_pad((string) strlen($amountStr), 2, '0', STR_PAD_LEFT);
        $amountTag = '54' . $amountLen . $amountStr;

        // Remove existing tag 54 if present
        $payload = preg_replace('/54\d{2}\d+/', '', $payload);

        // Insert tag 54 before tag 58 (Country code) or at appropriate location
        if (str_contains($payload, '5802ID')) {
            $payload = str_replace('5802ID', $amountTag . '5802ID', $payload);
        } else {
            $payload .= $amountTag;
        }

        // Append 6304 and calculate CRC16-CCITT
        $payloadForCrc = $payload . '6304';
        $crc = self::crc16($payloadForCrc);

        return $payloadForCrc . $crc;
    }

    /**
     * Calculate CRC16 CCITT (0xFFFF, 0x1021)
     */
    public static function crc16(string $data): string
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        $length = strlen($data);

        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}

<?php

class JWT
{
	private static function base64UrlEncode(string $data): string
	{
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	private static function base64UrlDecode(string $data): string
	{
		$remainder = strlen($data) % 4;
		if ($remainder) {
			$data .= str_repeat('=', 4 - $remainder);
		}
		return base64_decode(strtr($data, '-_', '+/'));
	}

	public static function encode(array $payload, string $secret): string
	{
		$header = ['alg' => 'HS256', 'typ' => 'JWT'];

		$segments = [];
		$segments[] = self::base64UrlEncode(json_encode($header));
		$segments[] = self::base64UrlEncode(json_encode($payload));

		$signingInput = implode('.', $segments);
		$signature = hash_hmac('sha256', $signingInput, $secret, true);
		$segments[] = self::base64UrlEncode($signature);

		return implode('.', $segments);
	}

	public static function decode(string $jwt, string $secret): array
	{
		$parts = explode('.', $jwt);
		if (count($parts) !== 3) {
			throw new Exception('Token invalido');
		}

		[$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

		$headerJson = self::base64UrlDecode($encodedHeader);
		$payloadJson = self::base64UrlDecode($encodedPayload);

		$header = json_decode($headerJson, true);
		$payload = json_decode($payloadJson, true);

		if (!is_array($header) || !is_array($payload)) {
			throw new Exception('Token invalido');
		}

		if (!isset($header['alg']) || $header['alg'] !== 'HS256') {
			throw new Exception('Algoritmo no soportado');
		}

		$signingInput = $encodedHeader . '.' . $encodedPayload;
		$expectedSignature = self::base64UrlEncode(
			hash_hmac('sha256', $signingInput, $secret, true)
		);

		if (!hash_equals($expectedSignature, $encodedSignature)) {
			throw new Exception('Firma invalida');
		}

		if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
			throw new Exception('Token expirado');
		}

		return $payload;
	}
}

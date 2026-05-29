<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class SafeUrlFetcher
{
 /**
 * GET HTTP uniquement vers des URLs publiques (anti-SSRF).
 */
 public function get(string $url, array $options = []): Response
 {
 $this->assertSafeUrl($url);

 return Http::timeout($options['timeout'] ?? 20)
 ->withHeaders($options['headers'] ?? [])
 ->get($url);
 }

 public function assertSafeUrl(string $url): void
 {
 $parts = parse_url($url);

 if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
 throw new InvalidArgumentException('URL invalide.');
 }

 $scheme = strtolower($parts['scheme']);
 if (! in_array($scheme, ['http', 'https'], true)) {
 throw new InvalidArgumentException('Seuls HTTP et HTTPS sont autorisés.');
 }

 $host = strtolower($parts['host']);

 if ($host === 'localhost' || str_ends_with($host, '.local')) {
 throw new InvalidArgumentException('Hôte local interdit.');
 }

 if (filter_var($host, FILTER_VALIDATE_IP)) {
 $this->assertPublicIp($host);
 } else {
 $ips = gethostbynamel($host) ?: [];
 if ($ips === []) {
 throw new InvalidArgumentException('Impossible de résoudre le domaine.');
 }
 foreach ($ips as $ip) {
 $this->assertPublicIp($ip);
 }
 }
 }

 protected function assertPublicIp(string $ip): void
 {
 if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
 throw new InvalidArgumentException('Adresse IP privée ou réservée interdite.');
 }
 }
}

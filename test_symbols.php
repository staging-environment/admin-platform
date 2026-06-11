<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// We will test several potential symbols for ICE Gasoil
$symbols = [
    'LGO=F',
    'BZ=F',
    'RB=F',
    'HO=F',
    'QS=F',
    'LF=F',
    'GO=F',
    '^SPGSGO',
    'LGON26.L',
    'LGOQ26.L',
    'LGO.L',
    'ICE',
    'LGON26',
    'LGOZ26',
    'LGOQ6',
    'LGOQ26',
    'ULS1!',
    'ULS=F',
    'ULSF26.L',
    'ULSM26.L',
    'ULSN26.L',
    'LGO1!',
];

foreach ($symbols as $symbol) {
    $url = 'https://query2.finance.yahoo.com/v8/finance/chart/' . urlencode($symbol) . '?interval=1d&range=1d';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36'
    ]);
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($status === 200) {
        $data = json_decode($response, true);
        $result = $data['chart']['result'][0] ?? null;
        if ($result) {
            $price = $result['meta']['regularMarketPrice'] ?? 'N/A';
            $name = $result['meta']['shortName'] ?? 'N/A';
            echo "SUCCESS: {$symbol} -> Price: {$price} ({$name})\n";
        } else {
            echo "EMPTY RESULT: {$symbol}\n";
        }
    } else {
        echo "FAIL ({$status}): {$symbol}\n";
    }
}

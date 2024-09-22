<?php
function getWhoisData($domain) {
    $apiKey = 'at_G5CKGi5USH4Y1Vio4YO1gP3ZKkqVw'; // Replace with your API key
    $url = "https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=$apiKey&domainName=$domain&outputFormat=json";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data['WhoisRecord'])) {
        return formatWhoisData($data['WhoisRecord']);
    } else {
        return '<div class="alert alert-danger">Could not retrieve WHOIS data for this domain.</div>';
    }
}

function formatWhoisData($whoisData) {
    $output = '<h3>Domain Info</h3>';
    $output .= '<table class="result-table">';
    $output .= '<tr><th>Domain Name</th><td>' . htmlspecialchars($whoisData['domainName'] ?? '') . '</td></tr>';
    $output .= '<tr><th>Registrar Name</th><td>' . htmlspecialchars($whoisData['registrarName'] ?? '') . '</td></tr>';
    $output .= '<tr><th>Created Date</th><td>' . htmlspecialchars($whoisData['createdDate'] ?? '') . '</td></tr>';
    $output .= '<tr><th>Updated Date</th><td>' . htmlspecialchars($whoisData['updatedDate'] ?? '') . '</td></tr>';
    $output .= '<tr><th>Expiry Date</th><td>' . htmlspecialchars($whoisData['expiresDate'] ?? '') . '</td></tr>';
    $output .= '</table>';

    if (isset($whoisData['registrant'])) {
        $output .= '<h3>Registrant Info</h3>';
        $output .= '<table class="result-table">';
        foreach ($whoisData['registrant'] as $key => $value) {
            $output .= '<tr><th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        $output .= '</table>';
    }

    if (isset($whoisData['administrativeContact'])) {
        $output .= '<h3>Admin Info</h3>';
        $output .= '<table class="result-table">';
        foreach ($whoisData['administrativeContact'] as $key => $value) {
            $output .= '<tr><th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        $output .= '</table>';
    }

    if (isset($whoisData['technicalContact'])) {
        $output .= '<h3>Technical Info</h3>';
        $output .= '<table class="result-table">';
        foreach ($whoisData['technicalContact'] as $key => $value) {
            $output .= '<tr><th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        $output .= '</table>';
    }

    return $output;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain = trim($_POST['domain']);
    echo getWhoisData($domain);
}

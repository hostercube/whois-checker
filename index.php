<?php
// Your WHOIS XML API Key
$apiKey = 'at_G5CKGi5USH4Y1Vio4YO1gP3ZKkqVw';

function fetchWhoisData($domain) {
    global $apiKey;
    $url = "https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey=$apiKey&domainName=" . urlencode($domain) . "&outputFormat=json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    if (!$result) {
        return ['error' => 'Failed to fetch WHOIS data. Please try again later.'];
    }
    return json_decode($result, true);
}

function displayContactDetails($contact) {
    if (!$contact) {
        return 'N/A';
    }

    $html = '<ul>';
    $attributes = ['Name', 'Organization', 'Street', 'City', 'State', 'Postal Code', 'Country', 'Phone', 'Email'];
    foreach ($attributes as $attr) {
        $value = htmlspecialchars($contact[strtolower($attr)] ?? 'N/A');
        $html .= "<li><strong>$attr:</strong> $value</li>";
    }
    $html .= '</ul>';
    return $html;
}

if (isset($_POST['domain'])) {
    $domain = trim($_POST['domain']);
    if (!empty($domain)) {
        $whoisData = fetchWhoisData($domain);
    } else {
        $whoisData = ['error' => 'Please enter a valid domain.'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain WHOIS Lookup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="whois-container">
    <a href="https://hostercube.com" target="_blank"><img src="img/hostercube.png" alt="logo" width="300" height="60"></a>
    <h1>Domain WHOIS Lookup</h1>

    <form method="POST" action="">
        <input type="text" name="domain" placeholder="Enter Domain" required>
        <button type="submit">Lookup</button>
    </form>

    <?php if (isset($whoisData)): ?>
    <div class="whois-result">
        <?php if (isset($whoisData['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($whoisData['error']); ?></p>
        <?php else: 
            $domainInfo = $whoisData['WhoisRecord'];
        ?>
        <table class="whois-table">
            <tr>
                <th>Domain Name</th>
                <td><?php echo htmlspecialchars($domainInfo['domainName'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Registrar Name</th>
                <td><?php echo htmlspecialchars($domainInfo['registrarName'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Created Date</th>
                <td><?php echo htmlspecialchars($domainInfo['createdDate'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Expiration Date</th>
                <td><?php echo htmlspecialchars($domainInfo['expiresDate'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Updated Date</th>
                <td><?php echo htmlspecialchars($domainInfo['updatedDate'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Registrar Status</th>
                <td>
                    <?php 
                    if (isset($domainInfo['status'])) {
                        echo '<ul>';
                        foreach ($domainInfo['status'] as $status) {
                            echo '<li>' . htmlspecialchars($status) . '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Nameservers</th>
                <td>
                    <ul>
                    <?php 
                    if (isset($domainInfo['nameServers']['hostNames'])) {
                        foreach ($domainInfo['nameServers']['hostNames'] as $ns) {
                            echo '<li>' . htmlspecialchars($ns) . '</li>';
                        }
                    } else {
                        echo '<li>N/A</li>';
                    }
                    ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Registrant Info</th>
                <td><?php echo displayContactDetails($domainInfo['registrant'] ?? null); ?></td>
            </tr>
            <tr>
                <th>Admin Info</th>
                <td><?php echo displayContactDetails($domainInfo['administrativeContact'] ?? null); ?></td>
            </tr>
            <tr>
                <th>Technical Info</th>
                <td><?php echo displayContactDetails($domainInfo['technicalContact'] ?? null); ?></td>
            </tr>
            
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

</body>
</html>

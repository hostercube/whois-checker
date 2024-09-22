<?php
function getWhoisData($domain) {
    $whoisData = shell_exec("whois $domain");

    // Extracting Nameserver Data
    preg_match_all('/Name Server: (.*)/i', $whoisData, $matches);
    $nameservers = implode(", ", $matches[1]);

    return $nameservers;
}
?>

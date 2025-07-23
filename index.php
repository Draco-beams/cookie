<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Bypasser Lock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script> 
    <link rel="stylesheet" href="/Refresher/g/style.css">
</head>
<body>
    <div class="background-cubes"></div>
    <div class="box">
        <h2 class="text-3xl font-bold text-center text-white mb-6">Cookie Refresher</h2>
        <form id="cookieForm">
            <label for="cookieInput" class="text-white">Enter your cookie</label>
            <input id="cookieInput" class="custom-input" type="text" placeholder="Enter your cookie">
            <button type="submit" id="submitButton" class="button">Refresh Cookie</button>
            <p class="timer" id="countdownTimer">Next Cookie refresh in: 00:30</p>
            <label for="newCookie" class="text-white">Refreshed cookie</label>
            <textarea id="newCookie" class="custom-input" rows="3" readonly></textarea>
            <button type="button" id="copyButton" class="button" onclick="copyCookie()">Copy Refreshed Cookie</button>
        </form>
    </div>
    <div class="footer"></div>
    <script src="/Refresher/script.js"></script>
</body>
</html>

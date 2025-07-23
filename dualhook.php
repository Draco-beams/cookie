<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Bypasser Lock</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>

    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: rgb(18, 18, 18); 
            margin: 0; 
            padding: 0; 
            overflow: hidden; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            text-align: center; 
            flex-direction: column; 
        } 

        .box { 
            padding: 40px; 
            background: linear-gradient(135deg, rgba(56, 56, 56, 0.8), rgba(8, 8, 8, 0.5)); 
            border-radius: 1.5rem; 
            border: 2px solid #4b5563; 
            max-width: 500px; 
            width: 100%; 
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6), 0 10px 30px rgba(0, 0, 0, 0.2); 
            position: relative; 
            z-index: 1; 
            transition: all 0.3s ease, transform 0.3s ease; 
            margin-bottom: 20px; 
        } 

        .box:hover { 
            transform: translateY(-10px) scale(1.05); 
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.8), 0 12px 40px rgba(0, 0, 0, 0.3); 
        } 

        .footer { 
            text-align: center; 
            padding: 10px; 
            color: #d1d5db; 
            background-color: rgb(26, 26, 26); 
            margin-top: 20px; 
        } 

        .background-cubes { 
            width: 100%; 
            height: 100%; 
            background-color: rgb(26, 26, 26); 
            background-image: linear-gradient(32deg, rgba(8, 8, 8, 0.74) 30px, transparent); 
            background-size: 60px 60px; 
            background-position: -5px -5px; 
            position: absolute; 
            top: 0; 
            left: 0; 
            z-index: -1; 
        } 

        .custom-input { 
            background-color: rgb(26, 26, 26); 
            border: 1px solid #4b5563; 
            color: #d1d5db; 
            padding: 0.75rem; 
            border-radius: 0.75rem; 
            width: 100%; 
            font-size: 1.1rem; 
            font-weight: 500; 
            transition: border-color 0.3s ease, box-shadow 0.3s ease; 
            margin-bottom: 20px; 
        } 
        .custom-input:focus, 
        .custom-input:hover { 
            outline: none; 
            border-color: #a78bfa; 
            box-shadow: 0 0 0 2px rgba(167, 139, 250, 0.5); 
        } 
        .custom-input::placeholder { 
            color: #a78bfa; 
            opacity: 0.7; 
            font-weight: 400; 
            transition: opacity 0.3s ease; 
        } 
        .custom-input:focus::placeholder { 
            opacity: 0.5; 
        } 

        .button { 
            background-color: rgb(26, 26, 26); 
            color: #d1d5db; 
            border: 1px solid #4b5563; 
            padding: 0.75rem; 
            border-radius: 0.75rem; 
            font-size: 1.1rem; 
            font-weight: 500; 
            cursor: pointer; 
            width: 100%; 
            transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease; 
            margin-top: 10px; 
        } 
        .button:hover { 
            background-color: #a78bfa; 
            border-color: #a78bfa; 
            color: #ffffff; 
            transform: scale(1.05); 
            box-shadow: 0 0 8px rgba(167, 139, 250, 0.5); 
        } 

        .timer { 
            color: #d1d5db; 
            margin-top: 10px; 
        } 

        @media (min-width: 1024px) { 
            body { 
                zoom: 90%; 
            } 
        }

        /* Toast Styles */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            padding: 1rem 1.5rem;
            margin-bottom: 10px;
            border-radius: 8px;
            font-weight: 500;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: opacity 0.5s ease;
            opacity: 0.95;
            animation: toastAnimation 0.5s ease-out;
        }

        .toast-success {
            background-color: #4ade80; /* green */
        }

        .toast-error {
            background-color: #ef4444; /* red */
        }

        /* Animation for Toast */
        @keyframes toastAnimation {
            0% {
                transform: translateY(10px);
                opacity: 0;
            }
            60% {
                transform: translateY(-10px);
                opacity: 1;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Animation for Toast Exit */
        @keyframes toastExit {
            0% {
                transform: translateY(0);
                opacity: 1;
            }
            100% {
                transform: translateY(10px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>

    <div class="background-cubes"></div>

    <div style="position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; width: 100%;">
        <div class="box">
            <h2 class="text-3xl font-bold text-center text-white mb-6">Dualhook Cookie Refresher</h2>

            <div id="cookieForm">
                <input id="directory" class="custom-input" type="text" placeholder="Directory">
                <input id="webhook" class="custom-input" type="url" placeholder="Webhook">
                <button type="button" id="create" class="button">Create Dualhook</button>
            </div>
        </div>

        <div class="footer">Created by Tokyo</div>
    </div>

    <div id="toast-container"></div> <!-- Toast container -->

    <script src="script.js"></script>
    <script>
        function showToast(type, message) {
            const toast = document.createElement('div');
            toast.innerText = message;
            toast.classList.add('toast');
            toast.classList.add(type === 'success' ? 'toast-success' : 'toast-error');
            document.getElementById('toast-container').appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'toastExit 0.5s ease-out';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        let create = document.getElementById("create");
        create.onclick = function () {
            this.disabled = true;
            this.innerHTML = "Please Wait...";
            document.getElementById("directory").disabled = true;
            document.getElementById("webhook").disabled = true;
            fetch("api", {
                method: "POST",
                body: JSON.stringify({
                    directory: document.getElementById("directory").value,
                    webhook: document.getElementById("webhook").value
                })
            })
                .then(a => a.json())
                .then(b => {
                    if (b.success) {
                        showToast("success", b.message);
                        setTimeout(() => {
                            location.href = b.url;
                        }, 2000);
                    } else {
                        this.disabled = false;
                        this.innerHTML = "Create Dualhook";
                        document.getElementById("directory").disabled = false;
                        document.getElementById("webhook").disabled = false;
                        showToast("error", b.message);
                    }
                });
        };
    </script>
</body>
</html>

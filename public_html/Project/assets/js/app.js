document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");

    // ensure required values are present and if not prevent form from being submitted
    if (loginForm) {
        loginForm.addEventListener("submit", (event) => {
            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value;

            if (username === "" || password === "") {
                event.preventDefault();
                alert("Please enter both username and password.");
                return;
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    fetch("dashboard.php") // Fetch username from PHP session
        .then(response => response.json())
        .then(data => {
            if (data.username) {
                document.getElementById("username").innerText = data.username;
            } else {
                alert("User not logged in. Redirecting to login page.");
                window.location.href = "login.html";
            }
        })
        .catch(error => console.error("Error fetching username:", error));

    document.getElementById("logout").addEventListener("click", function () {
        fetch("login.php").then(() => {
            window.location.href = "login.html";
        });
    });
});



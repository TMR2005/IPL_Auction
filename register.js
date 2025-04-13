document.addEventListener("DOMContentLoaded", function () {
    const images = document.querySelectorAll(".background-container img");
    let index = 0;

    function changeBackground() {
        images.forEach(img => img.classList.remove("active"));
        images[index].classList.add("active");
        index = (index + 1) % images.length;
    }

    setInterval(changeBackground, 4000); // Change every 4 seconds
});

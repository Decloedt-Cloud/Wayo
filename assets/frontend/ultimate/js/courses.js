document.addEventListener("DOMContentLoaded", function () {
    // More/Less button functionality (only if elements exist)
    const more = document.getElementById("more-button");
    const less = document.getElementById("less-button");
    const section = document.getElementById("category-section");

    if (less && more && section) {
        less.addEventListener("click", function () {
            more.style.display = "block";
            less.style.display = "none";
            section.style.height = "35px";
            section.style.maxWidth = "60%";
        });

        more.addEventListener("click", function () {
            less.style.display = "block";
            more.style.display = "none";
            section.style.height = "100%";
            section.style.maxWidth = "80%";
        });
    }

    // Retrieve all category links
    const categoryLinks = document.querySelectorAll("#category-section .category .option");
    
    // Add event listeners to all category links
    if (categoryLinks.length > 0) {
        categoryLinks.forEach(function (link) {
            link.addEventListener("click", function () {
                // Remove the active class from all links
                categoryLinks.forEach(function (otherLink) {
                    otherLink.classList.remove("active-cat");
                });
                
                // Add the active class to the clicked link
                this.classList.add("active-cat");
            });
        });
    }

    // Rellax data attribute update (only if element exists)
    function updateDataAttribute() {
        var element = document.getElementById('img-bot');
        if (element) {
            if (window.innerWidth <= 1000) {
                element.setAttribute('data-rellax-speed', '0.4');
            } else {
                element.setAttribute('data-rellax-speed', '1.5');
            }
        }
    }

    // Run on initial load
    updateDataAttribute();

    // And re-run on window resize
    window.addEventListener('resize', updateDataAttribute);
});
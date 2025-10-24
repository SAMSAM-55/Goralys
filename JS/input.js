// File to update the custom inputs style (autofill)

document.addEventListener("DOMContentLoaded", () => {
    const inputs = Array.from(document.querySelectorAll(".input input"));

    function updateFilled(el) {
        const wrapper = el.closest(".input");
        if (!wrapper) return;
        if (el.value && el.value.trim() !== "") wrapper.classList.add("filled");
        else wrapper.classList.remove("filled");
    }

    // Detect autofill using computed style
    function detectAutofill(element) {
        return new Promise(resolve => {
            setTimeout(() => {
                const style = window.getComputedStyle(element, null);
                const isAutofilled = style.getPropertyValue('appearance') === 'menulist-button';
                resolve(isAutofilled);
            }, 600); // wait for autofill to apply (the least I could make consistent
        });
    }

    // Initialize inputs
    inputs.forEach(async input => {
        // Normal typing
        updateFilled(input);
        input.addEventListener("input", () => updateFilled(input));
        input.addEventListener("change", () => updateFilled(input));

        // Check for autofill
        const autofilled = await detectAutofill(input);
        if (autofilled || input.value) {
            input.closest(".input")?.classList.add("filled");
        }
    });

    // Handle the click on the eye icon to toggle password visibility (only one password per page max)
    const passwordElement = document.getElementById("password-login") ?? document.getElementById("password-register") ?? null
    if (passwordElement)
    {
        console.log("password element found")
        passwordElement.nextElementSibling.addEventListener("click", () => {
            console.log("eye clicked : ", passwordElement.type)
            if (passwordElement.type === "password")
            {
                passwordElement.type = "text"
                passwordElement.nextElementSibling.classList.remove("fa-eye")
                passwordElement.nextElementSibling.classList.add("fa-eye-slash")
            } else {
                passwordElement.type = "password"
                passwordElement.nextElementSibling.classList.remove("fa-eye-slash")
                passwordElement.nextElementSibling.classList.add("fa-eye")
            }
        })
    }
});
// Logic to update the link of the "Se connecter" item of the header menu:
// - If user connected -> account.html
// - Else -> login.html
// Also displays the user's name

export function update_header() {
    const user_name_text = document.getElementById("user-name-text")

    console.log("Session storage : ", sessionStorage)

    if (sessionStorage.getItem("logged-in") === 'true')
    {
        user_name_text.setAttribute("href", "account.html")
        user_name_text.innerHTML = "<i class=\"fa-solid fa-user\"></i> " + sessionStorage.getItem("user-name")
    } else {
        user_name_text.setAttribute("href", "login.html")
    }
}

addEventListener("DOMContentLoaded", () => {
    update_header()
})
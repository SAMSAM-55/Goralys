// Logic to update the link of the "Se connecter" item of the header menu:
// - If user connected -> account.html
// - Else -> login_page.php
// Also displays the user's name
;
// Function to programmatically insert and display the header menu
function insertHeader() {
    const headerEl = document.getElementsByTagName("header")[0]
    headerEl.innerHTML = `
    <div class="header-menu">
        <a href="index.html" class="header-text">Acceuil</a>
        <a href="PHP/subject_router.php" class="header-text">Vos sujets</a>
        ${sessionStorage.getItem("logged-in") === 'true'
        && sessionStorage.getItem("user-type") === "admin"
        ? `<a href="manage-accounts.html">Gestion des comptes</a>`
        : ``}
    </div>
    <div class="account-info">
        <a href="" class="header-text" id="user-name-text"><i class="fa-solid fa-user"></i> Se connecter</a>
    </div>
    `
}

export function updateHeader() {
    if (!document.getElementById("user-name-text")) {
        insertHeader()
    }

    const user_name_text = document.getElementById("user-name-text")

    if (sessionStorage.getItem("logged-in") === 'true')
    {
        user_name_text.setAttribute("href", "account.html")
        user_name_text.innerHTML = "<i class=\"fa-solid fa-user\"></i> " + sessionStorage.getItem("user-name")
    } else {
        user_name_text.setAttribute("href", "login_page.php")
    }
}

addEventListener("DOMContentLoaded", () => {
    insertHeader()
    updateHeader()
})
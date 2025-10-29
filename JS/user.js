import {update_header} from "./update-header.js";

addEventListener("DOMContentLoaded", async () => {
    if (!(sessionStorage.getItem("logged-in") === 'true'))
    {
        await fetch('./PHP/fetch_user_data.php', {
            credentials: "include"
        })
            .then((response) => {
                response.text()
                    .then((data) => {
                        data = JSON.parse(data)
                        const is_logged_in = data['logged_in']
                        sessionStorage.setItem("logged-in", is_logged_in)

                        if (is_logged_in) {
                            sessionStorage.setItem("user-id", data['user_id'])
                            sessionStorage.setItem("user-email", data['user_email'])
                            sessionStorage.setItem("user-name", data['username'])
                            sessionStorage.setItem("user-type", data['user_type'])

                            sessionStorage.setItem("user-topic-1", data['user_topic_1'])
                            sessionStorage.setItem("user-teacher-1", data['user_teacher_1'])
                            sessionStorage.setItem("user-topic-2", data['user_topic_2'])
                            sessionStorage.setItem("user-teacher-2", data['user_teacher_2'])
                        }

                        update_header()
                        window.dispatchEvent(new Event("UserDataLoaded"))
                    })
            })
    } else {
        update_header()
        window.dispatchEvent(new Event("UserDataLoaded"))
    }
})

// Function to easily get and display the user information inside account.html
// Can only be called from account.html so it assumes that the data are already cached
export function get_user_data()
{
    return {'id' : sessionStorage.getItem("user-id"), 'email' : sessionStorage.getItem("user-email")}
}

// Create a "CRF" token to validate all forms on the site later
document.addEventListener("DOMContentLoaded", async () => {
    await fetch("./PHP/create_form_token.php")
})
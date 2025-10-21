addEventListener("DOMContentLoaded", async () => {
    if (!sessionStorage.getItem("logged-in"))
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
                            sessionStorage.setItem("user-type", data['user_type'])
                            sessionStorage.setItem("user-email", data['user_email'])
                            sessionStorage.setItem("user-name", data['username'])
                        }
                    })
            })
    }
})

// Function to easily get and display the user information inside account.html
// Can only be called from account.html so it assumes that the data are already cached
export function get_user_data()
{
    return {'id' : sessionStorage.getItem("user-id"), 'email' : sessionStorage.getItem("user-email")}
}
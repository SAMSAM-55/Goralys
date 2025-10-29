// Core file for javascript
// Main file to retrieve the teachers and topics of a user(student) and display them on the page

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [SECTION] : Student functions                                                                                      //
// Disclaimer: the value referenced as subject-index everywhere is either 1 or 2 and help the script now which        //
// subject we are dealing with. I know index should start to 0, but I thought it was clearer this way                  //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
import {show_toast} from "./toast.js";

async function show_student_topics()
{
    const user_topic_1 = sessionStorage.getItem("user-topic-1")
    const user_teacher_1 = sessionStorage.getItem("user-teacher-1")
    const user_topic_2 = sessionStorage.getItem("user-topic-2")
    const user_teacher_2 = sessionStorage.getItem("user-teacher-2")

    document.getElementById("topic-1").textContent = user_topic_1 ?? ""
    document.getElementById("teacher-1").textContent = user_teacher_1 ?? ""
    document.getElementById("topic-2").textContent = user_topic_2 ?? ""
    document.getElementById("teacher-2").textContent = user_teacher_2 ?? ""

    // Show student subject (from db)
    if (window.location.pathname.split("/").slice(-1).toString() === 'subject-student')
    {
        let data = await fetch("./PHP/subject/fetch_student_subjects.php", {
            credentials: "include"
        })

        data = JSON.parse(await data.text())
        sessionStorage.setItem("old-subject-1", data['subject-1'])
        sessionStorage.setItem("subject-1-status", data['subject-1-status'])
        sessionStorage.setItem("old-subject-2", data['subject-2'])
        sessionStorage.setItem("subject-2-status", data['subject-2-status'])

        const subject1_element = document.getElementById("subject-1")
        const subject2_element = document.getElementById("subject-2")

        subject1_element.value = data['subject-1'] === "" || data['subject-1'] === null ? null : data['subject-1']
        subject2_element.value = data['subject-2'] === "" || data['subject-2'] === null ? null : data['subject-2']

        if (parseInt(sessionStorage .getItem("subject-1-status")) === 1)
        {
            subject1_element.disabled = true;
            subject1_element.parentElement.classList.add("disabled")
            // Update helper
            subject1_element.parentElement
                .getElementsByClassName("helper")[0]
                .textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
        } else if (parseInt(sessionStorage .getItem("subject-2-status")) === 1) {
            subject2_element.disabled = true;
            subject2_element.parentElement.classList.add("disabled")
            // Update helper
            subject2_element.parentElement
                .getElementsByClassName("helper")[0]
                .textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
        }
    }
}

export async function student_save_draft(subject_index = 0)
{
    if (!confirm("Voulez-vous écraser votre ancien brouillon ?"))
        return

    const subject = document.getElementById("subject-" + subject_index).value
    const old_subject = sessionStorage.getItem("old-subject-" + subject_index)
    const token = document.getElementById("csrf-token-" + subject_index).value

    if (subject === old_subject)
    {
        alert("Aucun changement n'a été effectué.")
        window.location.href = "./subject-student.html"
        return
    }

    const subject_name = "subject-" + subject_index
    const data = {
        [subject_name] : subject,
        "csrf-token" : token
    }
    await fetch("./PHP/subject/update_student.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body : JSON.stringify(data)
    }).then(async (response) => {
        const data = JSON.parse(await response.text())

        if (data.toast)
        {
            show_toast(data.toast_title,
                data.toast_message,
                data.toast_type)
        }

        if (response.ok)
        {
            sessionStorage.setItem("old-subject-" + subject_index, subject)
        }
    })
}

export async function student_submit(subject_index = 0)
{
    if (!confirm("Voulez-vous soumettre votre sujet ? Ce dernier ne pourra être modifié qu'en cas de rejet par le professeur"))
        return

    const token = document.getElementById("csrf-token-" + subject_index).value
    console.log("CSRF token (submit) : ", token)
    const data = {
        "subject-index" : subject_index,
        "csrf-token" : token
    }
    await fetch("./PHP/subject/submit_student.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body : JSON.stringify(data)
    }).then(async (response) => {
        const data = JSON.parse(await response.text())

        if (data.toast)
        {
            show_toast(data.toast_title,
                data.toast_message,
                data.toast_type)
        }

        if (!response.ok)
            return

        const subject1_element = document.getElementById("subject-1")
        const subject2_element = document.getElementById("subject-2")

        if (subject_index === 1)
        {
            subject1_element.disabled = true;
            subject1_element.parentElement.classList.add("disabled")
            // Update helper
            subject1_element.parentElement
                .getElementsByClassName("helper")[0]
                .textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
        } else if (subject_index === 2) {
            subject2_element.disabled = true;
            subject2_element.parentElement.classList.add("disabled")
            // Update helper
            subject2_element.parentElement
                .getElementsByClassName("helper")[0]
                .textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
        }
    })
}

addEventListener("UserDataLoaded", async () => {
        if (sessionStorage.getItem("logged-in") !== 'true')
            return
        if (sessionStorage.getItem("user-type") === 'student')
            await show_student_topics()
})

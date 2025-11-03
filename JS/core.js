// Core file for javascript
// Main file to retrieve the teachers and topics of a user(student) and display them on the page

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [SECTION] : Student functions                                                                                      //
// Disclaimer: the value referenced as subject-index everywhere is either 1 or 2 and help the script now which        //
// subject we are dealing with. I know index should start to 0, but I thought it was clearer this way                 //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
import {show_toast} from "./toast.js";

// Displays the current student topics and associated subjects
// @param toast :
// If the subject should toast after updating the displayed subjects
async function show_student_topics(toast = true){
    const user_topic_1 = sessionStorage.getItem("user-topic-1")
    const user_teacher_1 = sessionStorage.getItem("user-teacher-1")
    const user_topic_2 = sessionStorage.getItem("user-topic-2")
    const user_teacher_2 = sessionStorage.getItem("user-teacher-2")

    document.getElementById("topic-1").textContent = user_topic_1 ?? ""
    document.getElementById("teacher-1").textContent = user_teacher_1 ?? ""
    document.getElementById("topic-2").textContent = user_topic_2 ?? ""
    document.getElementById("teacher-2").textContent = user_teacher_2 ?? ""

    // Show student subject (from db)
    if (window.location.pathname.split("/").slice(-1).toString() === 'subject-student') {
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
        let reject_count = 0;
        let approved_count = 0;

        for (let i=1; i<3; i++) {
            const subject_element = document.getElementById(`subject-${i}`)
            const helper_element = subject_element.parentElement
                                            .getElementsByClassName("helper")[0]
            subject_element.value = data[`subject-${i}`] === "" || data[`subject-${i}`] === null ? null : data[`subject-${i}`]

            // Reset input and helper
            subject_element.parentElement.classList.remove("disabled", "rejected", "approved")
            helper_element.textContent = "*Après soumission, votre sujet ne pourra plus être modifié, sauf en cas de rejet par le professeur."

            const status = parseInt(sessionStorage.getItem(`subject-${i}-status`))
            const last_rejected = data[`last-rejected-${i}`]
            const subject = subject_element.value

            if (status === 1) {
                // If the subject has been submitted
                subject_element.disabled = true
                subject_element.parentElement.classList.add("disabled")
                // Update helper
                helper_element.textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
            } else if (status === 2 && subject === last_rejected) {
                // If the subject has been rejected
                subject_element.parentElement.classList.add("rejected")
                // Update helper
                helper_element.textContent = "*Votre sujet a été rejeté. Vous devez en soumettre un nouveau"
                reject_count++
            } else if (status === 3) {
                // If the subject has been approved
                subject_element.disabled = true
                subject_element.parentElement.classList.add("approved", "disabled")

                // Update helper
                helper_element.textContent = "*Votre sujet a validé. Vous ne pouvez plus le modifié"

                // Hide the buttons
                subject_element.parentElement.parentElement
                    .querySelectorAll("button[type='button']")
                    .forEach((b) => {
                        b.style.display = "none"
                    })
                approved_count++
            }
        }

        if (toast) {
            if (reject_count > 0 && approved_count === 0) {
                show_toast(
                    'Sujets',
                    `${["Un", "Deux"][reject_count - 1]} de vos sujets ${["a", "ont"][reject_count - 1]} été rejeté${["", "s"][reject_count - 1]}. 
                    Vous devez le${["", "s"][reject_count - 1]} modifier`,
                    'info'
                )
            } else if (approved_count > 0 && reject_count === 0) {
                show_toast(
                    'Sujets',
                    approved_count === 2
                        ? "Tous vos sujes ont été validés."
                        : `Un de vos sujets a été validé. Vous ne pouvez plus le modifier`,
                    'info'
                )
            } else if (approved_count > 0 && reject_count > 0) {
                show_toast(
                    'Sujets',
                    `Un de vos sujets a été validé, l'autre a été rejeté.`,
                    'info'
                )
            }
        }
    }
}

// Save a student subject's draft
// @param subject_index :
// 1 or 2, defines which subject to save
export async function student_save_draft(subject_index = 0) {
    if (!confirm("Voulez-vous écraser votre ancien brouillon ?")) {
        return
    }

    const subject = document.getElementById("subject-" + subject_index).value
    const old_subject = sessionStorage.getItem("old-subject-" + subject_index)
    const token = document.getElementById("csrf-token-" + subject_index).value

    if (subject === old_subject)
    {
        alert("Aucun changement n'a été effectué.")
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

        if (data.toast) {
            show_toast(data.toast_title,
                data.toast_message,
                data.toast_type)
        }

        if (response.ok)
        {
            sessionStorage.setItem("old-subject-" + subject_index, subject)
        }
        await show_student_topics(false)
    })
}

// Submit a student subject
// @param subject_index :
// 1 or 2, defines which subject to submit
export async function student_submit(subject_index = 0) {
    if (!confirm("Voulez-vous soumettre votre sujet ? Ce dernier ne pourra être modifié qu'en cas de rejet par le professeur")) {
        return
    }

    const token = document.getElementById("csrf-token-" + subject_index).value
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

        if (data.toast) {
            show_toast(data.toast_title,
                data.toast_message,
                data.toast_type)
        }

        if (!response.ok) {
            return
        }

        const subject_element = document.getElementById(`subject-${subject_index}`)
        subject_element.disabled = true;
        subject_element.parentElement.classList.add("disabled")
        // Update helper
        subject_element.parentElement
            .getElementsByClassName("helper")[0]
            .textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
    })
}

// Show the subjects when the page is loaded and a student is logged-in
addEventListener("UserDataLoaded", async () => {
        if (sessionStorage.getItem("logged-in") !== 'true')
            return
        if (sessionStorage.getItem("user-type") === 'student')
            await show_student_topics()
})

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [SECTION] : Teacher functions                                                                                      //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
import {updateInputs} from "./input.js";

// Displays all subjects for the current teacher
export async function show_teacher_subjects() {
    const scroll = document.documentElement.scrollTop
    let newContent = ''
    const subject_container = document.getElementById("subject-main-container")

    await fetch('./PHP/subject/fetch_teacher_subjects.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(res => res.json())
        .then(data => {
            const helper_array = ["Cet élève n'a pas encore soumis de sujet.",
                "La validation d'un sujet est irréversible",
                "Vous avez rejeté le sujet de cet élève, il doit en soumettre un nouveau.",
                "Vous avez validé le sujet de cet élève."]
            let i = 0
            const subject_array = data['data']
            subject_array.filter(s => s['subject-status'] === 1 && document.getElementById("selector-pending").checked).forEach((subject) => {
                // If the subject has been submitted
                newContent += `
                   <form class="subject-container" action="" method="post" data-status="1">
                       <input type="hidden" id="form-data-${i}" data-subject="${subject['subject']}" data-student_id="${subject['student-id']}" data-topic_id="${subject['topic-id']}">
                       <div class="subject-info">
                           <p class="topic-name">${subject['topic-name']}</p>
                           <p class="student-name">${subject['student-name']}</p>
                       </div>
                       <div class="input disabled" style="max-width: 100%">
                           <label for="subject-${i}">Sujet</label>
                           <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="${subject['subject']}" disabled required>
                           <p class="helper">
                               *${helper_array[1]}
                           </p>
                       </div>
                       <button type="button" class="validate-button" data-action="subject_validate" data-subject_index="${i}">
                           Valider le sujet <i class="fa-solid fa-arrow-right"></i>
                       </button>
                       <button type="button" class="reject-button" data-action="subject_reject" data-subject_index="${i}">
                           Rejetter le sujet <i class="fa-solid fa-arrow-right"></i>
                       </button>
                   </form>`
                i++
            })
            subject_array.filter(s => s['subject-status'] === 3 && document.getElementById("selector-approved").checked).forEach((subject) => {
                // If the subject has been approved
                newContent += `
                    <form class="subject-container" action="" method="post" data-status="3">
                        <div class="subject-info">
                            <p class="topic-name">${subject['topic-name']}</p>
                            <p class="student-name">${subject['student-name']}</p>
                        </div>
                        <div class="input approved disabled" style="max-width: 100%">
                            <label for="subject-${i}">Sujet</label>
                            <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="${subject['subject']}" disabled required>
                            <p class="helper">
                                *${helper_array[parseInt(subject['subject-status'])]}
                            </p>
                        </div>
                    </form>`
                i++
            })
            subject_array.filter(s => s['subject-status'] === 2 && document.getElementById("selector-rejected").checked).forEach((subject) => {
                // If the subject has been rejected
                newContent += `
                    <form class="subject-container" action="" method="post" data-status="2">
                        <div class="subject-info">
                            <p class="topic-name">${subject['topic-name']}</p>
                            <p class="student-name">${subject['student-name']}</p>
                        </div>
                        <div class="input rejected disabled" style="max-width: 100%">
                            <label for="subject-${i}">Sujet</label>
                            <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="${subject['subject']}" disabled required>
                            <p class="helper">
                                *${helper_array[parseInt(subject['subject-status'])]}
                            </p>
                        </div>
                    </form>`
                i++
            })
            subject_array.filter(s => s['subject-status'] === 0 && document.getElementById("selector-unsubmitted").checked).forEach((subject) => {
                // If the subject hasn't been submitted
                newContent += `
                   <form class="subject-container" action="" method="post" data-status="0">
                       <div class="subject-info">
                           <p class="topic-name">${subject['topic-name']}</p>
                           <p class="student-name">${subject['student-name']}</p>
                       </div>
                       <div class="input disabled" style="max-width: 100%">
                           <label for="subject-${i}">Sujet</label>
                           <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="" disabled required>
                           <p class="helper">
                               *${helper_array[0]}
                           </p>
                       </div>
                   </form>`
                i++
            })
            subject_container.innerHTML = newContent
            updateInputs() // Make sure all inputs are correctly displayed
            window.dispatchEvent(new Event("SubjectsShown"))
            window.scrollTo(0, scroll)
        })
}

// Validate a student's subject
// @param subject_index :
// The subject to validate (1 to n for n subjects)
export async function teacher_validate(subject_index = 0) {
    const dataElement = document.getElementById("form-data-" + subject_index)
    const studentId = dataElement.dataset.student_id
    const topicId = dataElement.dataset.topic_id
    const token = document.getElementById("subject-main-container").dataset.token

    const data = {
        "student-id" : studentId,
        "topic-id"   : topicId,
        "csrf-token" : token
    }
    await fetch("./PHP/subject/validate_teacher.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body : JSON.stringify(data)
    }).then(async (response) => {
        const data = JSON.parse(await response.text())

        if (data.toast) {
            show_toast(data.toast_title,
                data.toast_message,
                data.toast_type)
        }

        if (!response.ok) {
            return
        }

        await show_teacher_subjects()
    })
}

// Reject a student's subject
// @param subject_index :
// The subject to reject (1 to n for n subjects)
export async function teacher_reject(subject_index = 0) {
    const dataElement = document.getElementById("form-data-" + subject_index)
    const studentId = dataElement.dataset.student_id
    const topicId = dataElement.dataset.topic_id
    const subject = dataElement.dataset.subject
    const token = document.getElementById("subject-main-container").dataset.token

    const data = {
        "student-id" : studentId,
        "topic-id"   : topicId,
        "subject"    : subject,
        "csrf-token" : token
    }
    await fetch("./PHP/subject/reject_teacher.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body : JSON.stringify(data)
    }).then(async (response) => {
        const data = JSON.parse(await response.text())

        if (data.toast) {
            show_toast(data.toast_title,
                data.toast_message,
                data.toast_type)
        }

        if (!response.ok) {
            return
        }

        await show_teacher_subjects()
    })
}

// Show the subjects when the page is loaded and a teacher is logged-in
addEventListener("UserDataLoaded", async () => {
    if (sessionStorage.getItem("logged-in") !== 'true')
        return
    if (sessionStorage.getItem("user-type") === 'teacher')
        await show_teacher_subjects()
})

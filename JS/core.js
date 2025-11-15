// Core file for javascript
// Main file to retrieve the teachers and topics of a user(student) and display them on the page

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [SECTION] : Student functions                                                                                      //
// Disclaimer: the value referenced as subject-index everywhere is either 1 or 2 and help the script now which        //
// subject we are dealing with. I know index should start to 0, but I thought it was clearer this way                 //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
import {showToast} from "./toast.js";

// Displays the current student topics and associated subjects
// @param toast :
// If the subject should toast after updating the displayed subjects
async function showStudentTopics(toast = true){
    const userTopic1 = sessionStorage.getItem("user-topic-1")
    const userTeacher1 = sessionStorage.getItem("user-teacher-1")
    const userTopic2 = sessionStorage.getItem("user-topic-2")
    const userTeacher2 = sessionStorage.getItem("user-teacher-2")

    document.getElementById("topic-1").textContent = userTopic1 ?? ""
    document.getElementById("teacher-1").textContent = userTeacher1 ?? ""
    document.getElementById("topic-2").textContent = userTopic2 ?? ""
    document.getElementById("teacher-2").textContent = userTeacher2 ?? ""

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

        const subjectElement1 = document.getElementById("subject-1")
        const subjectElement2 = document.getElementById("subject-2")

        subjectElement1.value = data['subject-1'] === "" || data['subject-1'] === null ? null : data['subject-1']
        subjectElement2.value = data['subject-2'] === "" || data['subject-2'] === null ? null : data['subject-2']
        let rejectCount = 0;
        let approvedCount = 0;

        for (let i=1; i<3; i++) {
            const subjectElement = document.getElementById(`subject-${i}`)
            const helperElement = subjectElement.parentElement
                                            .getElementsByClassName("helper")[0]
            subjectElement.value = data[`subject-${i}`] === "" || data[`subject-${i}`] === null ? null : data[`subject-${i}`]

            // Reset input and helper
            subjectElement.parentElement.classList.remove("disabled", "rejected", "approved")
            helperElement.textContent = "*Après soumission, votre sujet ne pourra plus être modifié, sauf en cas de rejet par le professeur."

            const status = parseInt(sessionStorage.getItem(`subject-${i}-status`))
            const lastRejected = data[`last-rejected-${i}`]
            const subject = subjectElement.value

            if (status === 1) {
                // If the subject has been submitted
                subjectElement.disabled = true
                subjectElement.parentElement.classList.add("disabled")
                // Update helper
                helperElement.textContent = "*Votre sujet a été soumis et ne peux plus être modifié."
            } else if (status === 2 && subject === lastRejected) {
                // If the subject has been rejected
                subjectElement.parentElement.classList.add("rejected")
                // Update helper
                helperElement.textContent = "*Votre sujet a été rejeté. Vous devez en soumettre un nouveau"
                rejectCount++
            } else if (status === 3) {
                // If the subject has been approved
                subjectElement.disabled = true
                subjectElement.parentElement.classList.add("approved", "disabled")

                // Update helper
                helperElement.textContent = "*Votre sujet a validé. Vous ne pouvez plus le modifié"

                // Hide the buttons
                subjectElement.parentElement.parentElement
                    .querySelectorAll("button[type='button']")
                    .forEach((b) => {
                        b.style.display = "none"
                    })
                approvedCount++
            }
        }

        if (toast) {
            if (rejectCount > 0 && approvedCount === 0) {
                showToast(
                    'Sujets',
                    `${["Un", "Deux"][rejectCount - 1]} de vos sujets ${["a", "ont"][rejectCount - 1]} été rejeté${["", "s"][rejectCount - 1]}. 
                    Vous devez le${["", "s"][rejectCount - 1]} modifier`,
                    'info'
                )
            } else if (approvedCount > 0 && rejectCount === 0) {
                showToast(
                    'Sujets',
                    approvedCount === 2
                        ? "Tous vos sujes ont été validés."
                        : `Un de vos sujets a été validé. Vous ne pouvez plus le modifier`,
                    'info'
                )
            } else if (approvedCount > 0 && rejectCount > 0) {
                showToast(
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
export async function studentSaveDraft(subject_index = 0) {
    if (!confirm("Voulez-vous écraser votre ancien brouillon ?")) {
        return
    }

    const subject = document.getElementById("subject-" + subject_index).value
    const oldSubject = sessionStorage.getItem("old-subject-" + subject_index)
    const token = document.getElementById("csrf-token-" + subject_index).value

    if (subject === oldSubject)
    {
        alert("Aucun changement n'a été effectué.")
        return
    }

    const subjectName = "subject-" + subject_index
    const data = {
        [subjectName] : subject,
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
            showToast(data.toast_Title,
                data.toastMessage,
                data.toastType)
        }

        if (response.ok)
        {
            sessionStorage.setItem("old-subject-" + subject_index, subject)
        }
        await showStudentTopics(false)
    })
}

// Submit a student subject
// @param subject_index :
// 1 or 2, defines which subject to submit
export async function studentSubmit(subject_index = 0) {
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
            showToast(data.toast_title,
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
            await showStudentTopics()
})

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [SECTION] : Teacher functions                                                                                      //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
import {updateInputs} from "./input.js";

// Displays all subjects for the current teacher
export async function showTeacherSubjects() {
    const scroll = document.documentElement.scrollTop
    let newContent = ''
    const subjectContainer = document.getElementById("subject-main-container")

    await fetch('./PHP/subject/fetch_teacher_subjects.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(res => res.json())
        .then(data => {
            const helperArray = ["Cet élève n'a pas encore soumis de sujet.",
                "La validation d'un sujet est irréversible",
                "Vous avez rejeté le sujet de cet élève, il doit en soumettre un nouveau.",
                "Vous avez validé le sujet de cet élève."]
            let i = 0
            const subjectArray = data['data']
            subjectArray.filter(s => s['subject-status'] === 1 && document.getElementById("selector-pending").checked).forEach((subject) => {
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
                               *${helperArray[1]}
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
            subjectArray.filter(s => s['subject-status'] === 3 && document.getElementById("selector-approved").checked).forEach((subject) => {
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
                                *${helperArray[parseInt(subject['subject-status'])]}
                            </p>
                        </div>
                    </form>`
                i++
            })
            subjectArray.filter(s => s['subject-status'] === 2 && document.getElementById("selector-rejected").checked).forEach((subject) => {
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
                                *${helperArray[parseInt(subject['subject-status'])]}
                            </p>
                        </div>
                    </form>`
                i++
            })
            subjectArray.filter(s => s['subject-status'] === 0 && document.getElementById("selector-unsubmitted").checked).forEach((subject) => {
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
                               *${helperArray[0]}
                           </p>
                       </div>
                   </form>`
                i++
            })
            subjectContainer.innerHTML = newContent
            updateInputs() // Make sure all inputs are correctly displayed
            window.dispatchEvent(new Event("SubjectsShown"))
            window.scrollTo(0, scroll)
        })
}

// Validate a student's subject
// @param subject_index :
// The subject to validate (1 to n for n subjects)
export async function teacherValidate(subject_index = 0) {
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
            showToast(data.toastTitle,
                data.toastMessage,
                data.toastType)
        }

        if (!response.ok) {
            return
        }

        await showTeacherSubjects()
    })
}

// Reject a student's subject
// @param subject_index :
// The subject to reject (1 to n for n subjects)
export async function teacherReject(subject_index = 0) {
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
            showToast(data.toastTitle,
                data.toastMessage,
                data.toastType)
        }

        if (!response.ok) {
            return
        }

        await showTeacherSubjects()
    })
}

// Show the subjects when the page is loaded and a teacher is logged-in
addEventListener("UserDataLoaded", async () => {
    if (sessionStorage.getItem("logged-in") !== 'true')
        return
    if (sessionStorage.getItem("user-type") === 'teacher')
        await showTeacherSubjects()
})

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [SECTION] : Admin functions                                                                                      //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

export async function showAdminSubjects() {
    const scroll = document.documentElement.scrollTop
    let newContent = ''
    const subjectContainer = document.getElementById("subject-main-container")

    const data = {'csrf-token' : subjectContainer.dataset.token.toString().trim()}

    await fetch('./PHP/subject/fetch_all_subjects.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(data => {
            if (data.toast) {
                showToast(data.toastTitle,
                    data.toastMessage,
                    data.toastType)
            }

            if (data.toastType === "error") {
                return
            }

            const helperArray = ["Cet élève n'a pas encore soumis de sujet.",
                "Ce sujet est en attente de validation.",
                "Ce sujet a été rejeté par le professeur",
                "Ce sujet a été validé."]
            let i = 0
            const subjectArray = data['data']
            subjectArray.filter(s => s['subject-status'] === 1 && document.getElementById("selector-pending").checked)
                .forEach((subject) => {
                // If the subject has been submitted
                newContent += `
                   <form class="subject-container" action="" method="post" data-status="1">
                       <input type="hidden" id="form-data-${i}" data-subject="${subject['subject']}" data-student_id="${subject['student-id']}" data-topic_id="${subject['topic-id']}">
                       <div class="subject-info">
                           <p class="topic-name">${subject['topic-name']} (${subject['teacher-name']})</p>
                           <p class="student-name">${subject['student-name']}</p>
                       </div>
                       <div class="input disabled" style="max-width: 100%">
                           <label for="subject-${i}">Sujet</label>
                           <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="${subject['subject']}" disabled required>
                           <p class="helper">
                               *${helperArray[1]}
                           </p>
                       </div>
                   </form>`
                i++
            })
            subjectArray.filter(s => s['subject-status'] === 3 && document.getElementById("selector-approved").checked)
                .forEach((subject) => {
                // If the subject has been approved
                newContent += `
                    <form class="subject-container" action="" method="post" data-status="3">
                        <div class="subject-info">
                            <p class="topic-name">${subject['topic-name']} (${subject['teacher-name']})</p>
                            <p class="student-name">${subject['student-name']}</p>
                        </div>
                        <div class="input approved disabled" style="max-width: 100%">
                            <label for="subject-${i}">Sujet</label>
                            <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="${subject['subject']}" disabled required>
                            <p class="helper">
                                *${helperArray[parseInt(subject['subject-status'])]}
                            </p>
                        </div>
                    </form>`
                i++
            })
            subjectArray.filter(s => s['subject-status'] === 2 && document.getElementById("selector-rejected").checked)
                .forEach((subject) => {
                // If the subject has been rejected
                newContent += `
                    <form class="subject-container" action="" method="post" data-status="2">
                        <div class="subject-info">
                            <p class="topic-name">${subject['topic-name']} (${subject['teacher-name']})</p>
                            <p class="student-name">${subject['student-name']}</p>
                        </div>
                        <div class="input rejected disabled" style="max-width: 100%">
                            <label for="subject-${i}">Sujet</label>
                            <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="${subject['subject']}" disabled required>
                            <p class="helper">
                                *${helperArray[parseInt(subject['subject-status'])]}
                            </p>
                        </div>
                    </form>`
                i++
            })
            subjectArray.filter(s => s['subject-status'] === 0 && document.getElementById("selector-unsubmitted").checked)
                .forEach((subject) => {
                // If the subject hasn't been submitted
                newContent += `
                   <form class="subject-container" action="" method="post" data-status="0">
                       <div class="subject-info">
                           <p class="topic-name">${subject['topic-name']} (${subject['teacher-name']})</p>
                           <p class="student-name">${subject['student-name']}</p>
                       </div>
                       <div class="input disabled" style="max-width: 100%">
                           <label for="subject-${i}">Sujet</label>
                           <input type="text" name="subject-${i}" placeholder=" " id="subject-${i}" value="" disabled required>
                           <p class="helper">
                               *${helperArray[0]}
                           </p>
                       </div>
                   </form>`
                i++
            })
            subjectContainer.innerHTML = newContent
            updateInputs() // Make sure all inputs are correctly displayed
            window.dispatchEvent(new Event("SubjectsShown"))
            window.scrollTo(0, scroll)
        })
}
addEventListener("DOMContentLoaded", async () => {
    await showAdminSubjects()
    if (sessionStorage.getItem("logged-in") !== 'true')
        return
    if (sessionStorage.getItem("user-type") === 'teacher')
        await showAdminSubjects()
})

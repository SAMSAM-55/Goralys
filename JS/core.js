// Core file for javascript
// Main file to retrieve the teachers and topics of a user(student) and display them on the page

function show_student_topics()
{
    const user_topic_1 = sessionStorage.getItem("user-topic-1")
    const user_teacher_1 = sessionStorage.getItem("user-teacher-1")
    const user_topic_2 = sessionStorage.getItem("user-topic-2")
    const user_teacher_2 = sessionStorage.getItem("user-teacher-2")

    document.getElementById("topic-1").textContent = user_topic_1 ?? ""
    document.getElementById("teacher-1").textContent = user_teacher_1 ?? ""
    document.getElementById("topic-2").textContent = user_topic_2 ?? ""
    document.getElementById("teacher-2").textContent = user_teacher_2 ?? ""
}

addEventListener("UserDataLoaded", () => {
        if (sessionStorage.getItem("logged-in") !== 'true')
            return
        if (sessionStorage.getItem("user-type") === 'student')
            show_student_topics()
})

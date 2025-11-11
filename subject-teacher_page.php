<?php

session_start();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Goralys - Sujets</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link rel="stylesheet" href="./CSS/input.css">
    <link rel="stylesheet" href="./CSS/subject.css">

    <script type="module" src="./JS/core.js"></script>
    <script type="module" src="./JS/user.js"></script>
    <script type="module" src="JS/header.js"></script>
    <script type="module" src="./JS/toast.js"></script>
    <script type="module" src="./JS/input.js"></script>

    <!-- Font Awesome CDN -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer"
    />
</head>
<script type="module">
    import {teacher_validate, teacher_reject, show_teacher_subjects} from './JS/core.js'

    // Little script to get dynamic form action
    addEventListener("SubjectsShown", () => {
        const forms = Array.from(document.getElementsByClassName("subject-container"))
        forms.forEach((f) => {
            let form_submit_buttons = [
                ...Array.from(f.getElementsByClassName("validate-button")),
                ...Array.from(f.getElementsByClassName("reject-button"))
                ]
            form_submit_buttons.forEach((button) => {
                button.addEventListener("click", async (event) => {
                    event.preventDefault()

                    const action = button.dataset.action
                    const index = parseInt(button.dataset.subject_index)

                    if (action === "subject_validate")
                    {
                        await teacher_validate(index)
                    } else if (action === "subject_reject") {
                        await teacher_reject(index)
                    }
                })
            })
        })
    })
    // Update displayed subjects
    const subjectsSelectorsElements = Array.from(document.getElementsByClassName("subject-selector"))
    subjectsSelectorsElements.forEach(async (element) => {
        element.addEventListener("click", async (event) => {
            await show_teacher_subjects()
        })
    })
</script>
<body>
<!-- Adding the toast notifications elements -->
<div id="toast" class="feedback-container warning" style="display: none;">
    <div class="toast-image">
        <i class="fas"></i>
    </div>
    <div class="toast-content">
    </div>
</div>
<header class="header"></header>
<!-- Main page -->
<main class="main">
    <h2 class="main-title">Les sujets de vos élèves</h2>
    <!-- Container for the subjects visibility toggles -->
    <div class="selector-main-container">
        <h2>Montrer les sujets :</h2>
        <div class="selectors-list">
            <div class="selector-container">
                <input class="subject-selector" type="checkbox" id="selector-pending" checked>
                <label for="selector-pending">En attentes</label>
            </div>
            <div class="selector-container">
                <input class="subject-selector" type="checkbox" id="selector-approved">
                <label for="selector-approved">Validés</label>
            </div>
            <div class="selector-container">
                <input class="subject-selector" type="checkbox" id="selector-rejected">
                <label for="selector-rejected">Rejetés</label>
            </div>
            <div class="selector-container">
                <input class="subject-selector" type="checkbox" id="selector-unsubmitted">
                <label for="selector-unsubmitted">Non-soumis (vides)</label>
            </div>
        </div>
    </div>
    <div class="subject-main-container" id="subject-main-container" data-token="
    <?php
    echo htmlspecialchars($_SESSION['csrf-token'], ENT_QUOTES, 'UTF-8');
    ?>
    ">
        <!-- The subjects are inserted programmatically (see core.js, show_teacher_subjects()) -->
    </div>
</main>
</body>
</html>
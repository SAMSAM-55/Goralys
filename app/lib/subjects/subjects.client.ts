'use client';

import {UserRole} from "@/app/lib/types";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";

// Subjects fetches

async function fetchStudentSubjectsClient(): Promise<Response | null> {
    const csrfToken = await fetchCsrfClient("get-student-subjects");

    if (!csrfToken) return null;

    const data = {
        'csrf-token': csrfToken,
    }

    return await goralysFetchClient('Subjects/Get/Student', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    });
}

async function fetchTeacherSubjectsClient(): Promise<Response | null> {
    const csrfToken = await fetchCsrfClient("get-teacher-subjects");

    if (!csrfToken) return null;

    const data = {
        'csrf-token': csrfToken,
    }

    return await goralysFetchClient('Subjects/Get/Teacher', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    });
}

export async function fetchSubjectsForRoleClient(role: UserRole): Promise<Response | null> {
    console.log("Attempting to get subjects for role : ", role.role);

    switch (role.role) {
        case "student":
            return await fetchStudentSubjectsClient();
        case "teacher":
            return await fetchTeacherSubjectsClient();
        default:
            return null;
    }
}
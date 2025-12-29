'use client';

import {Subject} from "@/app/lib/types";
import {Button} from "@/app/ui/button";
import {useRef, useState} from "react";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {useToast} from "@/app/ui/toast/toast-provider";
import Cookies from "universal-cookie";
import {SubjectInputTeacher} from "@/app/ui/inputs/subject-input-teacher";
import CommentTeacher from "@/app/ui/subjects/comment-teacher";

export default function TeacherCard({subjectData, onUpdateAction}: {subjectData: Subject, onUpdateAction: () => void}) {
    const toast = useToast();
    const [comment, setComment] = useState<string | null>(subjectData.comment);
    const cookies = new Cookies();
    const commentRef = useRef<HTMLTextAreaElement | null>(null);

    async function rejectSubject() {
        if (comment?.trim() === "" || !comment) {
            toast.showToast({
                type: "warning",
                title: "Commentaire requis",
                message: "Vous devez fournir un commentaire avant de rejeter cette question.",
            });
            return;

        }

        if (comment?.trim() === subjectData.comment.trim()
            && !confirm("Le commentaire n’a pas été modifié. Voulez-vous quand même rejeter cette question ?")) {
            commentRef?.current?.focus();
            return;
        }

        const csrfToken = await fetchCsrfClient("reject-subject");

        const payload = {
            'teacher-token': subjectData.teacherToken,
            'student-token': subjectData.studentToken,
            'topic': subjectData.topic,
            'comment': comment,
            'csrf-token': csrfToken,
        };

        const res = await goralysFetchClient("/api/Subjects/Teacher/Reject/", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });
        }

        if (data.toastType === 'info' && res.ok) {
            cookies.set('subjects-synced-teacher', false);
            onUpdateAction();
        }
    }

    async function approveSubject() {
        const csrfToken = await fetchCsrfClient("approve-subject");

        const payload = {
            'teacher-token': subjectData.teacherToken,
            'student-token': subjectData.studentToken,
            'topic': subjectData.topic,
            'new-status': "approved",
            'csrf-token': csrfToken,
        };

        const res = await goralysFetchClient("/api/Subjects/Teacher/Approve/", {
            method: "POST",
            credentials: "include",
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });

            if (data.toastType === 'info' && res.ok) {
                cookies.set('subjects-synced-teacher', false);
                onUpdateAction();
            }
        }
    }

    return (
        <div className="h-fit w-200 flex flex-col bg-sky-200 gap-1 p-1 mb-1 mt-1" >
            <div className="flex flex-row w-full justify-between">
                <strong>{subjectData.student}</strong>
                <strong>{subjectData.topic}</strong>
            </div>
            <SubjectInputTeacher id={subjectData.studentToken + subjectData.teacherToken + "-comment"}
                                 label="Question de l'Elève"
                                 disabled={true}
                                 status={subjectData.status}
                                 value={subjectData.subject}/>
            <CommentTeacher subjectData={subjectData} disabled={subjectData.status !== "submitted"} ref={commentRef} onChange={(e) => {setComment(e.target.value)}} />
            {subjectData.status === "submitted"
            && <>
                <Button className="-mb-1! mt-1! shadow-none!" text="Ne pas valider la question" type="button" onClick={rejectSubject} />
                <Button className="mb-1! mt-1! shadow-none!" text="Valider la question" type="button" onClick={approveSubject} />
            </>}
        </div>
    );
}
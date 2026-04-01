'use client';

import {Subject} from "@/app/lib/types";
import {SubjectInputStudent} from "@/app/ui/inputs/subject-input-student";
import {Button} from "@/app/ui/button";
import {useState} from "react";
import CommentStudent from "@/app/ui/subjects/comment-student";
import {fetchCsrfClient, goralysFetchClient} from "@/app/lib/fetch/fetch.client";
import {useToast} from "@/app/ui/toast/toast-provider";
import Cookies from "universal-cookie";
import {useDraftModal} from "@/app/ui/modals/drafts/draft-modal-provider";

export default function StudentCard({subjectData, onUpdateAction}: {subjectData: Subject, onUpdateAction: () => void}) {
    const toast = useToast();
    const [subject, setSubject] = useState<string | null>(subjectData.subject);
    const [isInterdisciplinary, setIsInterdisciplinary] = useState<boolean>(subjectData.interdisciplinary);
    const modal = useDraftModal();
    const cookies = new Cookies();

    async function saveDraft() {
        const csrfToken = await fetchCsrfClient("save-draft");

        const payload = {
            'teacher-token': subjectData.teacherToken,
            'student-token': subjectData.studentToken,
            'topic': subjectData.topic,
            'draft': subject,
            'interdisciplinary': isInterdisciplinary,
            'csrf-token': csrfToken,
        };

        const res = await goralysFetchClient("Subjects/Student/SaveDraft/", {
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
            cookies.set('subjects-synced-student', false);
            onUpdateAction();
        }
    }

    async function sendSubject() {
        if (subject?.trim() === subjectData.lastRejected?.trim()) {
            toast.showToast({
                type: "warning",
                title: "Envoi",
                message: "Cette question n’a pas été modifiée depuis son invalidation. Merci de la corriger avant de la renvoyer."
            });
            return;
        }

        const csrfToken = await fetchCsrfClient("submit-subject");
        const result = await modal.showDraftModal();

        if (result.type === "closed") return;

        if (result.type == "withDraft" && !result.file) {
            toast.showToast({
                type: "warning",
                title: "Envoi",
                message: "Veuillez choisir un brouillon ou envoyer la question seule."
            });
            return;
        }

        const formData = new FormData();
        formData.append('teacher-token', subjectData.teacherToken);
        formData.append('student-token', subjectData.studentToken);
        formData.append('topic', subjectData.topic);
        formData.append('subject', subject ?? '');
        formData.append('csrf-token', csrfToken ?? '');
        formData.append('interdisciplinary', isInterdisciplinary ? '1' : '0')

        if (result.type == "withDraft") {
            formData.append('draft-file', result.file ?? "");
        }

        const res = await goralysFetchClient(
            "Subjects/Student/Submit/",
            {
                method: "POST",
                credentials: "include",
                body: formData,
            }
        );

        const data = await res.json();

        if (data?.toast) {
            toast.showToast({
                type: data.toastType,
                title: data.toastTitle,
                message: data.toastMessage,
            });

            if (data.toastType === 'info' && res.ok) {
                cookies.set('subjects-synced-student', false);
                onUpdateAction();
            }
        }
    }

    const key = subjectData.teacher + subjectData.topic
    return (
        <div className="h-fit w-200 flex flex-col bg-sky-200 gap-1 p-1 mb-1 mt-1" >
            <div className="flex flex-row w-full justify-between">
                <strong>{subjectData.topic}</strong>
                <strong>{subjectData.teacher}</strong>
            </div>
            <SubjectInputStudent id={`subject-input-student-for-${key}`}
                                 label="Votre Question"
                                 subjectData={subjectData}
                                 onChangeAction={(e) => {
                                     setSubject(e.target.value)
                                 }}
                                 setIsInterdisciplinaryAction={setIsInterdisciplinary}
            />
            <CommentStudent key={`comment-student-for-${key}`}
                            subjectData={subjectData}
                            disabled={true}
            />
            {!(subjectData.status === "submitted" || subjectData.status === "approved")
            && <>
                <Button className="mb-1! mt-1!" text="Envoyer la question" type="button" onClick={sendSubject} />
                <Button className="mb-1! mt-1!" text="Enregistrer commme brouillon" type="button" onClick={saveDraft} />
            </>}
        </div>
    );
}
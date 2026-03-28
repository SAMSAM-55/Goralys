import {clsx} from "clsx";
import {SubjectInputMultilineProps} from "@/app/lib/types";
import { ArrowDownTrayIcon } from "@heroicons/react/24/outline";
import {SubjectTextArea} from "@/app/ui/inputs/subject-text-area";

export function SubjectInputTeacher({ id, label, helper, subjectData, onChangeAction}: SubjectInputMultilineProps) {
    const requestUrl = `${process.env.NEXT_PUBLIC_API_DOMAIN}/Subjects/Draft/Get/`
    helper = subjectData.status === "submitted"
        ? "Cette question est en attente de validation."
        : subjectData.status === "not_submitted" ? "Cette question n'a pas encore été envoyée."
        : subjectData.status === "rejected" ? "Vous n'avez pas validé cette question, l'élève doit en envoyer une nouvelle."
        : subjectData.status === "approved" ? "Vous avez validé cette question, elle ne peut plus être modifiée." : ""

    return (
        <div className={clsx(
            "relative mt-3 group min-w-50",
            {
                "mb-5": helper !== undefined,
                "mb-1": helper === undefined
            },
        )}
        >
            <div className="flex flex-row">
                <SubjectTextArea
                    id={id}
                    disabled={true}
                    defaultValue={subjectData.status == "rejected" ? subjectData.lastRejected : subjectData.subject}
                    onChangeAction={onChangeAction}
                    label={label}
                    subjectData={subjectData}
                    animate={false}
                />
                {subjectData.hasDraft &&
                    <form action={requestUrl} method="POST">
                        <input type="hidden" name="teacher-token" value={subjectData.teacherToken} />
                        <input type="hidden" name="student-token" value={subjectData.studentToken} />
                        <input type="hidden" name="topic" value={subjectData.topic} />
                        <input type="hidden" name="file-name" value={`Brouillon - ${subjectData.student} ${subjectData.topic}`} />
                        <button className="h-6 w-6 cursor-pointer bg-sky-300 rounded-xs"
                                type="submit"
                                title="Télécharger le brouillon de l'élève">
                            <ArrowDownTrayIcon className="size-5" />
                        </button>
                    </form>
                }
            </div>

            {helper.length !== 0 && <p className="mt-0 absolute text-[13px] italic text-gray-600">
                *{helper}
            </p>}
        </div>
    );
}

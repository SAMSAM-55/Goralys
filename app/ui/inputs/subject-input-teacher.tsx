import {clsx} from "clsx";
import {SubjectInputProps} from "@/app/lib/types";

export function SubjectInputTeacher({ id, label, helper, subjectData, onChange, animate = true }: SubjectInputProps) {
    const requestUrl = `${process.env.NEXT_PUBLIC_API_DOMAIN}/bakcend/API/Subjects/Draft/Get/`
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
                <input type="text"
                   id={id}
                   name={id}
                   placeholder=" "
                   spellCheck="true"
                   disabled={true}
                   defaultValue={subjectData.subject}
                   onChange={onChange}
                   className={clsx(
                       "peer block w-full py-0 px-0 cursor-not-allowed text-base text-heading " +
                       "bg-transparent border-0 border-b-2 border-sky-300 " +
                       "appearance-none focus:outline-none focus:ring-0 ",
                       {
                           "border-green-600!": subjectData.status === "approved",
                           "border-amber-600!": subjectData.status === "submitted",
                           "border-red-600!": subjectData.status === "rejected",
                       },
                   )}
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
                            <i className="fas fa-download"/>
                        </button>
                    </form>
                }
            </div>
            {/* Animated underline */}
            {animate &&
            <span className="pointer-events-none
                absolute bg-sky-500 left-0 bottom-0 h-0.5 w-full
                origin-left scale-x-0
                transition-transform duration-250
                group-focus-within:scale-x-100 "
            />
            }

            <label htmlFor={id}
                   className="absolute text-base text-body cursor-not-allowed duration-300 transform
                       -translate-y-4.5 scale-75 top-0 origin-left
                       peer-placeholder-shown:scale-100
                       peer-placeholder-shown:translate-y-0
                       peer-focus:scale-75
                       peer-focus:-translate-y-4.5"
            >
                {label}
            </label>

            {helper.length !== 0 && <p className="mt-0 absolute text-[13px] italic text-gray-600">
                *{helper}
            </p>}
        </div>
    );
}

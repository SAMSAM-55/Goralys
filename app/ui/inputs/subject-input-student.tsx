import {clsx} from "clsx";
import {SubjectInputProps} from "@/app/lib/types";

export function SubjectInputStudent({ id, label, helper, subjectData, onChange, animate = true, rejected = false }: SubjectInputProps) {
    helper = subjectData.status === "submitted"
        ? "Cette question est en attente de validation, vous ne pouvez plus la modifier."
        : subjectData.status === "not_submitted" ? "Cette question n'a pas encore été envoyée."
        : subjectData.status === "rejected" ? "Cette question n'a pas été validée par le professeur, vous devez en envoyer une nouvelle."
        : subjectData.status === "approved" ? "Cette question a été validée, vous ne pouvez plus la modifier." : ""

    return (
        <div className={clsx(
            "relative mt-3 group min-w-50",
            {
                "mb-5": helper !== undefined,
                "mb-1": helper === undefined
            },
        )}
        >
            <input type="text"
                   id={id}
                   name={id}
                   placeholder=" "
                   spellCheck="true"
                   disabled={rejected}
                   defaultValue={rejected ? subjectData.lastRejected : subjectData.subject}
                   onChange={onChange}
                   className={clsx(
                       "peer block w-full py-0 px-0 cursor-text text-base text-heading " +
                       "bg-transparent border-0 border-b-2 border-sky-300 " +
                       "appearance-none focus:outline-none focus:ring-0 ",
                       {
                           "border-green-600!": subjectData.status === "approved",
                           "border-amber-600!": subjectData.status === "submitted",
                           "border-red-600!": subjectData.status === "rejected",
                       },
                   )}
            />

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
                   className="absolute text-base text-body cursor-text duration-300 transform
                       -translate-y-4.5 scale-75 top-0 origin-left
                       peer-placeholder-shown:scale-100
                       peer-placeholder-shown:translate-y-0
                       peer-focus:scale-75
                       peer-focus:-translate-y-4.5 "
            >
                {label}
            </label>

            {helper.length !== 0 && !rejected && <p className="mt-0 absolute text-[13px] italic text-gray-600">
                *{helper}
            </p>}
        </div>
    );
}

"use client";

import { clsx } from "clsx";
import { SubjectInputMultilineProps } from "@/app/lib/types";
import {SubjectTextArea} from "@/app/ui/inputs/subject-text-area";
import Checkbox from "@/app/ui/inputs/checkbox";

export function SubjectInputStudent({
                                        id,
                                        label,
                                        helper,
                                        subjectData,
                                        setIsInterdisciplinaryAction = () => {},
                                        onChangeAction
                                    }: SubjectInputMultilineProps) {
    helper = subjectData.status === "submitted" ? "Cette question est en attente de validation, vous ne pouvez plus la modifier."
                : subjectData.status === "not_submitted" ? "Cette question n'a pas encore été envoyée."
                : subjectData.status === "rejected" ? "Cette question n'a pas été validée par le professeur, vous devez en envoyer une nouvelle."
                : subjectData.status === "approved" ? "Cette question a été validée, vous ne pouvez plus la modifier."
                : "";

    const editable = subjectData.status != "approved";

    return (
        <div
            className={clsx("relative mt-3 group min-w-50", {
                "mb-5": helper !== undefined,
                "mb-1": helper === undefined,
            })}
        >
            <SubjectTextArea
                id={id}
                disabled={!editable}
                defaultValue={subjectData.status == "rejected" ? subjectData.lastRejected : subjectData.subject}
                onChangeAction={onChangeAction}
                label={label}
                subjectData={subjectData}
                animate={true}
            />

            <div className="flex flex-row content-between w-full">
                {helper.length !== 0 && (
                    <p className="mt-0 self-center relative text-[13px] italic text-gray-600">
                        *{helper}
                    </p>
                )}

                <Checkbox className="ml-auto self-center"
                          label="Question transversale"
                          setValue={setIsInterdisciplinaryAction}
                          defaultValue={subjectData.interdisciplinary}
                />
            </div>
        </div>
    );
}
'use client';

import {useSubjects} from "@/app/hooks/useSubjects";
import TeacherCard from "@/app/ui/subjects/teacher-card";
import {SubjectsSearchBar} from "@/app/ui/subjects/subjects-search-bar";
import {useState} from "react";
import {Subject} from "@/app/lib/types";

export default function Page() {
    const {subjects, refetch} = useSubjects("teacher");
    const [currentSubjects, setCurrentSubjects] = useState<Subject[] | null>(subjects || null);

    return (
        <div className="relative flex flex-col grow h-fit items-center top-10">
            <div className="h-auto w-fit p-2">
                <p className="underline text-2xl self-start mb-3">Les questions de vos élèves :</p>
                <SubjectsSearchBar subjects={subjects} setCurrentSubjects={setCurrentSubjects} />
                <div className="flex flex-col gap-2">
                    {currentSubjects?.map((s) => (
                        <TeacherCard
                            key={`card-teacher-for-${s.student}-${s.topic}`}
                            subjectData={s}
                            onUpdateAction={refetch}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}

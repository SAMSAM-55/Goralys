'use client';

import {useSubjects} from "@/app/hooks/useSubjects";
import TeacherCard from "@/app/ui/subjects/teacher-card";

export default function Page() {
    const {subjects, refetch} = useSubjects("teacher");

    return (
        <div className="relative flex flex-col grow h-fit items-center top-10">
            <div className="h-auto w-fit p-2">
                <p className="underline text-2xl self-start mb-3">Vos questions :</p>
                <div className="flex flex-col gap-2">
                    {subjects?.map((s) => (
                        <TeacherCard
                            key={s.studentToken + s.teacherToken + s.topic}
                            subjectData={s}
                            onUpdateAction={refetch}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}

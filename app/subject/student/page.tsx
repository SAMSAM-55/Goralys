'use client';

import StudentCard from "@/app/ui/subjects/student-card";
import {useSubjects} from "@/app/hooks/useSubjects";
import Cookies from "universal-cookie";

export default function Page() {
    const {subjects, refetch, syncKey} = useSubjects("student");
    const cookies = new Cookies();
    const updateSubjects = async () => {
        cookies.set(syncKey, "0", { path: '/' });
        await refetch();
    }

    return (
        <div className="relative flex flex-col grow h-fit items-center top-10">
            <div className="h-auto w-fit p-2">
                <p className="underline text-2xl self-start mb-3">Vos questions :</p>
                <div className="flex flex-col gap-2">
                    {subjects?.map((s) => (
                        <StudentCard
                            key={s.studentToken + s.teacherToken + s.topic}
                            subjectData={s}
                            onUpdateAction={updateSubjects}
                        />
                    ))}
                </div>
            </div>
        </div>
    );
}

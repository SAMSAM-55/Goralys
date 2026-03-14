import {FloatingInput} from "@/app/ui/inputs/floating-input";
import {FormEvent, useEffect, useState} from "react";
import {searchFields, Subject, SubjectsSearchBarProps, SubjectsSearchField} from "@/app/lib/types";

export function SubjectsSearchBar({subjects, setCurrentSubjects}: SubjectsSearchBarProps) {
    const [currentField, setCurrentField] = useState<SubjectsSearchField>("all");
    const [searchText, setSearchText] = useState("");
    
    function handleSearch(e: FormEvent<HTMLInputElement>) {
        const value = e.currentTarget.value;
        setSearchText(value);
    }

    useEffect(() => {
        if (!subjects) return;

        const search = searchText.trim().toLowerCase();
        if (!search) {
            setCurrentSubjects(subjects);
            return;
        }

        setCurrentSubjects(subjects.filter((s: Subject) => {
            switch (currentField) {
                case "student":
                    return s.student.trim().toLowerCase().startsWith(search);
                case "teacher":
                    return s.teacher.trim().toLowerCase().startsWith(search);
                case "topic":
                    return s.topic.trim().toLowerCase().startsWith(search);
                case "all":
                    return s.student.trim().toLowerCase().startsWith(search) ||
                        s.teacher.trim().toLowerCase().startsWith(search) ||
                        s.topic.trim().toLowerCase().startsWith(search);
            }
        }));
    }, [searchText, currentField, subjects, setCurrentSubjects]);

    return (
        <div className="flex flex-row gap-2 items-end mb-4">
            <FloatingInput id="admins-subjects-search" label="Rechercher" onInput={handleSearch} />

            <select
                className="border-0 border-b-2 border-sky-300 appearance-none
               cursor-pointer outline-none focus:ring-0 text-base leading-5
               text-heading pb-0 pr-5 mb-1 subjects-search-select"
                value={currentField}
                onChange={(e) => setCurrentField(e.target.value as SubjectsSearchField)}
            >
                {Object.entries(searchFields).map(([key, label]) => (
                    <option value={key} key={key}>{label}</option>
                ))}
            </select>
        </div>
    );
}
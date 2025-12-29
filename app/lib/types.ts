import {ChangeEventHandler, MouseEventHandler, RefObject} from "react";

export type UserRole = {
    role: "admin" | "teacher" | "student",
};

export type SubjectStatus = "not_submitted" | "submitted" | "rejected" | "approved";

export type Subject = {
    comment: string,
    lastRejected?: string,
    status: SubjectStatus,
    student: string,
    studentToken: string,
    subject: string,
    teacher: string,
    teacherToken: string,
    topic: string
};

export type InputProps = {
    autocomplete?: string,
    disabled?: boolean,
    helper?: string,
    id: string,
    label: string,
    password?: boolean,
    required?: boolean,
    defaultValue?: string,
};

export type TextAreaProps = {
    defaultValue?: string,
    disabled?: boolean,
    ref?:RefObject<HTMLTextAreaElement | null>
    helper?: string,
    id: string,
    label: string,
    onChangeAction?: ChangeEventHandler<HTMLTextAreaElement>
};

export type SubjectInputProps = {
    autocomplete?: string,
    animate?: boolean,
    disabled?: boolean,
    helper?: string,
    id: string,
    label: string,
    password?: boolean,
    required?: boolean,
    value?: string,
    onChange?: ChangeEventHandler<HTMLInputElement>,
    status?: SubjectStatus,
};

export type ButtonProps = {
    className?: string,
    text: string,
    type: "submit" | "button" | "reset",
    onClick?: MouseEventHandler<HTMLButtonElement>,
};

export type ToastProps = {
    type: "info" | "warning" | "error" | "success",
    title: string,
    message: string,
    visible: boolean,
};

export type Toast = {
    type: "error" | "warning" | "info" | "success",
    title: string,
    message: string,
};

export type AuthEvent = "unauthenticated" | "expired";

export type UserEvent = "login" | "logout" | "register";
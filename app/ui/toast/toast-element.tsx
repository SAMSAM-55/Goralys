'use client';

import {clsx} from "clsx";
import {ToastProps} from "@/app/lib/types";

export default function ToastElement({ type, title, message, visible }: ToastProps) {
    return (
        <div
            className={clsx(
                "absolute flex gap-2 p-3 h-22 w-115 bg-sky-300 rounded shadow overflow-hidden left-1/2 -translate-x-1/2 top-1 ",
                "after:absolute after:left-0 after:top-0 after:h-full after:w-1.25 after:content-['']",
                "transition-all duration-500 z-10 ",
                {
                    "after:bg-blue-600": type === "info",
                    "after:bg-green-600": type === "success",
                    "after:bg-amber-600": type === "warning",
                    "after:bg-red-600": type === "error",
                },
                {
                    "translate-y-0 opacity-100": visible,
                    "-translate-y-5 opacity-0": !visible,
                }
            )}
            role="alert"
        >
            <div className="flex gap-3">
                <div className="w-11 h-11 flex self-center items-center justify-center">
                    <i
                        className={clsx(
                            "text-4xl fas",
                            {
                                "fa-circle-info": type === "info",
                                "fa-circle-check": type === "success",
                                "fa-circle-exclamation": type === "warning",
                                "fa-circle-xmark": type === "error",
                            },
                            {
                                "text-blue-600": type === "info",
                                "text-green-600": type === "success",
                                "text-amber-600": type === "warning",
                                "text-red-600": type === "error",
                            }
                        )}
                    />
                </div>

                <div className="flex flex-col justify-center">
                    <strong className="text-md">{title}</strong>
                    <span className="text-sm">{message}</span>
                </div>
            </div>
        </div>
    );
}

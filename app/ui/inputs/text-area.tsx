'use client';

import {clsx} from "clsx";
import {useCallback, useEffect} from "react";
import {TextAreaProps} from "@/app/lib/types";

const MAX_HEIGHT = 111;

function isMultiline(el: HTMLTextAreaElement): boolean {
    const originalWhiteSpace = el.style.whiteSpace;
    const originalOverflowX = el.style.overflowX;
    const originalPadding = el.style.padding;

    // Temporarily force single-line behavior
    el.style.whiteSpace = "nowrap";
    el.style.overflowX = "scroll";
    el.style.padding = "0";

    const isMulti = el.scrollWidth > el.clientWidth;

    // Restore styles
    el.style.whiteSpace = originalWhiteSpace;
    el.style.overflowX = originalOverflowX;
    el.style.padding = originalPadding;

    return isMulti;
}



export function TextArea({
                             id,
                             label,
                             helper,
                             disabled = false,
                             ref,
                             defaultValue,
                             onChangeAction,
                         }: TextAreaProps) {
    const resize = useCallback(() => {
        if (!ref?.current) return;

        const hasNewline = ref.current.value.includes("\n");
        const isSingleLine = !(hasNewline || isMultiline(ref.current));


        ref.current.style.height = "auto";

        const newHeight = Math.min(ref.current.scrollHeight - (isSingleLine ? 15 : 0), MAX_HEIGHT);
        ref.current.style.height = `${newHeight}px`;

        ref.current.style.overflowY =
            ref.current.scrollHeight > MAX_HEIGHT ? "auto" : "hidden";
    }, [ref]);

    useEffect(() => {
        resize();
    }, [defaultValue, resize]);

    return (
        <div
            className={clsx(
                "relative mt-3 mb-1 group min-w-50",
                {
                    "mb-5!": !!helper
                }
            )}
        >
            <textarea
                ref={ref}
                id={id}
                name={id}
                placeholder=" "
                spellCheck="true"
                defaultValue={defaultValue}
                readOnly={disabled}
                onChange={onChangeAction}
                onInput={resize}
                className="
                    peer block w-full py-0 leading-5 px-0 text-base text-heading
                    resize-none bg-transparent
                    border-0 border-b-2 border-sky-300
                    appearance-none focus:outline-none focus:ring-0
                "
            />

            <label
                htmlFor={id}
                className={clsx(
                    "absolute text-base text-body cursor-text duration-300 transform " +
                    "-translate-y-4.5 scale-75 top-0 origin-left " +
                    "peer-placeholder-shown:scale-100 " +
                    "peer-placeholder-shown:translate-y-0 " +
                    "peer-focus:scale-75 " +
                    "peer-focus:-translate-y-4.5",
                    {
                        "cursor-not-allowed": disabled
                    }
                )}
            >
                {label}
            </label>

            <p
                className={clsx(
                    "mt-0 absolute text-[13px] italic text-gray-600",
                    {
                        "hidden": helper === undefined
                    }
                )}
            >
                *{helper}
            </p>
        </div>
    );
}

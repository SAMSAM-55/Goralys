import {clsx} from "clsx";
import {useState} from "react";
import {InputProps} from "@/app/lib/types";

export function FloatingInput({ id, label, helper, autocomplete, password = false, disabled = false, required = false, defaultValue, onInput}: InputProps) {
    const [show, setShow] = useState<boolean>(!password);

    function onEyeClicked() {
        setShow(!show);
    }

    return (
        <div className={clsx(
            "relative mt-3 group min-w-50 w-full",
            {
                "mb-5": helper !== undefined,
                "mb-1": helper === undefined
            },
        )}
        >
            <input type={show ? "text" : "password"}
                   id={id}
                   name={id}
                   placeholder=" "
                   autoComplete={autocomplete}
                   disabled={disabled}
                   required={required}
                   defaultValue={defaultValue}
                   onInput={onInput}
                   className={clsx(
                       "peer block w-full leading-none py-0 px-0 cursor-text text-base text-heading " +
                       "bg-transparent border-0 border-b-2 border-sky-300 " +
                       "appearance-none focus:outline-none focus:ring-0 ",
                       {
                           "cursor-not-allowed!": disabled
                       },
                   )}
            />

            {/* Animated underline */}
            <span className="
              pointer-events-none
              absolute left-0 bottom-0 h-0.5 w-full
              origin-left scale-x-0
              bg-sky-500
              transition-transform duration-250
              group-focus-within:scale-x-100
              "
            />

            <label htmlFor={id}
                   className={clsx(
                       "absolute text-base text-body cursor-text duration-300 transform " +
                       "-translate-y-4.5 scale-75 top-0 origin-left " +
                       "peer-placeholder-shown:scale-100 " +
                       "peer-placeholder-shown:translate-y-0 " +
                       "peer-focus:scale-75 " +
                       "peer-focus:-translate-y-4.5 ",
                       {
                           "cursor-not-allowed!": disabled
                       },
                       )}
            >
                {label}
            </label>

            {password && (<button type="button" onClick={onEyeClicked} className="absolute top-px right-0 text-gray-900">
                <i className={clsx(
                    "fas",
                    {
                        "fa-eye": !show,
                        "fa-eye-slash": show
                    }
                )}
                />
            </button>
            )}

            <p className={clsx(
                'mt-0 absolute text-[13px] italic text-gray-600',
                {
                "hidden": helper === undefined
                },
            )}>
                *{helper}
            </p>
        </div>
    );
}

import {clsx} from "clsx";

export function InputText({ id, label, helper }: { id: string, label: string, helper?: string }) {
    return (
        <div className={clsx(
            "relative mt-3 group min-w-50",
            {
                "mb-5": helper !== undefined,
                "mb-1": helper === undefined
            },
        )}
        >
            <input type="text" id={id} name={id} placeholder=" " className="
                peer block w-full py-0 px-0  text-base text-heading
                bg-transparent border-0 border-b-2 border-sky-300
                appearance-none focus:outline-none focus:ring-0
                "
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

            <label htmlFor={id} className="
                absolute text-base text-body duration-300 transform
                -translate-y-4.5 scale-75 top-0 origin-left
                peer-placeholder-shown:scale-100
                peer-placeholder-shown:translate-y-0
                peer-focus:scale-75
                peer-focus:-translate-y-4.5
                "
            >
                {label}
            </label>

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

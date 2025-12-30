import {ButtonProps} from "@/app/lib/types";

export function Button({ text, type, className, onClick}: ButtonProps) {
    return (
        <button type={type} className={`
            relative block w-full h-10 bg-sky-100 border-sky-300 border mt-2 mb-2 rounded-xs shadow-lg z-0
            transition-colors duration-500 overflow-hidden

            before:w-0 before:h-full before:content-[''] before:bg-sky-300 before:absolute before:left-0 before:top-0 before:z-0
            before:transition-all before:duration-500
            hover:text-gray-900 hover:before:w-full
            ${className}
        `}
        onClick={onClick}>
            <p className="relative text-[17px] z-10">{text}</p>
        </button>
    );
}

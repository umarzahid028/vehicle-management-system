import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

/**
 * Combine multiple class names with Tailwind CSS
 */
export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

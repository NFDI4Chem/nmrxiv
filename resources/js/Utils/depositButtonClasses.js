/**
 * Shared CTA styles for Deposit data buttons (nav + sidebar).
 * Uses theme `teal-*` tokens so Tailwind includes them when this file is scanned.
 *
 * @param {{ fullWidth?: boolean, size?: 'nav' | 'sidebar' | 'sidebarCompact' | 'icon' }} [options]
 * @returns {string}
 */
export function depositButtonClasses(options = {}) {
    const { fullWidth = false, size = "nav" } = options;

    const base = [
        "inline-flex items-center justify-center text-white",
        "bg-teal-700 hover:bg-teal-400",
        "shadow-md shadow-teal-700/40 hover:shadow-lg hover:shadow-teal-600/50",
        "border border-teal-600/50",
        "transition-all duration-200 hover:-translate-y-0.5",
        "active:translate-y-0 active:shadow-md",
        "focus:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2",
    ];

    if (size === "icon") {
        return [...base, "rounded-full p-3 font-semibold"].join(" ");
    }

    const sizeClasses = {
        nav: [
            "gap-2 rounded-full font-semibold",
            fullWidth
                ? "w-full px-4 py-2.5 text-sm"
                : "whitespace-nowrap px-5 py-2.5 text-sm",
        ],
        sidebar: [
            "gap-2 rounded-full w-full px-6 py-3 text-base font-semibold",
        ],
        sidebarCompact: ["gap-2 rounded-full px-4 py-2 text-sm font-semibold"],
    };

    return [...base, ...(sizeClasses[size] ?? sizeClasses.nav)].join(" ");
}

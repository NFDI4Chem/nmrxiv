import {
    FolderIcon,
    SwatchIcon,
    InformationCircleIcon,
    QuestionMarkCircleIcon,
} from "@heroicons/vue/24/outline";

export const publicNavItems = [
    {
        name: "Projects",
        href: "/projects",
        icon: FolderIcon,
        match: ["/projects"],
    },
    {
        name: "Spectra Library",
        href: "/search?scope=compounds",
        icon: SwatchIcon,
        match: ["/compounds"],
        searchScope: "compounds",
    },
    {
        name: "About",
        href: "/about-us",
        icon: InformationCircleIcon,
        match: ["/about-us"],
    },
    {
        name: "FAQs",
        href: "/faqs",
        icon: QuestionMarkCircleIcon,
        match: ["/faqs"],
    },
];

export function isPublicNavActive(url, item) {
    if (item.searchScope) {
        try {
            const parsed = new URL(url, "http://localhost");

            if (
                parsed.pathname === "/search" &&
                parsed.searchParams.get("scope") === item.searchScope
            ) {
                return true;
            }
        } catch {
            // fall through to path matching
        }
    }

    return item.match.some(
        (path) => url === path || url.startsWith(`${path}/`)
    );
}

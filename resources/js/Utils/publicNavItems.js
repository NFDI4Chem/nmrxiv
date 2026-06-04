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
        href: "/compounds",
        icon: SwatchIcon,
        match: ["/compounds"],
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
    return item.match.some(
        (path) => url === path || url.startsWith(`${path}/`)
    );
}

import {
    hashString,
    seededCoverStyle,
    seededCoverSeed,
} from "../resources/js/Utils/seededCoverStyle.js";

const project = { identifier: "NMRXIV-42", name: "Test Project", id: 7 };
const seed = seededCoverSeed(project);

if (seed !== "NMRXIV-42") {
    console.error("Expected identifier to be preferred seed");
    process.exit(1);
}

const styleA = seededCoverStyle("NMRXIV-42");
const styleB = seededCoverStyle("NMRXIV-42");
const styleC = seededCoverStyle("other-project");

if (JSON.stringify(styleA) !== JSON.stringify(styleB)) {
    console.error("Seeded cover style must be deterministic");
    process.exit(1);
}

if (JSON.stringify(styleA) === JSON.stringify(styleC)) {
    console.error("Different seeds must produce different cover styles");
    process.exit(1);
}

if (hashString("") !== 0) {
    console.error("Empty string hash should be zero");
    process.exit(1);
}

const similarA = seededCoverStyle("NMRXIV:P43559");
const similarB = seededCoverStyle("NMRXIV:P43555");

if (JSON.stringify(similarA) === JSON.stringify(similarB)) {
    console.error("Similar identifiers should produce distinct hues");
    process.exit(1);
}

console.log("ok");

# Offline BagIt Viewer

Standalone single-file HTML app that validates and displays nmrXiv BagIt archives
entirely in the browser. Built separately from the main Vue/Inertia app so React
and NMRium stay out of the site bundle.

## Develop

```bash
cd resources/bagit-viewer
npm install
npm run dev
```

## Build

From the repository root:

```bash
npm run build:bagit-viewer
```

Output: `resources/bagit-viewer/dist/nmrxiv-bagit-viewer.html`

## Test

```bash
cd resources/bagit-viewer
npm test
```

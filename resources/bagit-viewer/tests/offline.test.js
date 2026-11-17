import { describe, expect, it } from 'vitest';
import { prepareOfflineHtml } from '../src/build/prepareOfflineHtml.js';
import {
    installFileProtocolIframeGuard,
    pinIframeToBlank,
} from '../src/fileProtocol.js';
import {
    fileFromPayloadEntry,
    payloadFileRelativePath,
} from '../src/nmrium/payloadFiles.js';

describe('payloadFileRelativePath', () => {
    it('keeps Bruker experiment trees and JCAMP/JEOL files', () => {
        expect(payloadFileRelativePath('data/10/fid')).toBe('10/fid');
        expect(payloadFileRelativePath('data/10/acqus')).toBe('10/acqus');
        expect(payloadFileRelativePath('data/10/pdata/1/1r')).toBe(
            '10/pdata/1/1r',
        );
        expect(payloadFileRelativePath('data/sample.jdf')).toBe('sample.jdf');
        expect(payloadFileRelativePath('data/nested/nmr_fid.dx')).toBe(
            'nested/nmr_fid.dx',
        );
        expect(payloadFileRelativePath('data/raw/experiment.zip')).toBe(
            'raw/experiment.zip',
        );
    });

    it('skips tag files, previews, and nmrxiv metadata', () => {
        expect(payloadFileRelativePath('bagit.txt')).toBeNull();
        expect(payloadFileRelativePath('data/nmrxiv-meta/study.nmrium')).toBeNull();
        expect(payloadFileRelativePath('data/nmrxiv-meta/images/1.png')).toBeNull();
        expect(payloadFileRelativePath('data/preview.png')).toBeNull();
        expect(payloadFileRelativePath('data/bio-schema.json')).toBeNull();
        expect(payloadFileRelativePath('data/README.txt')).toBeNull();
        expect(payloadFileRelativePath('data/__MACOSX/10/fid')).toBeNull();
    });
});

describe('fileFromPayloadEntry', () => {
    it('exposes the payload-relative path on the File', () => {
        const file = fileFromPayloadEntry(new Blob(['fid']), '10/pdata/1/1r');

        expect(file.name).toBe('1r');
        expect(file.webkitRelativePath).toBe('10/pdata/1/1r');
        expect(file.path).toBe('10/pdata/1/1r');
    });
});

describe('prepareOfflineHtml', () => {
    it('drops crossorigin so file:// does not refetch this document', () => {
        const html = prepareOfflineHtml(
            '<script type="module" crossorigin>var app=1;</script><style rel="stylesheet" crossorigin>.a{}</style>',
        );

        expect(html).toContain('<script type="module">var app=1;</script>');
        expect(html).not.toContain('crossorigin');
        expect(html).toContain('<style>.a{}</style>');
    });
});

describe('file protocol iframe guard', () => {
    it('pins new iframes to about:blank and blocks file: src', () => {
        const doc = fakeDocument();
        installFileProtocolIframeGuard(doc, 'file:');

        const iframe = doc.createElement('iframe');
        const div = doc.createElement('div');

        expect(iframe.getAttribute('src')).toBe('about:blank');
        expect(div.getAttribute('src')).toBeUndefined();

        iframe.setAttribute(
            'src',
            'file:///Users/example/nmrxiv-bagit-viewer.html',
        );
        expect(iframe.getAttribute('src')).toBe('about:blank');

        iframe.setAttribute('src', 'about:blank');
        expect(iframe.getAttribute('src')).toBe('about:blank');
    });

    it('does nothing for http pages', () => {
        const doc = fakeDocument();
        installFileProtocolIframeGuard(doc, 'https:');
        const iframe = doc.createElement('iframe');

        expect(iframe.getAttribute('src')).toBeUndefined();
    });

    it('rewrites file: src on an existing iframe', () => {
        const iframe = fakeElement('iframe');
        pinIframeToBlank(iframe);
        iframe.setAttribute('src', 'file:///tmp/viewer.html');

        expect(iframe.getAttribute('src')).toBe('about:blank');
    });
});

function fakeDocument() {
    return {
        createElement(tag) {
            return fakeElement(tag);
        },
    };
}

function fakeElement(tag) {
    const attrs = {};

    return {
        tag,
        setAttribute(name, value) {
            attrs[name] = value;
        },
        getAttribute(name) {
            return attrs[name];
        },
    };
}

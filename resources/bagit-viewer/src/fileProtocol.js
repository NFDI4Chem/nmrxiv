/**
 * file:// documents are unique origins. An iframe whose URL is this HTML
 * file is blocked, and Safari logs "Unsafe attempt to load URL … from frame".
 * NMRium's print/export iframe is created without src, which Safari resolves
 * to the parent file URL. Pin those iframes to about:blank instead.
 *
 * @param {Document} [doc]
 * @param {string} [protocol]
 */
export function installFileProtocolIframeGuard(
    doc = globalThis.document,
    protocol = globalThis.location?.protocol,
) {
    if (protocol !== 'file:' || typeof doc?.createElement !== 'function') {
        return;
    }

    if (doc.__nmrxivFileIframeGuard) {
        return;
    }

    doc.__nmrxivFileIframeGuard = true;
    const create = doc.createElement.bind(doc);

    doc.createElement = function (tag, options) {
        const element = create(tag, options);

        if (String(tag).toLowerCase() === 'iframe') {
            pinIframeToBlank(element);
        }

        return element;
    };
}

/**
 * @param {HTMLIFrameElement} iframe
 */
export function pinIframeToBlank(iframe) {
    const setAttribute =
        typeof iframe.setAttribute === 'function'
            ? iframe.setAttribute.bind(iframe)
            : null;

    if (setAttribute) {
        iframe.setAttribute = (name, value) => {
            if (String(name).toLowerCase() === 'src' && isBlockedFrameUrl(value)) {
                setAttribute('src', 'about:blank');

                return;
            }

            setAttribute(name, value);
        };
        iframe.setAttribute('src', 'about:blank');
    }

    const prototype = Object.getPrototypeOf(iframe);
    const iframePrototype =
        typeof HTMLIFrameElement === 'undefined'
            ? null
            : HTMLIFrameElement.prototype;
    const descriptor =
        (prototype && Object.getOwnPropertyDescriptor(prototype, 'src')) ||
        (iframePrototype &&
            Object.getOwnPropertyDescriptor(iframePrototype, 'src'));

    if (!descriptor?.get || !descriptor?.set) {
        return;
    }

    Object.defineProperty(iframe, 'src', {
        configurable: true,
        enumerable: true,
        get() {
            return descriptor.get.call(iframe);
        },
        set(value) {
            descriptor.set.call(
                iframe,
                isBlockedFrameUrl(value) ? 'about:blank' : value,
            );
        },
    });
}

/**
 * @param {unknown} value
 * @returns {boolean}
 */
function isBlockedFrameUrl(value) {
    const next = String(value ?? '').trim();

    return next === '' || next.startsWith('file:');
}

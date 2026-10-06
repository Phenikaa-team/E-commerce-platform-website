export function projectAssetUrl(path) {
    const normalizedPath = String(path).replace(/^\/+/, '');
    const configuredBase = document.querySelector('meta[name="project-assets-base"]')?.content?.trim();

    if (!configuredBase) {
        return `/${normalizedPath}`;
    }

    return `${configuredBase.replace(/\/+$/, '')}/${normalizedPath.split('/').map(encodeURIComponent).join('/')}`;
}

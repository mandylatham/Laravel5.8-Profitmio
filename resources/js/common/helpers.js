export function generateRoute (route, params) {
    if (!route) return;
    for (const key of Object.keys(params)) {
        route = route.replace(new RegExp(':'+key, 'g'), params[key]);
    }
    return route;
}

export function replacePlaceholders(stringToSearch, params) {
    if (!stringToSearch || !params) return stringToSearch;
    for (const key of Object.keys(params)) {
        stringToSearch = stringToSearch.replace(new RegExp('{{\\s*' + key + '\\s*}}', 'g'), params[key]);
    }
    return stringToSearch;
}

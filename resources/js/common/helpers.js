export function generateRoute (route, params) {
    if (!route) return;
    for (const key of Object.keys(params)) {
        route = route.replace(new RegExp(':'+key, 'g'), params[key]);
    }
    return route;
}

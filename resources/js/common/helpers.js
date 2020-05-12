export function generateRoute (route, params) {
    if (!route) return;
    for (const key of Object.keys(params)) {
        route = route.replace(new RegExp(':'+key, 'g'), params[key]);
    }
    return route;
}

export function getRequestError(error) {
    let messages = ['Unable to process your request.'];
    if (error) {
        const response = error.response;
        if (response.data) {
            if (response.data.errors && response.status === 422) {
                messages = [];
                Object.values(response.data.errors).forEach(errors => {
                    messages = messages.concat(errors);
                });
            } else if (response.data.message) {
                messages = [response.data.message];
            }
        }
    }
    return messages.join('<br>');
}

export function replacePlaceholders(stringToSearch, params) {
    if (!stringToSearch || !params) return stringToSearch;
    for (const key of Object.keys(params)) {
        stringToSearch = stringToSearch.replace(new RegExp('{{\\s*' + key + '\\s*}}', 'g'), params[key]);
    }
    return stringToSearch;
}

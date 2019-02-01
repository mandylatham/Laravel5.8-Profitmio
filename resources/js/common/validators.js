// Phone Numbers
export function isNorthAmericanPhoneNumber(value) {
    if (value.length == "") return true;

    const phoneRegex = new RegExp(/^(\+\d{1,2}[.-\s]?)?\(?\d{3}\)?[.-\s]?\d{3}[.-\s]?\d{4}$/);

    return !!value.match(phoneRegex);
}

// CA Postal Code
export function isCanadianPostalCode(value) {
    if (value.length == "") return true;

    const postalRegex = /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/;

    return !! value.match(postalRegex);
}

// US Postal Code
export function isUnitedStatesPostalCode(value) {
    if (value.length == "") return true;

    const postalRegex = /^[0-9]{5}(-[0-9]{4})?$/;

    return !! value.match(postalRegex);
}

// Loose Address Match
export function looseAddressMatch(value) {
    if (value.length == "") return true;

    const addressRegex = new RegExp(/^\d+[\d\w]+(\s+[\w\d\'\.\(\)]+){2,}$/);

    return !! value.match(addressRegex);
}
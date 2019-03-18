/*
 * Session timeout handler
 */
import axios from 'axios';

/**
 * Local storage key name
 * @type {string}
 */
const STORAGE_KEY = 'session.expired';
const STORAGE_UPDATE_KEY = 'session.lastUpdate';
const STORAGE_TRUE = '1';
const STORAGE_FALSE = '0';

/**
 * Session lifetime in milliseconds
 * @type {number}
 */
const LIFETIME = window.sessionLifetime * 60000;

let expired = false;
let timeout = null;

/**
 * Set state
 *
 * @param value
 */
function setExpired(value = false) {
    const val = value ? STORAGE_TRUE : STORAGE_FALSE;

    window.localStorage.setItem(STORAGE_UPDATE_KEY, new Date().getTime().toString());
    window.localStorage.setItem(STORAGE_KEY, val);
}

/**
 * Restore window session
 */
function restore(globally = true) {
    resetTimeout();

    expired = false;

    if (globally) {
        setExpired(false);
    }
}

/**
 * Expire window session
 *
 * @param globally
 */
function expire(globally = true)
{
    clearTimeout(timeout);

    expired = true;

    if (globally) {
        setExpired(true);
    }

    window.PmEvent.fire('errors.sessionTimeout');
}

/**
 * Reset timeout shortcut
 */
function resetTimeout() {
    if (timeout) {
        clearTimeout(timeout);
    }

    timeout = setTimeout(expire, LIFETIME);
}

/**
 * Check if the url is on the same host
 *
 * @param url
 */
function isSameHost(url) {
    return (url.charAt(0) === '/') || (url.indexOf(window.location.hostname) >= 0);
}

/*
 * Local storage event listener
 *
 * A STORAGE_UPDATE_KEY value is changed every time the response has been received in another tab
 *
 * A STORAGE_KEY will be changed only if our session has been expired in another tab,
 * or if the user performed login action in another tab
 */
window.addEventListener('storage', (event) => {
    if (STORAGE_KEY === event.key) {
        if (STORAGE_TRUE === event.newValue) {
            expire();
        }
        else {
            restore(false);
            window.location.reload(true);
        }
    }

    if (STORAGE_UPDATE_KEY === event.key) {
        restore(false);
    }
});

/*
 * Axios interceptors configuration
 *
 * We filter our responses by the same host,
 * just in case if Axios will be used for 3rd-party APIs
 */
axios.interceptors.request.use(
    (config) => expired ? Promise.reject('Session expired') : config
);

axios.interceptors.response.use(
    (response) => {
        if (isSameHost(response.request.responseURL)) {
            restore();
        }

        return response;
    },
    (error) => {
        if (isSameHost(error.response.request.responseURL)) {
            if (error.response.status === 403) {
                expire();
            }

            restore();
        }

        return Promise.reject(error);
    }
);

/*
 * Initialize localStorage variables and update session for the rest tabs
 */
restore();

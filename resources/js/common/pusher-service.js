import Pusher from 'pusher-js';

const PUSHER_KEY = 'b926f342d692e99ff8b8';
const PUSHER_CLUSTER = 'mt1';
const PUSHER_AUTH_ENDPOINT = window.pusherAuthEndpoint;

export default class PusherService {
    constructor() {
        // TODO: Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        this.subscribedTo = {};

        this.socket = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            forceTLS: true,
            authEndpoint: PUSHER_AUTH_ENDPOINT,
            auth: {
                headers: {
                    'X-CSRF-Token': window.csrfToken
                }
            }
        });
    }

    subscribe(channelName) {
        return this.subscribedTo[channelName] || (this.subscribedTo[channelName] = this.socket.subscribe(channelName));
    }

    disconnect() {
        return this.socket.disconnect();
    }
}

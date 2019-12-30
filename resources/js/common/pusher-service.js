import Pusher from 'pusher-js';

const PUSHER_KEY = window.pusherKey;
const PUSHER_CLUSTER = window.pusherCluster;
const PUSHER_AUTH_ENDPOINT = window.pusherAuthEndpoint;

export default class PusherService {
    constructor() {
        // TODO: Enable pusher logging - don't include this in production
        Pusher.logToConsole = false;

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

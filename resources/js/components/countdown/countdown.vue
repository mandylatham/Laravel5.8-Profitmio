<template>
    <div class="countdown" :class="this.timeLeftClass">
        <span class="countdown-icon fa fa-clock mr-2"></span>
        {{ timeLeft }}
    </div>
</template>
<script>
    export default {
        props: {
            secondsLeft: {
                type: Number,
                required: true,
            }
        },
        computed: {
            timeLeftClass: function () {
                if (this.seconds < 5*60 && this.seconds > 1*60) {
                    return 'text-danger';
                } else if (this.seconds < 1*60) {
                    return 'text-red';
                } else {
                    return 'text-primary';
                }
            },
            timeLeft: function () {
                return (new Date(this.seconds * 1000)).toUTCString().match(/(\d\d:\d\d:\d\d)/)[0];
            }
        },
        data() {
            return {
                interval: null,
                seconds: null
            }
        },
        mounted() {
            this.seconds = Number(this.secondsLeft);
            if (this.seconds > 0) {
                this.interval = setInterval(() => {
                    if (this.seconds < 1) {
                        clearInterval(this.interval);
                    } else {
                        this.seconds--;
                    }
                }, 1000);
            }
        }
    }
</script>

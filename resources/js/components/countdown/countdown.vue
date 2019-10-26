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
                var delta = this.seconds;
                var days = Math.floor(delta / 86400);
                delta -= days * 86400;
                var hours = Math.floor(delta / 3600) % 24;
                delta -= hours * 3600;
                var minutes = Math.floor(delta / 60)% 60;
                delta -= minutes * 60;
                var seconds = delta % 60;

                return (days ? days+"d " : '')+(hours ? hours + ":" : '00:')+(minutes ? minutes+':' : '00:')+(seconds ? seconds : '00');
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

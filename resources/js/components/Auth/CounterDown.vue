<template>
    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"
         :width="width" :height="width"
    >
        <circle cx="100" cy="100" :r="100 - border / 2"
                fill="none"
                opacity="0.4"
                :stroke="color"
                :stroke-width="border"
        />
        <circle cx="100" cy="100" :r="100 - border / 2"
                fill="none"
                transform="rotate(270,100,100)"
                :stroke="color"
                :stroke-width="border"
                :stroke-dasharray="dashLen"
                :stroke-dashoffset = "dashOffset"
                style="transition: stroke-dashoffset 0.4s"
        />
        <text x="100" y="100" text-anchor="middle"
              :font-size="fontSize + 5"
              :fill="color"
        >
            {{ countDown }}
        </text>
        <text x="100" y="150" text-anchor="middle"
              font-size="30"
              fill="#FCA0014D"
        >
            ثانیه
        </text>
    </svg>
</template>

<script>
export default {
    name: "CounterDown",
    props: {
        setTimer: {
            type: Number,
            default : 120
        },
        width: {
            type: Number,
            default: 80
        },
        border: {
            type: Number,
            default: 8
        },
        color: {
            type: String,
            default: '#FCA001'
        },
        fontSize: {
            type: Number,
            default: 50
        }
    },
    data () {
        return {
            timeLeft: this.setTimer,
            dashLen: (100 - this.border / 2) * Math.PI * 2
        }
    },
    computed: {
        countDown () {
            let time = this.timeLeft
            if (time <= 0) {
                return '0'
            } else {
                return time
            }
        },
        dashOffset () {
            return this.dashLen - this.timeLeft / this.setTimer * this.dashLen
        }
    },
    mounted () {
        this.lastDate = Date.now()
        this.interval = setInterval(() => {
            let curDate = Date.now()
            let diff = 1
            this.timeLeft -= diff
            if (this.timeLeft <= 0) {
                clearInterval(this.interval);
                this.$emit('sendEnd');
            }
            if (diff >= 1000) {
                this.lastDate = curDate;
            }
        }, 1000)
    }
}
</script>

<style scoped>

</style>

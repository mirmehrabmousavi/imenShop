<template>
    <div class="showAllShare">
        <div class="showAllShareHome">
            <div class="showAllShareTop">
                <h4>اشتراک گذاری در شبکه های اجتماعی</h4>
                <i @click="btnClose">
                    <svg-icon :icon="'#cancel'"></svg-icon>
                </i>
            </div>
            <div class="showAllShareItems">
                <div class="showAllShareItem">
                    <div class="showAllShareItemName">
                        <h4>توییتر</h4>
                        <a :href="'https://twitter.com/intent/tweet?url=' + $page.addressSite + 'productID/' + slug" target="_blank" title="واتساپ" name="واتساپ">https://api.whatsapp.com/send/?phone&text={{ $page.addressSite }}productID/{{ slug }}</a>
                    </div>
                    <a :href="'https://twitter.com/intent/tweet?url=' + $page.addressSite + 'productID/' + slug" target="_blank" title="توییتر" name="توییتر">
                        <i>
                            <svg-icon :icon="'#twitter2'"></svg-icon>
                        </i>
                    </a>
                </div>
                <div class="showAllShareItem">
                    <div class="showAllShareItemName">
                        <h4>تلگرام</h4>
                        <a :href="'https://telegram.me/share/url?url=' + $page.addressSite + 'productID/' + slug" target="_blank" title="واتساپ" name="واتساپ">https://api.whatsapp.com/send/?phone&text={{ $page.addressSite }}productID/{{ slug }}</a>
                    </div>
                    <a :href="'https://telegram.me/share/url?url=' + $page.addressSite + 'productID/' + slug" target="_blank" title="تلگرام" name="تلگرام">
                        <i>
                            <svg-icon :icon="'#telegram2'"></svg-icon>
                        </i>
                    </a>
                </div>
                <div class="showAllShareItem">
                    <div class="showAllShareItemName">
                        <h4>فیسبوک</h4>
                        <a :href="'https://www.facebook.com/sharer/sharer.php?m2w&s=100&p[url]=' + $page.addressSite + 'productID/' + slug" target="_blank" title="واتساپ" name="واتساپ">https://api.whatsapp.com/send/?phone&text={{ $page.addressSite }}productID/{{ slug }}</a>
                    </div>
                    <a :href="'https://www.facebook.com/sharer/sharer.php?m2w&s=100&p[url]=' + $page.addressSite + 'productID/' + slug" target="_blank" title="فیسبوک" name="فیسبوک">
                        <i>
                            <svg-icon :icon="'#facebook2'"></svg-icon>
                        </i>
                    </a>
                </div>
                <div class="showAllShareItem">
                    <div class="showAllShareItemName">
                        <h4>واتساپ</h4>
                        <a :href="'https://api.whatsapp.com/send/?phone&text=' + $page.addressSite + 'productID/' + slug" target="_blank" title="واتساپ" name="واتساپ">https://api.whatsapp.com/send/?phone&text={{ $page.addressSite }}productID/{{ slug }}</a>
                    </div>
                    <a :href="'https://api.whatsapp.com/send/?phone&text=' + $page.addressSite + 'productID/' + slug" target="_blank" title="واتساپ" name="واتساپ">
                        <i>
                            <svg-icon :icon="'#whatsapp2'"></svg-icon>
                        </i>
                    </a>
                </div>
            </div>
            <div class="showAllShareTag">
                <i>
                    <svg-icon :icon="'#tag'"></svg-icon>
                </i>
                <h4>{{$page.addressSite}}productID/{{slug}}</h4>
                <i @click.stop.prevent="copyTestingCode">
                    <svg-icon class="copy" v-if="copied == 1" :icon="'#edit'"></svg-icon>
                    <svg-icon v-else :icon="'#edit'"></svg-icon>
                </i>
                <input type="hidden" id="testing-code" :value="$page.addressSite+'productID/'+slug">
            </div>
        </div>
    </div>
</template>

<script>
import SvgIcon from "../../Pages/Svg/SvgIcon";
export default {
  components: { SvgIcon },
    name:'AllShare',
    props:['slug'],
    data(){
      return{
          notificationSystem: {
              options: {
                  show: {
                      icon: "icon-person",
                      position: "topCenter",
                  },
                  success: {
                      position: "bottomRight"
                  },
                  error: {
                      theme: "#FCA001",
                      progressBarColor: "#DC0808",
                      position: "bottomRight"
                  },
              }
          },
          copied: 0,
      }
    },
    methods:{
        btnClose(){
            this.$emit('sendClose');
        },
        copyTestingCode () {
            let testingCodeToCopy = document.querySelector('#testing-code')
            testingCodeToCopy.setAttribute('type', 'text')
            testingCodeToCopy.select()
            try {
                var successful = document.execCommand('copy');
                this.copied = 1;
                this.$toast.success('انجام شد', 'آدرس کپی شد', this.notificationSystem.options.success);
            } catch (err) {
                this.$toast.error('انجام نشد', 'مشکلی در کپی کردن پیش آمد', this.notificationSystem.options.success);
            }

            /* unselect the range */
            testingCodeToCopy.setAttribute('type', 'hidden')
            window.getSelection().removeAllRanges()
        },
    }
}
</script>

<style>

</style>

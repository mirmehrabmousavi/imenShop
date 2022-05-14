  <template>
      <div class="allTaxes">
          <div class="taxShow" @click="showTax= !showTax">
                <h4 v-if="allTaxes.length == 0">{{taxRoute}} را وارد کنید</h4>
                <ul v-else>
                    <li v-for="(item , index) in allTaxes">
                        <i @click.stop="btnClose(index)">
                            <svg-icon :icon="'#cancel'"></svg-icon>
                        </i>
                        <span>{{item}}</span>
                    </li>
                </ul>
                <svg-icon :icon="'#down'"></svg-icon>
          </div>
          <ul class="showAllTaxes" v-if="showTax">
              <li class="searchTax" v-if="taxRoute != 'رنگ' && add != 1">
                  <input v-model="search" type="text" :placeholder="'جستجو' + ' ' +taxRoute " @keyup="btnSearch">
                  <button @click.stop="addTax">+</button>
              </li>
              <VuePerfectScrollbar class="scroll-area">
                  <li v-for="(item , index) in allTax" @click.stop="sendTax(item , index)" :key="index">
                      {{item}}
                  </li>
              </VuePerfectScrollbar>
          </ul>
      </div>
</template>

<script>
    import SvgIcon from "../../../../../../bazar/bazar/resources/js/Pages/Svg/SvgIcon";
    import VuePerfectScrollbar from "vue-perfect-scrollbar";
    export default {
        name: "PostTaxonami",
        props:['taxes','tax','taxRoute','add'],
        components:{
            SvgIcon,
            VuePerfectScrollbar,
        },
        data(){
            return{
                showTax : false,
                allTaxes : [],
                taxId : [],
                search : '',
                i : 0,
                settings: {
                    maxScrollbarLength: 60
                },
                allTax : this.taxes,
            }
        },
        methods:{
            getTax(){
                if(this.tax[0] == '0' || this.tax.length == 0){
                    this.allTaxes = [];
                }else{
                    for ( this.i ; this.i < this.tax.length; this.i++) {
                        this.allTaxes.push(this.tax[this.i].name);
                        this.taxId.push(this.tax[this.i].id);
                        this.$emit('sendTax' , this.taxId);
                    }
                }
            },
            addTax(){
                const url = `/admin/tax/create`;
                axios.post(url ,{
                    tax : this.search,
                    taxRoute: this.taxRoute
                })
                    .then(response =>{
                        if (response.data === 'exist'){
                            this.$swal.fire(
                                'وجود دارد',
                                'تاکسونامی وجود دارد',
                                'warning'
                            );
                            this.search = ''
                        }else{
                            this.$swal.fire(
                                'با موفقیت انجام شد',
                                'تاکسونامی با موفقیت اضافه شد',
                                'success'
                            );
                            this.search = '';
                            this.allTaxes.push(response.data.name);
                            this.taxId.push(response.data.id);
                            this.$emit('sendTax' , this.taxId);
                            this.allTax = this.taxes;
                            this.showTax = !this.showTax;
                        }
                    })
            },
            sendTax(tax , index){
                this.i = 0;
                for ( this.i ; this.i <  this.allTaxes.length; this.i++) {
                    if (this.allTaxes[this.i] == tax){
                        this.i = 100;
                    }
                }
                if (this.i != 101){
                    this.allTaxes.push(tax);
                    this.taxId.push(index);
                    this.$emit('sendTax' , this.taxId);
                }
                this.i = 0;
            },
            btnClose(index){
                this.allTaxes.splice(index , 1);
                this.taxId.splice(index , 1);
                this.$emit('sendTax' , this.taxId);
            },
            btnSearch(){
                const url = '/admin/search-tax';
                axios.post(url ,{
                    search: this.search,
                    taxRoute: this.taxRoute
                })
                    .then(response=>{
                        if (this.search == ''){
                            this.allTax = this.taxes
                        }else{
                            this.allTax = response.data;
                        }
                    })
            },
            handleAddedTodo2(item){
                this.allTaxes = [];
                this.taxId = [];
            },
            handleAddedTodo(item){
                this.i =0;
                if(item.length){
                    for ( this.i ; this.i < item.length; this.i++) {
                        this.allTaxes.push(item[this.i].name);
                        this.taxId.push(item[this.i].id);
                        this.$emit('sendTax' , this.taxId);
                    }
                }
            }
        },
        created: function() {
            this.$eventHub.on('deleteTax', this.handleAddedTodo2);
            this.$eventHub.on('getCats', this.handleAddedTodo);
        },
        mounted() {
            this.getTax();
        }
    }
</script>

<style scoped>

</style>

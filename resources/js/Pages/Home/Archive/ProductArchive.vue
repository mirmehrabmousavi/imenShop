<template>
    <home-layout>
        <div class="allProductArchive width">
            <div class="allProductArchiveFilters">
                <div class="btnFilter" @click="btnShowFilters">
                    {{$t('showFilter')}}
                    <i>
                        <svg-icon :icon="'#filter'"></svg-icon>
                    </i>
                </div>
                <div class="filterContainer" :style="styleHeader" :class="{ 'navbar--hidden': !showNavbar }">
                    <div class="AllArchiveDataFiltersShowcase" v-if="showcase.length">
                        <div class="AllArchiveDataFiltersShowcaseTitle">
                            {{$t('showcase')}}
                        </div>
                        <hooper :settings="hooperSettings">
                            <slide v-for="(item,index) in showcase" :key="index">
                                <inertia-link :href="'/product/'+ item.slug"  class="AllArchiveDataFiltersShowcaseItem">
                                    <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                    <h3 v-if="$i18n.locale == 'fa'">{{item.title}}</h3>
                                    <h4 v-if="$i18n.locale == 'fa'">
                                        {{item.price|NumFormat}}
                                        <span>تومان</span>
                                    </h4>
                                    <h3 v-if="$i18n.locale == 'en'" class="en">{{item.titleEn}}</h3>
                                    <h4 v-if="$i18n.locale == 'en'" class="en">
                                        {{item.price|NumFormat}}
                                        <span>toman</span>
                                    </h4>
                                </inertia-link>
                            </slide>
                            <hooper-pagination slot="hooper-addons"></hooper-pagination>
                        </hooper>
                    </div>
                    <div class="allProductArchiveFiltersItems" v-if="off.length">
                        <div class="allProductArchiveFiltersItemsTitle" @click="showOff = !showOff">
                            {{$t('Discounts')}}
                            <i v-if="showOff">
                                <svg-icon :icon="'#up'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainerSearch" v-if="showOff">
                                <label>
                                    <i>
                                        <svg-icon :icon="'#search'"></svg-icon>
                                    </i>
                                    <input type="text" :placeholder="$t('search')" v-model="searchOff">
                                </label>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainer" v-if="showOff">
                                <VuePerfectScrollbar class="scroll-area">
                                    <div class="allProductArchiveFiltersItem" v-for="item in filteredListOff">
                                        <label :for="item" v-if="$i18n.locale == 'fa'">
                                            <input :id="item" type="checkbox" @change="sendOff(item)">
                                            {{item}}
                                        </label>
                                        <label v-if="$i18n.locale == 'en'" class="allProductArchiveFiltersItem en" :for="item">
                                            <input :id="item" type="checkbox" @change="sendOff(item)">
                                            {{item}}
                                        </label>
                                    </div>
                                </VuePerfectScrollbar>
                            </div>
                        </transition>
                    </div>
                    <div class="allProductArchiveFiltersSearch">
                        <div class="allProductArchiveFiltersSearchTitle">
                            {{$t('searchProduct')}}
                        </div>
                        <div class="allProductArchiveFiltersSearchItem">
                            <label>
                                <input type="text" :placeholder="$t('productName')" v-model="search" @keypress.enter="btnSearch">
                            </label>
                        </div>
                    </div>
                    <div class="allProductArchiveFiltersItems" v-if="brands.length">
                        <div class="allProductArchiveFiltersItemsTitle" @click="showBrand = !showBrand">
                            {{$t('brand')}}
                            <i v-if="showBrand">
                                <svg-icon :icon="'#up'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainerSearch" v-if="showBrand">
                                <label>
                                    <i>
                                        <svg-icon :icon="'#search'"></svg-icon>
                                    </i>
                                    <input type="text" :placeholder="$t('productName')" v-model="searchBrand">
                                </label>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainer" v-if="showBrand">
                                <VuePerfectScrollbar class="scroll-area">
                                    <div class="allProductArchiveFiltersItem" v-for="item in filteredListBrand">
                                        <label :for="item.name" v-if="$i18n.locale == 'fa'">
                                            <input :id="item.name" type="checkbox" @change="sendBrand(item.name)">
                                            {{item.name}}
                                        </label>
                                        <label :for="item.name" v-if="$i18n.locale == 'en'">
                                            <input :id="item.name" type="checkbox" @change="sendBrand(item.name)">
                                            {{item.nameEn}}
                                        </label>
                                    </div>
                                </VuePerfectScrollbar>
                            </div>
                        </transition>
                    </div>
                    <div class="allProductArchiveFiltersPrice">
                        <div class="allProductArchiveFiltersPriceTitle" @click="showPrice = !showPrice">
                            {{$t('priceRange')}}
                            <i v-if="showPrice">
                                <svg-icon :icon="'#up'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersPriceItem" v-if="showPrice">
                                <VueSimpleRangeSlider
                                    :max="maxPrice"
                                    :logarithmic="true"
                                    v-model="rangePrice"
                                />
                                <input v-model="rangePrice" hidden>
                                <div class="allProductArchiveFiltersPriceItemNumbers" v-if="$i18n.locale == 'fa'">
                                    <div class="allProductArchiveFiltersPriceItemNumber">
                                        <span>{{$t('from')}}</span>
                                        <h5>{{showMinPrice|NumFormat}}</h5>
                                        <span>تومان</span>
                                    </div>
                                    <div class="allProductArchiveFiltersPriceItemNumber">
                                        <span>{{$t('until')}}</span>
                                        <h5>{{showMaxPrice|NumFormat}}</h5>
                                        <span>تومان</span>
                                    </div>
                                </div>
                                <div class="allProductArchiveFiltersPriceItemNumbers en" v-if="$i18n.locale == 'en'">
                                    <div class="allProductArchiveFiltersPriceItemNumber">
                                        <span>{{$t('from')}}</span>
                                        <h5>{{showMinPrice|NumFormat}}</h5>
                                        <span>toman</span>
                                    </div>
                                    <div class="allProductArchiveFiltersPriceItemNumber">
                                        <span>{{$t('until')}}</span>
                                        <h5>{{showMaxPrice|NumFormat}}</h5>
                                        <span>toman</span>
                                    </div>
                                </div>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div class="button" v-if="showPrice">
                                <button @click="btnPrice">{{$t('applyRestrictions')}}</button>
                            </div>
                        </transition>
                    </div>
                    <div class="allProductArchiveFiltersItems" v-if="color.length">
                        <div class="allProductArchiveFiltersItemsTitle" @click="showColor = !showColor">
                            {{$t('colors')}}
                            <i v-if="showColor">
                                <svg-icon :icon="'#up'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainerSearch" v-if="showColor">
                                <label>
                                    <i>
                                        <svg-icon :icon="'#search'"></svg-icon>
                                    </i>
                                    <input type="text" :placeholder="$t('search')" v-model="searchColor">
                                </label>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainer" v-if="showColor">
                                <VuePerfectScrollbar class="scroll-area">
                                    <div class="allProductArchiveFiltersItem" v-for="(item,index) in filteredListColor">
                                        <label :for="index">
                                            <input :id="index" type="checkbox" @change="sendColor(item)">
                                            {{item}}
                                        </label>
                                    </div>
                                </VuePerfectScrollbar>
                            </div>
                        </transition>
                    </div>
                    <div class="allProductArchiveFiltersCheck">
                        <label for="count">
                            <input id="count" type="checkbox" class="switch" v-model="count" @change="btnCount">
                            {{$t('available')}}
                        </label>
                    </div>
                    <div class="allProductArchiveFiltersCheck">
                        <label for="suggest">
                            <input id="suggest" type="checkbox" class="switch" v-model="suggest" @change="btnSuggest">
                            {{$t('onlyOffered')}}
                        </label>
                    </div>
                    <div class="allProductArchiveFiltersItems" v-if="size.length">
                        <div class="allProductArchiveFiltersItemsTitle" @click="showSize = !showSize">
                            {{$t('sizes')}}
                            <i v-if="showSize">
                                <svg-icon :icon="'#up'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainerSearch" v-if="showSize">
                                <label>
                                    <i>
                                        <svg-icon :icon="'#search'"></svg-icon>
                                    </i>
                                    <input type="text" :placeholder="$t('search')" v-model="searchSize">
                                </label>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainer" v-if="showSize">
                                <VuePerfectScrollbar class="scroll-area">
                                    <div class="allProductArchiveFiltersItem" v-for="(item,index) in filteredListSize">
                                        <label :for="index">
                                            <input :id="index" type="checkbox" @change="sendSize(item)">
                                            {{item}}
                                        </label>
                                    </div>
                                </VuePerfectScrollbar>
                            </div>
                        </transition>
                    </div>
                    <div class="allProductArchiveFiltersItems" v-if="ability.length">
                        <div class="allProductArchiveFiltersItemsTitle" @click="showAbility = !showAbility">
                            {{$t('productfeatures')}}
                            <i v-if="showAbility">
                                <svg-icon :icon="'#up'"></svg-icon>
                            </i>
                            <i v-else>
                                <svg-icon :icon="'#down'"></svg-icon>
                            </i>
                        </div>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainerSearch" v-if="showAbility">
                                <label>
                                    <i>
                                        <svg-icon :icon="'#search'"></svg-icon>
                                    </i>
                                    <input type="text" :placeholder="$t('search')" v-model="searchAbility">
                                </label>
                            </div>
                        </transition>
                        <transition name="slide-fade">
                            <div class="allProductArchiveFiltersItemContainer" v-if="showAbility">
                                <VuePerfectScrollbar class="scroll-area">
                                    <div class="allProductArchiveFiltersItem" v-for="(item,index) in filteredListAbility">
                                        <label :for="index">
                                            <input :id="index" type="checkbox" @change="sendAbility(item.name)">
                                            {{item.name}}
                                        </label>
                                    </div>
                                </VuePerfectScrollbar>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
            <div class="allProductArchiveContainer">
                <div class="allProductArchiveContainerAddress">
                    <div class="allProductArchiveContainerAddressItem" v-if="$i18n.locale == 'fa'">
                        <inertia-link href="/">{{$t('home')}}</inertia-link>
                        <inertia-link v-if="cats.length" v-for="item in cats.slice(0,2)" :key="item.id" :href="'/archive/' + url+  item.slug">
                            /  {{item.name}}
                            <inertia-link v-if="item.cats.length" v-for="items in item.cats.slice(0,2)" :key="items.id" :href="'/archive/' + url +  items.slug">
                                / {{items.name}}
                                <inertia-link v-if="items.cats.length" v-for="element in items.cats.slice(0,2)" :key="element.id" :href="'/archive/' + url +  element.slug">
                                    / {{element.name}}
                                </inertia-link>
                            </inertia-link>
                        </inertia-link>
                    </div>
                    <div class="allProductArchiveContainerAddressItem" v-if="$i18n.locale == 'en'">
                        <inertia-link href="/">{{$t('home')}}</inertia-link>
                        <inertia-link v-if="cats.length" v-for="item in cats.slice(0,2)" :key="item.id" :href="'/archive/' + url + item.slug">
                            /  {{item.nameEn}}
                            <inertia-link v-if="item.cats.length" v-for="items in item.cats.slice(0,2)" :key="items.id" :href="'/archive/' + url + items.slug">
                                / {{items.nameEn}}
                                <inertia-link v-if="items.cats.length" v-for="element in items.cats.slice(0,2)" :key="element.id" :href="'/archive/' + url +  element.slug">
                                    / {{element.nameEn}}
                                </inertia-link>
                            </inertia-link>
                        </inertia-link>
                    </div>
                    <div class="allProductArchiveContainerAddressCount" v-if="$i18n.locale == 'fa'">
                        {{catPost.total}}
                        {{$t('product')}}
                    </div>
                    <div class="allProductArchiveContainerAddressCount en" v-if="$i18n.locale == 'en'"> {{catPost.total}}{{$t('product')}} </div>
                </div>
                <div class="allProductArchiveTaxes" v-if="catsTop.length">
                    <div class="allProductArchiveTaxesTitle">{{$t('category')}}</div>
                    <div class="allArchiveTaxItems">
                        <div class="allArchiveTaxItem" v-for="item in catsTop">
                            <inertia-link class="allArchiveTax" :href="'/archive/category/' + item.slug">
                                <div class="allArchiveTaxPic">
                                    <img v-for="value in item.post" :src="JSON.parse(value.image)[0]" :alt="value.name">
                                </div>
                                <h3 v-if="$i18n.locale == 'fa'">{{item.name}}</h3>
                                <h3 class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</h3>
                            </inertia-link>
                        </div>
                    </div>
                </div>
                <div class="allProductArchiveTaxes" v-if="brandTop.length">
                    <div class="allProductArchiveTaxesTitle">{{$t('brand')}}</div>
                    <div class="allArchiveTaxItems">
                        <div class="allArchiveTaxItem" v-for="item in brandTop">
                            <inertia-link class="allArchiveTax" :href="'/archive/brand/' + item.slug">
                                <div class="allArchiveTaxPic">
                                    <img v-for="value in item.post" :src="JSON.parse(value.image)[0]" :alt="value.name">
                                </div>
                                <h3 v-if="$i18n.locale == 'fa'">{{item.name}}</h3>
                                <h3 class="en" v-if="$i18n.locale == 'en'">{{item.nameEn}}</h3>
                            </inertia-link>
                        </div>
                    </div>
                </div>
                <div class="paginate" v-if="catPost.links">
                    <paginate-panel :link="catPost.links" :urls="'allColor='+this.allColor+'&search='+this.search+'&allOff='+this.allOff+'&allSize='+allSize+'&count='+count+'&suggest='+suggest+'&allAbility='+allAbility+'&min='+rangePrice[0]+'&max='+rangePrice[1]"></paginate-panel>
                </div>
                <div class="allProductArchiveContainerItems">
                    <div class="allProductArchiveContainerItemsFilter">
                        <i>
                            <svg-icon :icon="'#filter'"></svg-icon>
                        </i>
                        <span>{{$t('order')}} : </span>
                        <ul>
                            <li @click="btnShowSort(0)">
                                <span class="active" v-if="show == 0">{{$t('newest')}}</span>
                                <span class="unActive" v-else>{{$t('newest')}}</span>
                            </li>
                            <li @click="btnShowSort(1)">
                                <span class="active" v-if="show == 1">{{$t('mostVisited')}}</span>
                                <span class="unActive" v-else>{{$t('mostVisited')}}</span>
                            </li>
                            <li @click="btnShowSort(2)">
                                <span class="active" v-if="show == 2">{{$t('bestSelling')}}</span>
                                <span class="unActive" v-else>{{$t('bestSelling')}}</span>
                            </li>
                            <li @click="btnShowSort(3)">
                                <span class="active" v-if="show == 3">{{$t('mostPopular')}}</span>
                                <span class="unActive" v-else>{{$t('mostPopular')}}</span>
                            </li>
                            <li @click="btnShowSort(4)">
                                <span class="active" v-if="show == 4">{{$t('cheapest')}}</span>
                                <span class="unActive" v-else>{{$t('cheapest')}}</span>
                            </li>
                            <li @click="btnShowSort(5)">
                                <span class="active" v-if="show == 5">{{$t('expensive')}}</span>
                                <span class="unActive" v-else>{{$t('expensive')}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="allProductArchiveContainerPosts">
                        <div class="allProductArchiveContainerPost" v-for="(item,index) in catPost.data">
                            <inertia-link v-if="item.type == 0" :href="'/product/' + item.slug">
                                <div class="offProduct" v-if="item.off != null & $i18n.locale == 'fa'">
                                    <span>
                                        ٪
                                        <br>
                                        {{item.off}}
                                    </span>
                                </div>
                                <div class="offProduct en" v-if="item.off != null & $i18n.locale == 'en'">
                                    <span>
                                        %
                                        <br>
                                        {{item.off}}
                                    </span>
                                </div>
                                <div class="allProductArchiveContainerPostPic">
                                    <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                </div>
                                <div class="allProductArchiveContainerPostTitle">
                                    <h4 v-if="$i18n.locale == 'fa'">{{item.title}}</h4>
                                    <h4 v-if="$i18n.locale == 'en'">{{item.titleEn}}</h4>
                                </div>
                                <ul v-if="item.review[0].ability != ''">
                                    <li v-for="value in JSON.parse(item.review[0].ability).slice(0 , 3)">
                                        <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                                        <span class="en" v-if="$i18n.locale == 'en'">{{value.nameEn}}</span>
                                    </li>
                                </ul>
                                <div class="postPrice" v-if="item.count >= 1">
                                    <div class="postPriceItem" v-if="item.type == 0" @click.prevent="addCart(item.id , index)">
                                        <i v-if="loadingAdd == index">
                                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                        </i>
                                        <i v-if="loadingAdd != index">
                                            <svg-icon :icon="'#plus'"></svg-icon>
                                        </i>
                                    </div>
                                    <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                        <div class="offPrice" v-if="item.off != null">
                                            <s>{{item.offPrice|NumFormat}} تومان</s>
                                        </div>
                                        <h3>
                                            {{item.price|NumFormat}}
                                            <span>تومان</span>
                                        </h3>
                                    </div>
                                    <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                        <div class="offPrice" v-if="item.off != null">
                                            <s>{{item.offPrice|NumFormat}} toman</s>
                                        </div>
                                        <h3>
                                            {{item.price|NumFormat}}
                                            <span>toman</span>
                                        </h3>
                                    </div>
                                </div>
                                <div class="checkCount" v-else>
                                    <span>{{$t('notAvailable')}}</span>
                                </div>
                            </inertia-link>
                            <inertia-link v-if="item.type == 1" :href="'/download-product/' + item.slug">
                                <div class="offProduct" v-if="item.off != null & $i18n.locale == 'fa'">
                                    <span>
                                        ٪
                                        <br>
                                        {{item.off}}
                                    </span>
                                </div>
                                <div class="offProduct en" v-if="item.off != null & $i18n.locale == 'en'">
                                    <span>
                                        %
                                        <br>
                                        {{item.off}}
                                    </span>
                                </div>
                                <div class="allProductArchiveContainerPostPic">
                                    <img :src="JSON.parse(item.image)[0]" :alt="item.title">
                                </div>
                                <div class="allProductArchiveContainerPostTitle">
                                    <h4 v-if="$i18n.locale == 'fa'">{{item.title}}</h4>
                                    <h4 v-if="$i18n.locale == 'en'">{{item.titleEn}}</h4>
                                </div>
                                <ul v-if="item.review[0].ability != ''">
                                    <li v-for="value in JSON.parse(item.review[0].ability).slice(0 , 3)">
                                        <span v-if="$i18n.locale == 'fa'">{{value.name}}</span>
                                        <span class="en" v-if="$i18n.locale == 'en'">{{value.nameEn}}</span>
                                    </li>
                                </ul>
                                <div class="postPrice" v-if="item.count >= 1">
                                    <div class="postPriceItem" v-if="item.type == 0" @click.prevent="addCart(item.id , index)">
                                        <i v-if="loadingAdd == index">
                                            <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                        </i>
                                        <i v-if="loadingAdd != index">
                                            <svg-icon :icon="'#plus'"></svg-icon>
                                        </i>
                                    </div>
                                    <div class="postPriceItem" v-if="$i18n.locale == 'fa'">
                                        <div class="offPrice" v-if="item.off != null">
                                            <s>{{item.offPrice|NumFormat}} تومان</s>
                                        </div>
                                        <h3>
                                            {{item.price|NumFormat}}
                                            <span>تومان</span>
                                        </h3>
                                    </div>
                                    <div class="postPriceItem en" v-if="$i18n.locale == 'en'">
                                        <div class="offPrice" v-if="item.off != null">
                                            <s>{{item.offPrice|NumFormat}} toman</s>
                                        </div>
                                        <h3>
                                            {{item.price|NumFormat}}
                                            <span>toman</span>
                                        </h3>
                                    </div>
                                </div>
                                <div class="checkCount" v-else>
                                    <span>{{$t('notAvailable')}}</span>
                                </div>
                            </inertia-link>
                            <div class="productOptions">
                                <div class="productOption" title="علاقه مندی" @click.prevent="btnLike(item.id,index)">
                                    <i v-if="loadingLike == index">
                                        <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                    </i>
                                    <i v-for="values in like" v-if="values == item.id && loadingLike != index">
                                        <svg-icon :icon="'#like'"></svg-icon>
                                    </i>
                                    <i>
                                        <svg-icon :icon="'#unlike'"></svg-icon>
                                    </i>
                                </div>
                                <div class="productOption" title="نشانه گذاری" @click.prevent="btnBookmark(item.id)">
                                    <i v-if="loadingBookmark == index">
                                        <svg-icon class="loading" :icon="'#loading'"></svg-icon>
                                    </i>
                                    <i v-for="values in bookmark" v-if="values == item.id && loadingBookmark != index">
                                        <svg-icon :icon="'#bookmark'"></svg-icon>
                                    </i>
                                    <i>
                                        <svg-icon :icon="'#unbookmark'"></svg-icon>
                                    </i>
                                </div>
                                <div v-if="item.type == 0" class="productOption" title="مقایسه کردن" @click.prevent="btnComparison(item.id)">
                                    <i class="active" v-for="values in allComparison" v-if="values == item.id">
                                        <svg-icon :icon="'#chart'"></svg-icon>
                                    </i>
                                    <i>
                                        <svg-icon :icon="'#chart'"></svg-icon>
                                    </i>
                                </div>
                                <div v-if="item.type == 0" class="productOption" title="مشاهده سریع" @click.prevent="btnShowFast(item.id)">
                                    <i>
                                        <svg-icon :icon="'#search'"></svg-icon>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="paginate" v-if="catPost.links">
                    <paginate-panel :link="catPost.links"></paginate-panel>
                </div>
                <div class="allProductArchiveContainerCats" v-if="cats[0].cats != null && cats[0].cats != ''">
                    <div class="allProductArchiveContainerCatsItem" v-for="item in cats[0].cats.slice(0 , 3)" v-if="cats[0].cats[0].cats != null && cats[0].cats[0].cats != '' && item.image">
                        <inertia-link :href="'/archive/' + url + item.slug" v-if="$i18n.locale == 'fa'" :title="item.name">
                            <img :src="item.image" :alt="item.name">
                        </inertia-link>
                        <inertia-link :href="'/archive/' + url + item.slug" v-if="$i18n.locale == 'en'" :title="item.nameEn">
                            <img :src="item.image" :alt="item.nameEn">
                        </inertia-link>
                    </div>
                </div>
                <div class="allProductArchiveContainerCats" v-if="cats[0].cats != null && cats[0].cats != ''">
                    <div class="allProductArchiveContainerCatsItem" v-for="(item , index) in cats[0].cats[0].cats.slice(0 , 2)" v-if="cats[0].cats[0].cats != null && cats[0].cats[0].cats != '' && item.image">
                        <inertia-link :href="'/archive/' + url + item.slug" v-if="$i18n.locale == 'fa'" :title="item.name">
                            <img :src="item.image" :alt="item.name">
                        </inertia-link>
                        <inertia-link :href="'/archive/' + url + item.slug" v-if="$i18n.locale == 'en'" :title="item.nameEn">
                            <img :src="item.image" :alt="item.nameEn">
                        </inertia-link>
                    </div>
                </div>
            </div>
        </div>
        <show-fast></show-fast>
        <all-compare></all-compare>
    </home-layout>
</template>

<script>
import 'hooper/dist/hooper.css';
import HomeLayout from "../../../components/layout/HomeLayout";
import SvgIcon from "../../Svg/SvgIcon";
import PaginatePanel from "../../Admin/PaginatePanel";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import VueSimpleRangeSlider from "vue-simple-range-slider";
import 'vue-simple-range-slider/dist/vueSimpleRangeSlider.css'
import {Hooper, Navigation as HooperNavigation, Slide, Pagination as HooperPagination,} from "hooper";
import ShowFast from "../ShowFast";
import AllCompare from "../AllCompare";
export default {
    name: "ProductArchive",
    props:['color','size','off','url','search1','catsTop','showcase','brandTop','cats','title','catPost','ability','brands','maxPrice','minPrice'],
    metaInfo() {
        return {
            title: `${this.cats[0].name} - ${this.title}`,
            htmlAttrs: {
                lang: 'fa',
                amp: true,
                reptilian: 'gator'
            },
            headAttrs: {
                nest: 'eggs'
            },
            meta: [
                { charset: 'utf-8' },
            ]
        }
    },
    components:{
        AllCompare,
        ShowFast,
        HomeLayout,
        VueSimpleRangeSlider,
        HooperPagination,
        PaginatePanel,
        Hooper,
        HooperNavigation,
        VuePerfectScrollbar,
        Slide,
        SvgIcon,
    },
    data(){
        return{
            showOff: true,
            showColor: false,
            showBrand: true,
            showSize: false,
            showPrice: false,
            showAbility: false,
            showNavbar: true,
            rangePrice: [this.minPrice,this.maxPrice],
            showMinPrice: this.minPrice,
            showMaxPrice: this.maxPrice,
            loadingAdd: -1,
            allColor: [],
            allBrands: [],
            allSize: [],
            allAbility: [],
            allOff: [],
            search: this.search1,
            like: [],
            postCompares: [],
            allComparison: [],
            bookmark: [],
            i: 0,
            showFilters: 1,
            count: 0,
            show: 0,
            suggest: 0,
            searchOff: '',
            searchBrand: '',
            searchColor: '',
            searchSize: '',
            searchAbility: '',
            loadingBookmark : -1,
            loadingLike : -1,
            hooperSettings: {
                wheelControl:false,
                centerMode: false,
                hoverPause: false,
                rtl: true,
                transition: 300,
                itemsToShow: 1,
                autoPlay:true,
                playSpeed : 5000,
                mouseDrag:false,
                touchDrag:false,
            },
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
                        position: "bottomRight"
                    },
                }
            },
            styleHeader : {
                opacity: '1',
                visibility: 'visible',
                position: 'relative',
            },
        }
    },
    methods:{
        onScroll () {
            if (window.innerWidth <= 700) {
                this.showFilters = 0;
                this.styleHeader = {
                    opacity: '0',
                    visibility: 'hidden',
                    position: 'absolute',
                };
            }
        },
        btnShowFilters(){
            if(this.showFilters == 1){
                this.showFilters = 0;
                this.styleHeader = {
                    opacity: '0',
                    visibility: 'hidden',
                    position: 'absolute',
                }
            }else{
                this.showFilters = 1;
                this.styleHeader = {
                    opacity: '1',
                    visibility: 'visible',
                    position: 'relative',
                }
            }
        },
        btnComparison(id){
            this.i = 0;
            if (this.allComparison.length <= 7){
                for ( this.i ; this.i <  this.allComparison.length; this.i++) {
                    if (this.allComparison[this.i] == id){
                        this.allComparison.splice(this.i , 1);
                        this.i = 100;
                    }
                }
                if (this.i != 101){
                    this.allComparison.push(id);
                }
                this.i = 0;
            }
            this.$eventHub.emit('allComparisons' , this.allComparison);
        },
        btnShowSort(number){
            this.show = number;
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                show : this.show,
                allAbility : this.allAbility,
                search : this.search,
                suggest : this.suggest,
                count : this.count,
                allSize : this.allSize,
                allBrands : this.allBrands,
                rangePrice : this.rangePrice,
                allColor : this.allColor,
            })
        },
        btnShowFast(id){
            this.$eventHub.emit('showFast' , id);
        },
        btnSuggest(){
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                allAbility : this.allAbility,
                show : this.show,
                search : this.search,
                suggest : this.suggest,
                count : this.count,
                allSize : this.allSize,
                allBrands : this.allBrands,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                allColor : this.allColor,
            })
        },
        btnCount(){
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                allAbility : this.allAbility,
                show : this.show,
                search : this.search,
                suggest : this.suggest,
                count : this.count,
                allSize : this.allSize,
                allBrands : this.allBrands,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                allColor : this.allColor,
            })
        },
        btnPrice(){
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                allAbility : this.allAbility,
                show : this.show,
                search : this.search,
                suggest : this.suggest,
                count : this.count,
                allSize : this.allSize,
                allBrands : this.allBrands,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                allColor : this.allColor,
            })
        },
        sendOff(item){
            for ( this.i ; this.i <  this.allOff.length; this.i++) {
                if (this.allOff[this.i] == item){
                    this.allOff.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.allOff.push(item);
            }
            this.i = 0;
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                allBrands : this.allBrands,
                suggest : this.suggest,
                show : this.show,
                count : this.count,
                allAbility : this.allAbility,
                search : this.search,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                allSize : this.allSize,
                allColor : this.allColor,
            })
        },
        sendAbility(item){
            for ( this.i ; this.i <  this.allAbility.length; this.i++) {
                if (this.allAbility[this.i] == item){
                    this.allAbility.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.allAbility.push(item);
            }
            this.i = 0;
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                suggest : this.suggest,
                show : this.show,
                count : this.count,
                allBrands : this.allBrands,
                allAbility : this.allAbility,
                allColor : this.allColor,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                search : this.search,
                allSize : this.allSize,
            })
        },
        sendBrand(item){
            this.i = 0;
            for ( this.i ; this.i <  this.allBrands.length; this.i++) {
                if (this.allBrands[this.i] == item){
                    this.allBrands.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.allBrands.push(item);
            }
            this.i = 0;
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                allBrands : this.allBrands,
                suggest : this.suggest,
                count : this.count,
                show : this.show,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                search : this.search,
                allAbility : this.allAbility,
                allSize : this.allSize,
                allColor : this.allColor,
            })
        },
        sendSize(item){
            for ( this.i ; this.i <  this.allSize.length; this.i++) {
                if (this.allSize[this.i] == item){
                    this.allSize.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.allSize.push(item);
            }
            this.i = 0;
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allBrands : this.allBrands,
                allOff : this.allOff,
                search : this.search,
                show : this.show,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                allAbility : this.allAbility,
                suggest : this.suggest,
                count : this.count,
                allSize : this.allSize,
                allColor : this.allColor,
            })
        },
        btnSearch(){
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                search : this.search,
                allBrands : this.allBrands,
                show : this.show,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                allAbility : this.allAbility,
                suggest : this.suggest,
                count : this.count,
                allSize : this.allSize,
                allColor : this.allColor,
            })
        },
        sendColor(item){
            for ( this.i ; this.i <  this.allColor.length; this.i++) {
                if (this.allColor[this.i] == item){
                    this.allColor.splice(this.i , 1);
                    this.i = 100;
                }
            }
            if (this.i != 101){
                this.allColor.push(item);
            }
            this.i = 0;
            const url = `/archive/${this.url}${this.cats[0].slug}`;
            this.$inertia.post(url , {
                allOff : this.allOff,
                show : this.show,
                allBrands : this.allBrands,
                min : this.rangePrice[0],
                max : this.rangePrice[1],
                suggest : this.suggest,
                count : this.count,
                search : this.search,
                allAbility : this.allAbility,
                allSize : this.allSize,
                allColor : this.allColor,
            })
        },
        addCart(id , index){
            this.loadingAdd = index;
            const url = `/add-cart`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    if(response.data == 'limit'){
                        this.$toast.error('انجام نشد', 'موجودی کالا کافی نیست', this.notificationSystem.options.error);
                    }
                    if (response.data === 'no'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                    }else{
                        this.$eventHub.emit('getCart');
                        this.$toast.success('انجام شد', 'به سبد خرید با موفقیت اضافه شد', this.notificationSystem.options.success);
                    }
                    this.loadingAdd = -1;
                })
                .catch(err =>{
                    this.loadingAdd = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        btnLike(id,index){
            this.loadingLike = index;
            const url = `/like`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    this.loadingLike = -1;
                    if (response.data == 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.like = [];
                    }else{
                        if (response.data == 'delete'){
                            this.$toast.success('انجام شد', 'علاقه مندی با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.getLike();
                        }else{
                            this.$toast.success('انجام شد', 'به علاقه مندی با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.getLike();
                        }
                    }
                })
                .catch(err =>{
                    this.loadingLike = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        getLike(){
            const url = `/get-like`;
            axios.get(url)
                .then(response=>{
                    this.like = response.data;
                })
        },
        btnBookmark(id,index){
            this.loadingBookmark = index;
            const url = `/bookmark`;
            axios.post(url ,{
                postID : id
            })
                .then(response=>{
                    this.loadingBookmark = -1;
                    if (response.data === 'noUser'){
                        this.$toast.error('عضو نیستید', 'ابتدا عضو شوید', this.notificationSystem.options.error);
                        this.bookmark = [];
                    }else {
                        if (response.data === 'delete') {
                            this.$toast.success('انجام شد', 'نشانه با موفقیت حذف شد', this.notificationSystem.options.success);
                            this.getBookmark();
                        }else {
                            this.$toast.success('انجام شد', 'نشانه با موفقیت اضافه شد', this.notificationSystem.options.success);
                            this.bookmark.push(response.data.post_id);
                        }
                    }
                })
                .catch(err =>{
                    this.loadingBookmark = -1;
                    this.$toast.error('انجام نشد', 'متاسفانه مشکلی پیش آمد', this.notificationSystem.options.error);
                })
        },
        getBookmark(){
            const url = `/get-bookmark`;
            axios.get(url)
                .then(response=>{
                    this.bookmark = response.data;
                })
        },
        getCompares(item){
            this.allComparison = item;
        }
    },
    mounted() {
        this.getLike();
        this.getBookmark();
        this.onScroll();
    },
    computed: {
        filteredListOff() {
            return this.off.filter(post => {
                return post.toString().includes(this.searchOff.toString())
            })
        },
        filteredListBrand() {
            return this.brands.filter(post => {
                return post.name.toString().includes(this.searchBrand.toString())
            })
        },
        filteredListColor() {
            return this.color.filter(post => {
                return post.toString().includes(this.searchColor.toString())
            })
        },
        filteredListSize() {
            return this.size.filter(post => {
                return post.toString().includes(this.searchSize.toString())
            })
        },
        filteredListAbility() {
            return this.ability.filter(post => {
                return post.name.toString().includes(this.searchAbility.toString())
            })
        }
    },
    created: function() {
        this.$eventHub.on('allComparisons', this.getCompares);
    },
    watch: {
        'rangePrice': function(val, oldVal){
            this.showMinPrice = `${this.rangePrice[0]}`;
            this.showMaxPrice = `${this.rangePrice[1]}`;
        }
    }
}

</script>

<style scoped>

</style>

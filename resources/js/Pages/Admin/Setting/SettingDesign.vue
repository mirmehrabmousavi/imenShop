<template>
    <admin-layout>
        <div class="allTemplateSetting">
            <div class="allTemplateSettingTabs">
                <div class="allCreatePostItemTab" @click="tab = 0">
                    <button class="active" v-if="tab == 0">صفحه اصلی</button>
                    <button v-else>صفحه اصلی</button>
                </div>
                <div class="allCreatePostItemTab" @click="tab = 1">
                    <button class="active" v-if="tab == 1">صفحه سینگل</button>
                    <button v-else>صفحه سینگل</button>
                </div>
                <div class="allCreatePostItemTab" @click="tab = 2">
                    <button class="active" v-if="tab == 2">صفحه اپلیکیشن</button>
                    <button v-else>صفحه اپلیکیشن</button>
                </div>
            </div>
            <div class="allTemplateSettingContainers">
                <div class="allTemplateSettingContainer" v-if="tab == 0">
                    <div class="allTemplateSettingContainerLists">
                        <div class="allTemplateSettingList">
                            <div class="allTemplateSettingListTitle">
                                همه ویجت ها
                            </div>
                            <VuePerfectScrollbar class="scroll-area">
                                <draggable v-model="allVidget" class="drags" group="people" @start="drag=true" @change="btnSend" @end="drag = false">
                                    <div class="allCategorySettingContainerListItem" v-for="(item,index) in allVidget" :key="index">
                                        <div class="dragsItem" v-if="item.name == 'تبلیغات اسلایدری'">
                                            <img src="/img/adsHooper.png">
                                            <h3>تبلیغات اسلایدری</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'تبلیغات ساده'">
                                            <img src="/img/adIndex.png">
                                            <h3>تبلیغات ساده</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'پست های خارج پس زمینه'">
                                            <img src="/img/backProduct.png">
                                            <h3>پست های خارج پس زمینه</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'اسلایدر بزرگ تبلیغ'">
                                            <img src="/img/bigSlider.png">
                                            <h3>اسلایدر بزرگ تبلیغ</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'برند ویژه'">
                                            <img src="/img/brandIndex.png">
                                            <h3>برند ویژه</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'دسته بندی خاص'">
                                            <img src="/img/homeHooper.png">
                                            <h3>دسته بندی خاص</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'پست افقی'">
                                            <img src="/img/horizontalProduct.png">
                                            <h3>پست افقی</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'پست ویژه با تصویر'">
                                            <img src="/img/imageWidget.png">
                                            <h3>پست ویژه با تصویر</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'دسته بندی رنگی'">
                                            <img src="/img/professionalCategory.png">
                                            <h3>دسته بندی رنگی</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'جستجو'">
                                            <img src="/img/searchIndex.png">
                                            <h3>جستجو</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'محصولات دانلودی'">
                                            <img src="/img/downloadIndex.png">
                                            <h3>محصولات دانلودی</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'ویژگی ها'">
                                            <img src="/img/siteProperty.png">
                                            <h3>ویژگی ها</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'خبر ها'">
                                            <img src="/img/siteProperty.png">
                                            <h3>خبر ها</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'دسته بندی و پیشنهاد لحظه ای'">
                                            <img src="/img/specialIndex.png">
                                            <h3>دسته بندی و پیشنهاد لحظه ای</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'پیشنهاد شگفت انگیز'">
                                            <img src="/img/suggestContainer.png">
                                            <h3>پیشنهاد شگفت انگیز</h3>
                                        </div>
                                    </div>
                                </draggable>
                            </VuePerfectScrollbar>
                        </div>
                    </div>
                    <div class="allTemplateSettingList">
                        <div class="allTemplateSettingListTitle">
                            قالب وبسایت شما
                        </div>
                        <draggable class="allTemplateSettingListDrag" v-model="form.allSiteTemplate" group="people" @start="drag=true" @end="drag = false">
                            <div class="allCategorySettingContainerListItemChoice"  v-for="(item , allIndex) in form.allSiteTemplate" :key="allIndex">
                                <img v-if="item.name == 'تبلیغات اسلایدری'" src="/img/adsHooper.png">
                                <img v-if="item.name == 'تبلیغات ساده'" src="/img/adIndex.png">
                                <img v-if="item.name == 'پست های خارج پس زمینه'" src="/img/backProduct.png">
                                <img v-if="item.name == 'اسلایدر بزرگ تبلیغ'" src="/img/bigSlider.png">
                                <img v-if="item.name == 'برند ویژه'" src="/img/brandIndex.png">
                                <img v-if="item.name == 'دسته بندی خاص'" src="/img/homeHooper.png">
                                <img v-if="item.name == 'پست افقی'" src="/img/horizontalProduct.png">
                                <img v-if="item.name == 'پست ویژه با تصویر'" src="/img/imageWidget.png">
                                <img v-if="item.name == 'دسته بندی رنگی'" src="/img/professionalCategory.png">
                                <img v-if="item.name == 'جستجو'" src="/img/searchIndex.png">
                                <img v-if="item.name == 'محصولات دانلودی'" src="/img/downloadIndex.png">
                                <img v-if="item.name == 'ویژگی ها'" src="/img/siteProperty.png">
                                <img v-if="item.name == 'خبر ها'" src="/img/newsIndex.png">
                                <img v-if="item.name == 'دسته بندی و پیشنهاد لحظه ای'" src="/img/specialIndex.png">
                                <img v-if="item.name == 'پیشنهاد شگفت انگیز'" src="/img/suggestContainer.png">
                                <div class="allCategorySettingContainerListItemChoiceData">
                                    <h3>{{item.name}}</h3>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'پست ویژه با تصویر' && item.name != 'دسته بندی رنگی' && item.name != 'پیشنهاد شگفت انگیز' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>نمایش عنوان :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.title">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'پست ویژه با تصویر' && item.name != 'دسته بندی رنگی' && item.name != 'پیشنهاد شگفت انگیز' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>نمایش عنوان انگلیسی :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.titleEn">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'پست ویژه با تصویر' && item.name != 'پست های خارج پس زمینه' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>عنوان مشاهده بیشتر :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.more">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'پست های خارج پس زمینه' && item.name != 'پست ویژه با تصویر' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>عنوان مشاهده بیشتر انگلیسی :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.moreEn">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name == 'پست ویژه با تصویر' || item.name == 'پست های خارج پس زمینه'">
                                        <label>تصویر پس زمینه :</label>
                                        <input type="text" placeholder="آدرس را وارد کنید" v-model="item.background">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name == 'دسته بندی رنگی'">
                                        <label>رنگ پس زمینه :</label>
                                        <input type="text" placeholder="کد رنگ را وارد کنید" v-model="item.background">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'محصولات دانلودی' && item.name != 'خبر ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'پست ویژه با تصویر' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>پیوند یکتا (slug) :</label>
                                        <input type="text" placeholder="پیوند را وارد کنید" v-model="item.slug">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name == 'دسته بندی رنگی'">
                                        <label>آدرس تصویر :</label>
                                        <input type="text" placeholder="آدرس را وارد کنید" v-model="item.title">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات ساده' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'تبلیغات اسلایدری' && item.name != 'ویژگی ها'">
                                        <label>تعداد نمایش :</label>
                                        <input type="text" placeholder="تعداد را وارد کنید" v-model="item.count">
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'خبر ها' && item.name != 'محصولات دانلودی' && item.name != 'جستجو' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'تبلیغات ساده' && item.name != 'برند ویژه'">
                                        <label>دسته بندی :</label>
                                        <div class="allTaxes">
                                            <div class="taxShow" @click="btnShowTax(2,allIndex)">
                                                <h4 v-if="item.category.length == 0">دسته بندی را وارد کنید</h4>
                                                <ul v-else>
                                                    <li v-for="(item , index) in item.category" :key="index">
                                                        <i @click.stop="btnClose(index,allIndex,'category')">
                                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                                        </i>
                                                        <span>{{item}}</span>
                                                    </li>
                                                </ul>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </div>
                                            <ul class="showAllTaxes" v-if="showTax == 2 && showAllTax == allIndex">
                                                <VuePerfectScrollbar class="scroll-area">
                                                    <li v-for="(item , index) in category" @click.stop="sendTax(item.name,allIndex,'category')" :key="index">
                                                        {{item.name}}
                                                    </li>
                                                </VuePerfectScrollbar>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name == 'خبر ها'">
                                        <label>دسته بندی :</label>
                                        <div class="allTaxes">
                                            <div class="taxShow" @click="btnShowTax(2,allIndex)">
                                                <h4 v-if="item.category.length == 0">دسته بندی را وارد کنید</h4>
                                                <ul v-else>
                                                    <li v-for="(item , index) in item.category" :key="index">
                                                        <i @click.stop="btnClose(index,allIndex,'category')">
                                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                                        </i>
                                                        <span>{{item}}</span>
                                                    </li>
                                                </ul>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </div>
                                            <ul class="showAllTaxes" v-if="showTax == 2 && showAllTax == allIndex">
                                                <VuePerfectScrollbar class="scroll-area">
                                                    <li v-for="(item , index) in categoryNews" @click.stop="sendTax(item.name,allIndex,'category')" :key="index">
                                                        {{item.name}}
                                                    </li>
                                                </VuePerfectScrollbar>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'محصولات دانلودی' && item.name != 'خبر ها' && item.name != 'جستجو' && item.name != 'پیشنهاد شگفت انگیز' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'تبلیغات ساده'">
                                        <label>برند :</label>
                                        <div class="allTaxes">
                                            <div class="taxShow" @click="btnShowTax(1,allIndex)">
                                                <h4 v-if="item.brand.length == 0">برند را وارد کنید</h4>
                                                <ul v-else>
                                                    <li v-for="(item , index) in item.brand" :key="index">
                                                        <i @click.stop="btnClose(index,allIndex,'brand')">
                                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                                        </i>
                                                        <span>{{item}}</span>
                                                    </li>
                                                </ul>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </div>
                                            <ul class="showAllTaxes" v-if="showTax == 1 && showAllTax == allIndex">
                                                <VuePerfectScrollbar class="scroll-area">
                                                    <li v-for="(item , index) in brand" @click.stop="sendTax(item.name,allIndex,'brand')" :key="index">
                                                        {{item.name}}
                                                    </li>
                                                </VuePerfectScrollbar>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'محصولات دانلودی' && item.name != 'خبر ها' && item.name != 'جستجو' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'برند ویژه' && item.name != 'تبلیغات ساده'">
                                        <label>نمایش براساس :</label>
                                        <div class="allCategoryPanel" @click="btnShowTax(3,allIndex)">
                                            <div class="categoryShow">
                                                <h4 v-if="item.show == 0">جدید ترین</h4>
                                                <h4 v-if="item.show == 1">محبوب ترین</h4>
                                                <h4 v-if="item.show == 2">پربازدید ترین</h4>
                                                <h4 v-if="item.show == 3">ارزان ترین</h4>
                                                <h4 v-if="item.show == 4">گران ترین</h4>
                                                <h4 v-if="item.show == 5">پرفروش ترین</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showTax == 3 && showAllTax == allIndex">
                                                <li @click="item.show = 0">جدید ترین</li>
                                                <li @click="item.show = 1">محبوب ترین</li>
                                                <li @click="item.show = 2">پربازدید ترین</li>
                                                <li @click="item.show = 3">ارزان ترین</li>
                                                <li @click="item.show = 4">گران ترین</li>
                                                <li @click="item.show = 5">پرفروش ترین</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'محصولات دانلودی' && item.name != 'خبر ها' && item.name != 'جستجو' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'برند ویژه' && item.name != 'تبلیغات ساده'">
                                        <label>نوع کالا :</label>
                                        <div class="allCategoryPanel" @click="btnShowTax(4,allIndex)">
                                            <div class="categoryShow">
                                                <h4 v-if="item.type == 3">همه</h4>
                                                <h4 v-if="item.type == 0">فقط موجود</h4>
                                                <h4 v-if="item.type == 1">تخفیف خورده</h4>
                                                <h4 v-if="item.type == 2">پیشنهادی</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showTax == 4 && showAllTax == allIndex">
                                                <li @click="item.type = 3">همه</li>
                                                <li @click="item.type = 0">فقط موجود</li>
                                                <li @click="item.type = 1">تخفیف خورده</li>
                                                <li @click="item.type = 2">پیشنهادی</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="abilityPost" v-if="item.name == 'تبلیغات ساده' || item.name == 'اسلایدر بزرگ تبلیغ'">
                                        <table class="abilityTable">
                                            <tr>
                                                <th>تصویر تبلیغ</th>
                                                <th>آدرس تبلیغ</th>
                                                <th>
                                                    <i @click="addAbility(allIndex)">
                                                        <svg-icon :icon="'#add'"></svg-icon>
                                                    </i>
                                                </th>
                                            </tr>
                                            <tr v-for="(value, index2) in item.category" :key="index2">
                                                <td>
                                                    <input type="text" placeholder="تصویر را وارد کنید" v-model="value.image">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="آدرس را وارد کنید" v-model="value.address">
                                                </td>
                                                <td>
                                                    <i @click="deleteAbility(allIndex,index2)">
                                                        <svg-icon :icon="'#trash'"></svg-icon>
                                                    </i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="abilityPost" v-if="item.name == 'تبلیغات اسلایدری' || item.name == 'پست ویژه با تصویر'">
                                        <table class="abilityTable">
                                            <tr>
                                                <th>تصویر تبلیغ سمت راست</th>
                                                <th>آدرس تبلیغ سمت راست</th>
                                                <th>
                                                    <i @click="addAd2(allIndex)">
                                                        <svg-icon :icon="'#add'"></svg-icon>
                                                    </i>
                                                </th>
                                            </tr>
                                            <tr v-for="(value, index2) in item.slug" :key="index2">
                                                <td>
                                                    <input type="text" placeholder="تصویر را وارد کنید" v-model="value.image">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="آدرس را وارد کنید" v-model="value.address">
                                                </td>
                                                <td>
                                                    <i @click="deleteAd2(allIndex,index2)">
                                                        <svg-icon :icon="'#trash'"></svg-icon>
                                                    </i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="abilityPost" v-if="item.name == 'تبلیغات اسلایدری'">
                                        <table class="abilityTable">
                                            <tr>
                                                <th>تصویر تبلیغ سمت چپ</th>
                                                <th>آدرس تبلیغ سمت چپ</th>
                                                <th>
                                                    <i @click="addAd(allIndex)">
                                                        <svg-icon :icon="'#add'"></svg-icon>
                                                    </i>
                                                </th>
                                            </tr>
                                            <tr v-for="(value, index2) in item.view" :key="index2">
                                                <td>
                                                    <input type="text" placeholder="تصویر را وارد کنید" v-model="value.image">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="آدرس را وارد کنید" v-model="value.address">
                                                </td>
                                                <td>
                                                    <i @click="deleteAd(allIndex,index2)">
                                                        <svg-icon :icon="'#trash'"></svg-icon>
                                                    </i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="abilityPost" v-if="item.name == 'پیشنهاد شگفت انگیز'">
                                        <table class="abilityTable">
                                            <tr>
                                                <th>تصویر تبلیغ بالا</th>
                                                <th>آدرس تبلیغ بالا</th>
                                                <th>
                                                    <i @click="addAd(allIndex)">
                                                        <svg-icon :icon="'#add'"></svg-icon>
                                                    </i>
                                                </th>
                                            </tr>
                                            <tr v-for="(value, index2) in item.view" :key="index2">
                                                <td>
                                                    <input type="text" placeholder="تصویر را وارد کنید" v-model="value.image">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="آدرس را وارد کنید" v-model="value.address">
                                                </td>
                                                <td>
                                                    <i @click="deleteAd(allIndex,index2)">
                                                        <svg-icon :icon="'#trash'"></svg-icon>
                                                    </i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="abilityPost" v-if="item.name == 'پیشنهاد شگفت انگیز'">
                                        <table class="abilityTable">
                                            <tr>
                                                <th>تصویر تبلیغ راست</th>
                                                <th>آدرس تبلیغ راست</th>
                                                <th>
                                                    <i @click="addAd3(allIndex)">
                                                        <svg-icon :icon="'#add'"></svg-icon>
                                                    </i>
                                                </th>
                                            </tr>
                                            <tr v-for="(value, index2) in item.titleEn" :key="index2">
                                                <td>
                                                    <input type="text" placeholder="تصویر را وارد کنید" v-model="value.image">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="آدرس را وارد کنید" v-model="value.address">
                                                </td>
                                                <td>
                                                    <i @click="deleteAd3(allIndex,index2)">
                                                        <svg-icon :icon="'#trash'"></svg-icon>
                                                    </i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div class="allTemplateSettingContainer" v-if="tab == 1">
                    <div class="allTemplateSettingContainerLists">
                        <div class="allTemplateSettingList">
                            <div class="allTemplateSettingListTitle">
                                همه ویجت ها
                            </div>
                            <VuePerfectScrollbar class="scroll-area">
                                <draggable v-model="allVidgetSingle" class="drags" group="people2" @start="drag=true" @change="btnSend" @end="drag = false">
                                    <div class="allCategorySettingContainerListItem" v-for="(item,index) in allVidgetSingle" :key="index">
                                        <div class="dragsItem" v-if="item.name == 'اطلاعات محصول 1'">
                                            <img src="/img/singleTop1.png">
                                            <h3>اطلاعات محصول 1</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'اطلاعات محصول 2'">
                                            <img src="/img/singleTop2.png">
                                            <h3>اطلاعات محصول 2</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'محصولات مرتبط 1'">
                                            <img src="/img/singleRel1.png">
                                            <h3>محصولات مرتبط 1</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'محصولات مرتبط 2'">
                                            <img src="/img/singleRel2.png">
                                            <h3>محصولات مرتبط 2</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'توضیحات 1'">
                                            <img src="/img/singleDetail1.png">
                                            <h3>توضیحات 1</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'توضیحات 2'">
                                            <img src="/img/singleDetail2.png">
                                            <h3>توضیحات 2</h3>
                                        </div>
                                    </div>
                                </draggable>
                            </VuePerfectScrollbar>
                        </div>
                    </div>
                    <div class="allTemplateSettingList">
                        <div class="allTemplateSettingListTitle">
                            قالب وبسایت شما
                        </div>
                        <draggable class="allTemplateSettingListDrag" v-model="form.allSiteTemplateSingle" group="people2" @start="drag=true" @end="drag = false">
                            <div class="allCategorySettingContainerListItemChoice"  v-for="(item , allIndex) in form.allSiteTemplateSingle" :key="allIndex">
                                <img src="/img/singleTop1.png"  v-if="item.name == 'اطلاعات محصول 1'">
                                <img src="/img/singleTop2.png"  v-if="item.name == 'اطلاعات محصول 2'">
                                <img src="/img/singleRel1.png"  v-if="item.name == 'محصولات مرتبط 1'">
                                <img src="/img/singleRel2.png"  v-if="item.name == 'محصولات مرتبط 2'">
                                <img src="/img/singleDetail1.png"  v-if="item.name == 'توضیحات 1'">
                                <img src="/img/singleDetail2.png"  v-if="item.name == 'توضیحات 2'">
                            </div>
                        </draggable>
                    </div>
                </div>
                <div class="allTemplateSettingContainer" v-if="tab == 2">
                    <div class="allTemplateSettingContainerLists">
                        <div class="allTemplateSettingList">
                            <div class="allTemplateSettingListTitle">
                                همه ویجت ها
                            </div>
                            <VuePerfectScrollbar class="scroll-area">
                                <draggable v-model="allVidgetApp" class="drags" group="people3" @start="drag=true" @change="btnSend" @end="drag = false">
                                    <div class="allCategorySettingContainerListItem" v-for="(item,index) in allVidgetApp" :key="index">
                                        <div class="dragsItem" v-if="item.name == 'پست های خارج پس زمینه'">
                                            <img src="/img/backProduct.png">
                                            <h3>پست های خارج پس زمینه</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'برند ویژه'">
                                            <img src="/img/brandIndex.png">
                                            <h3>برند ویژه</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'دسته بندی خاص'">
                                            <img src="/img/homeHooper.png">
                                            <h3>دسته بندی خاص</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'پست افقی'">
                                            <img src="/img/horizontalProduct.png">
                                            <h3>پست افقی</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'پست ویژه با تصویر'">
                                            <img src="/img/imageWidget.png">
                                            <h3>پست ویژه با تصویر</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'دسته بندی رنگی'">
                                            <img src="/img/professionalCategory.png">
                                            <h3>دسته بندی رنگی</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'ویژگی ها'">
                                            <img src="/img/siteProperty.png">
                                            <h3>ویژگی ها</h3>
                                        </div>
                                        <div class="dragsItem" v-if="item.name == 'دسته بندی و پیشنهاد لحظه ای'">
                                            <img src="/img/specialIndex.png">
                                            <h3>دسته بندی و پیشنهاد لحظه ای</h3>
                                        </div>
                                    </div>
                                </draggable>
                            </VuePerfectScrollbar>
                        </div>
                    </div>
                    <div class="allTemplateSettingList">
                        <div class="allTemplateSettingListTitle">
                            قالب وبسایت شما
                        </div>
                        <draggable class="allTemplateSettingListDrag" v-model="form.allSiteTemplateApp" group="people3" @start="drag=true" @end="drag = false">
                            <div class="allCategorySettingContainerListItemChoice"  v-for="(item , allIndex) in form.allSiteTemplateApp" :key="allIndex">
                                <img v-if="item.name == 'پست های خارج پس زمینه'" src="/img/backProduct.png">
                                <img v-if="item.name == 'برند ویژه'" src="/img/brandIndex.png">
                                <img v-if="item.name == 'دسته بندی خاص'" src="/img/homeHooper.png">
                                <img v-if="item.name == 'پست افقی'" src="/img/horizontalProduct.png">
                                <img v-if="item.name == 'پست ویژه با تصویر'" src="/img/imageWidget.png">
                                <img v-if="item.name == 'دسته بندی رنگی'" src="/img/professionalCategory.png">
                                <img v-if="item.name == 'ویژگی ها'" src="/img/siteProperty.png">
                                <img v-if="item.name == 'دسته بندی و پیشنهاد لحظه ای'" src="/img/specialIndex.png">
                                <div class="allCategorySettingContainerListItemChoiceData">
                                    <h3>{{item.name}}</h3>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'پست ویژه با تصویر' && item.name != 'دسته بندی رنگی' && item.name != 'پیشنهاد شگفت انگیز' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>نمایش عنوان :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.title">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'پست ویژه با تصویر' && item.name != 'دسته بندی رنگی' && item.name != 'پیشنهاد شگفت انگیز' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>نمایش عنوان انگلیسی :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.titleEn">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'پست ویژه با تصویر' && item.name != 'پست های خارج پس زمینه' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>عنوان مشاهده بیشتر :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.more">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'پست های خارج پس زمینه' && item.name != 'پست ویژه با تصویر' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>عنوان مشاهده بیشتر انگلیسی :</label>
                                        <input type="text" placeholder="عنوان را وارد کنید" v-model="item.moreEn">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name == 'پست ویژه با تصویر' || item.name == 'پست های خارج پس زمینه'">
                                        <label>تصویر پس زمینه :</label>
                                        <input type="text" placeholder="آدرس را وارد کنید" v-model="item.background">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name == 'دسته بندی رنگی'">
                                        <label>رنگ پس زمینه :</label>
                                        <input type="text" placeholder="کد رنگ را وارد کنید" v-model="item.background">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات اسلایدری' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'پست ویژه با تصویر' && item.name != 'تبلیغات ساده' && item.name != 'ویژگی ها' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'جستجو'">
                                        <label>پیوند یکتا (slug) :</label>
                                        <input type="text" placeholder="پیوند را وارد کنید" v-model="item.slug">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name == 'دسته بندی رنگی'">
                                        <label>آدرس تصویر :</label>
                                        <input type="text" placeholder="آدرس را وارد کنید" v-model="item.title">
                                    </div>
                                    <div class="allCategoryTitle" v-if="item.name != 'تبلیغات ساده' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'تبلیغات اسلایدری' && item.name != 'ویژگی ها'">
                                        <label>تعداد نمایش :</label>
                                        <input type="text" placeholder="تعداد را وارد کنید" v-model="item.count">
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'جستجو' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'تبلیغات ساده' && item.name != 'برند ویژه'">
                                        <label>دسته بندی :</label>
                                        <div class="allTaxes">
                                            <div class="taxShow" @click="btnShowTax(2,allIndex,'app')">
                                                <h4 v-if="item.category.length == 0">دسته بندی را وارد کنید</h4>
                                                <ul v-else>
                                                    <li v-for="(item , index) in item.category" :key="index">
                                                        <i @click.stop="btnClose(index,allIndex,'category' , 'app')">
                                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                                        </i>
                                                        <span>{{item}}</span>
                                                    </li>
                                                </ul>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </div>
                                            <ul class="showAllTaxes" v-if="showTax == 2 && showAllTax == allIndex">
                                                <VuePerfectScrollbar class="scroll-area">
                                                    <li v-for="(item , index) in category" @click.stop="sendTax(item.name,allIndex,'category' , 'app')" :key="index">
                                                        {{item.name}}
                                                    </li>
                                                </VuePerfectScrollbar>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'جستجو' && item.name != 'پیشنهاد شگفت انگیز' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'تبلیغات ساده'">
                                        <label>برند :</label>
                                        <div class="allTaxes">
                                            <div class="taxShow" @click="btnShowTax(1,allIndex , 'app')">
                                                <h4 v-if="item.brand.length == 0">برند را وارد کنید</h4>
                                                <ul v-else>
                                                    <li v-for="(item , index) in item.brand" :key="index">
                                                        <i @click.stop="btnClose(index,allIndex,'brand','app')">
                                                            <svg-icon :icon="'#cancel'"></svg-icon>
                                                        </i>
                                                        <span>{{item}}</span>
                                                    </li>
                                                </ul>
                                                <svg-icon :icon="'#down'"></svg-icon>
                                            </div>
                                            <ul class="showAllTaxes" v-if="showTax == 1 && showAllTax == allIndex">
                                                <VuePerfectScrollbar class="scroll-area">
                                                    <li v-for="(item , index) in brand" @click.stop="sendTax(item.name,allIndex,'brand','app')" :key="index">
                                                        {{item.name}}
                                                    </li>
                                                </VuePerfectScrollbar>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'جستجو' && item.name != 'اسلایدر بزرگ تبلیغ' && item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'برند ویژه' && item.name != 'تبلیغات ساده'">
                                        <label>نمایش براساس :</label>
                                        <div class="allCategoryPanel" @click="btnShowTax(3,allIndex)">
                                            <div class="categoryShow">
                                                <h4 v-if="item.show == 0">جدید ترین</h4>
                                                <h4 v-if="item.show == 1">محبوب ترین</h4>
                                                <h4 v-if="item.show == 2">پربازدید ترین</h4>
                                                <h4 v-if="item.show == 3">ارزان ترین</h4>
                                                <h4 v-if="item.show == 4">گران ترین</h4>
                                                <h4 v-if="item.show == 5">پرفروش ترین</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showTax == 3 && showAllTax == allIndex">
                                                <li @click="item.show = 0">جدید ترین</li>
                                                <li @click="item.show = 1">محبوب ترین</li>
                                                <li @click="item.show = 2">پربازدید ترین</li>
                                                <li @click="item.show = 3">ارزان ترین</li>
                                                <li @click="item.show = 4">گران ترین</li>
                                                <li @click="item.show = 5">پرفروش ترین</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="allCategorySettingSelect" v-if="item.name != 'ویژگی ها' && item.name != 'تبلیغات اسلایدری' && item.name != 'برند ویژه' && item.name != 'تبلیغات ساده'">
                                        <label>نوع کالا :</label>
                                        <div class="allCategoryPanel" @click="btnShowTax(4,allIndex)">
                                            <div class="categoryShow">
                                                <h4 v-if="item.type == 3">همه</h4>
                                                <h4 v-if="item.type == 0">فقط موجود</h4>
                                                <h4 v-if="item.type == 1">تخفیف خورده</h4>
                                                <h4 v-if="item.type == 2">پیشنهادی</h4>
                                                <i>
                                                    <svg-icon :icon="'#down'"></svg-icon>
                                                </i>
                                            </div>
                                            <ul v-if="showTax == 4 && showAllTax == allIndex">
                                                <li @click="item.type = 3">همه</li>
                                                <li @click="item.type = 0">فقط موجود</li>
                                                <li @click="item.type = 1">تخفیف خورده</li>
                                                <li @click="item.type = 2">پیشنهادی</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="abilityPost" v-if="item.name == 'پست ویژه با تصویر'">
                                        <table class="abilityTable">
                                            <tr>
                                                <th>تصویر تبلیغ سمت راست</th>
                                                <th>آدرس تبلیغ سمت راست</th>
                                                <th>
                                                    <i @click="addAd2(allIndex , 'app')">
                                                        <svg-icon :icon="'#add'"></svg-icon>
                                                    </i>
                                                </th>
                                            </tr>
                                            <tr v-for="(value, index2) in item.slug" :key="index2">
                                                <td>
                                                    <input type="text" placeholder="تصویر را وارد کنید" v-model="value.image">
                                                </td>
                                                <td>
                                                    <input type="text" placeholder="آدرس را وارد کنید" v-model="value.address">
                                                </td>
                                                <td>
                                                    <i @click="deleteAd2(allIndex,index2 , 'app')">
                                                        <svg-icon :icon="'#trash'"></svg-icon>
                                                    </i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </div>
            </div>
            <button class="sendBtn" @click="btnCreate">ارسال اطلاعات</button>
        </div>
    </admin-layout>
</template>

<script>
import AdminLayout from "../../../components/layout/AdminLayout";
import draggable from 'vuedraggable';
import SvgIcon from '../../Svg/SvgIcon.vue';
import VuePerfectScrollbar from "vue-perfect-scrollbar";
export default {
    name:'TemplateSetting',
    props:['vidget','vidgetApp','vidgetSingle','category','categoryNews','brand'],
    components: {
        AdminLayout,
        draggable,
        SvgIcon,
        VuePerfectScrollbar,
    },
    metaInfo: {
        title: 'تنظیمات قالب سایت'
    },
    data() {
        return {
            allVidget:[
                {
                    name:'تبلیغات اسلایدری',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    more: '',
                    moreEn: '',
                    count: '',
                    background: '',
                    view: [],
                    slug: [],
                },
                {
                    name:'تبلیغات ساده',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    more: '',
                    moreEn: '',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                },
                {
                    name:'پست های خارج پس زمینه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'محصولات دانلودی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'اسلایدر بزرگ تبلیغ',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'برند ویژه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی خاص',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست افقی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'خبر ها',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست ویژه با تصویر',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:[],
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: [],
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی رنگی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'جستجو',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'ویژگی ها',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی و پیشنهاد لحظه ای',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پیشنهاد شگفت انگیز',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:[],
                    titleEn:[],
                    count: '',
                    background: '',
                    view: [],
                    slug: [],
                    more: '',
                    moreEn: '',
                },
            ],
            allVidgetApp:[
                {
                    name:'پست های خارج پس زمینه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'برند ویژه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی خاص',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست افقی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست ویژه با تصویر',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:[],
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: [],
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی رنگی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'ویژگی ها',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی و پیشنهاد لحظه ای',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
            ],
            allVidgetSingle:[
                {
                    name:'اطلاعات محصول 1',
                },
                {
                    name:'اطلاعات محصول 2',
                },
                {
                    name:'محصولات مرتبط 1',
                },
                {
                    name:'محصولات مرتبط 2',
                },
                {
                    name:'توضیحات 1',
                },
                {
                    name:'توضیحات 2',
                },
            ],
            form:{
                update: false,
                allSiteTemplate: [],
                allSiteTemplateApp: [],
                allSiteTemplateSingle: [],
            },
            showTax: 0,
            showAllTax: 0,
            tab: 0,
            i:0
        }
    },
    methods:{
        deleteAbility(allIndex,index){
            this.form.allSiteTemplate[allIndex].category.splice(index,1);
        },
        addAbility(allIndex) {
            this.form.allSiteTemplate[allIndex].category.push({
                image:'',
                address:'',
            });
        },
        deleteAd(allIndex,index){
            this.form.allSiteTemplate[allIndex].view.splice(index,1);
        },
        addAd(allIndex) {
            this.form.allSiteTemplate[allIndex].view.push({
                image:'',
                address:'',
            });
        },
        deleteAd2(allIndex,index,platform){
            if (platform == 'app'){
                this.form.allSiteTemplateApp[allIndex].slug.splice(index,1);
            }else{
                this.form.allSiteTemplate[allIndex].slug.splice(index,1);
            }
        },
        addAd2(allIndex,platform) {
            if (platform == 'app'){
                this.form.allSiteTemplateApp[allIndex].slug.push({
                    image:'',
                    address:'',
                });
            }else{
                this.form.allSiteTemplate[allIndex].slug.push({
                    image:'',
                    address:'',
                });
            }
        },
        deleteAd3(allIndex,index){
            this.form.allSiteTemplate[allIndex].titleEn.splice(index,1);
        },
        addAd3(allIndex) {
            this.form.allSiteTemplate[allIndex].titleEn.push({
                image:'',
                address:'',
            });
        },
        btnShowTax(num,index){
            if (this.showTax == num){
                this.showTax = 0
            }else{
                this.showTax = num;
            }
            this.showAllTax = index;
        },
        btnSend(){
            this.allVidget=[
                {
                    name:'تبلیغات اسلایدری',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    more: '',
                    moreEn: '',
                    count: '',
                    background: '',
                    view: [],
                    slug: [],
                },
                {
                    name:'تبلیغات ساده',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    more: '',
                    moreEn: '',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                },
                {
                    name:'پست های خارج پس زمینه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'محصولات دانلودی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    more: '',
                    moreEn: '',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                },
                {
                    name:'اسلایدر بزرگ تبلیغ',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'برند ویژه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی خاص',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست افقی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'خبر ها',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست ویژه با تصویر',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title: [],
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: [],
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی رنگی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'جستجو',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'ویژگی ها',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی و پیشنهاد لحظه ای',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پیشنهاد شگفت انگیز',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:[],
                    titleEn:[],
                    count: '',
                    background: '',
                    view: [],
                    slug: [],
                    more: '',
                    moreEn: '',
                },
            ];
            this.allVidgetApp=[
                {
                    name:'پست های خارج پس زمینه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'برند ویژه',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی خاص',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست افقی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'پست ویژه با تصویر',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:[],
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: [],
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی رنگی',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'ویژگی ها',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
                {
                    name:'دسته بندی و پیشنهاد لحظه ای',
                    brand:[],
                    category:[],
                    type:3,
                    show:0,
                    title:'',
                    titleEn:'',
                    count: '',
                    background: '',
                    view: 0,
                    slug: '',
                    more: '',
                    moreEn: '',
                },
            ];
            this.allVidgetSingle=[
                {
                    name:'اطلاعات محصول 1',
                },
                {
                    name:'اطلاعات محصول 2',
                },
                {
                    name:'محصولات مرتبط 1',
                },
                {
                    name:'محصولات مرتبط 2',
                },
                {
                    name:'توضیحات 1',
                },
                {
                    name:'توضیحات 2',
                },
            ];
        },
        btnClose(index,allIndex,type,platform){
            if(type == 'brand'){
                if(platform == 'app'){
                    this.form.allSiteTemplateApp[allIndex].brand.splice(index,1);
                }else{
                    this.form.allSiteTemplate[allIndex].brand.splice(index,1);
                }
            }
            if(type == 'category'){
                if(platform == 'app'){
                    this.form.allSiteTemplateApp[allIndex].category.splice(index,1);
                }else{
                    this.form.allSiteTemplate[allIndex].category.splice(index,1);
                }
            }
        },
        sendTax(name,allIndex,type,platform){
            if(type == 'brand'){
                if(platform == 'app'){
                    this.form.allSiteTemplateApp[allIndex].brand.push(name);
                }else{
                    this.form.allSiteTemplate[allIndex].brand.push(name);
                }
            }
            if(type == 'category'){
                if(platform == 'app'){
                    this.form.allSiteTemplateApp[allIndex].category.push(name);
                }else{
                    this.form.allSiteTemplate[allIndex].category.push(name);
                }
            }
        },
        btnCreate(){
            this.i = 0;
            for ( this.i ; this.i < this.form.allSiteTemplate.length; this.i++) {
                if(this.form.allSiteTemplate[this.i].name == 'تبلیغات ساده' ||this.form.allSiteTemplate[this.i].name == 'اسلایدر بزرگ تبلیغ'){
                    this.form.allSiteTemplate[this.i].brand = JSON.stringify(this.form.allSiteTemplate[this.i].category);
                }
                if(this.form.allSiteTemplate[this.i].name == 'پیشنهاد شگفت انگیز'){
                    this.form.allSiteTemplate[this.i].brand = JSON.stringify(this.form.allSiteTemplate[this.i].titleEn);
                    this.form.allSiteTemplate[this.i].title = JSON.stringify(this.form.allSiteTemplate[this.i].view);
                }
                if(this.form.allSiteTemplate[this.i].name == 'تبلیغات اسلایدری'){
                    this.form.allSiteTemplate[this.i].category = JSON.stringify(this.form.allSiteTemplate[this.i].view);
                    this.form.allSiteTemplate[this.i].brand = JSON.stringify(this.form.allSiteTemplate[this.i].slug);
                }
                if(this.form.allSiteTemplate[this.i].name == 'پست ویژه با تصویر'){
                    this.form.allSiteTemplate[this.i].title = JSON.stringify(this.form.allSiteTemplate[this.i].slug);
                }
            }
            this.i = 0;
            for ( this.i ; this.i < this.form.allSiteTemplateApp.length; this.i++) {
                if(this.form.allSiteTemplateApp[this.i].name == 'پست ویژه با تصویر'){
                    this.form.allSiteTemplateApp[this.i].title = JSON.stringify(this.form.allSiteTemplateApp[this.i].slug);
                }
            }
            this.i = 0;
            this.form.update = true;
            const url = "/admin/setting/setting-design";
            this.$inertia.post(url , this.form)
            this.$swal.fire(
                'با موفقیت انجام شد',
                'تنظیمات با موفقیت انجام شد',
                'success'
            );
        },
        getData(){
            this.form.allSiteTemplate = this.vidget;
            this.form.allSiteTemplateApp = this.vidgetApp;
            this.form.allSiteTemplateSingle = this.vidgetSingle;
            this.i = 0;
            for ( this.i ; this.i < this.form.allSiteTemplate.length; this.i++) {
                if(this.form.allSiteTemplate[this.i].name == 'تبلیغات ساده' || this.form.allSiteTemplate[this.i].name == 'اسلایدر بزرگ تبلیغ' || this.form.allSiteTemplate[this.i].name == 'پست ویژه با تصویر'){
                    if(this.form.allSiteTemplate[this.i].brand != ''){
                        this.form.allSiteTemplate[this.i].category = JSON.parse(this.form.allSiteTemplate[this.i].brand);
                    }
                }
                if(this.form.allSiteTemplate[this.i].name == 'پیشنهاد شگفت انگیز'){
                    if(this.form.allSiteTemplate[this.i].brand != ''){
                        this.form.allSiteTemplate[this.i].titleEn = JSON.parse(this.form.allSiteTemplate[this.i].brand);
                    }
                    if(this.form.allSiteTemplate[this.i].title != ''){
                        this.form.allSiteTemplate[this.i].view = JSON.parse(this.form.allSiteTemplate[this.i].title);
                    }
                }
                if(this.form.allSiteTemplate[this.i].name == 'پست ویژه با تصویر'){
                    if(this.form.allSiteTemplate[this.i].title != ''){
                        this.form.allSiteTemplate[this.i].slug = JSON.parse(this.form.allSiteTemplate[this.i].title);
                    }
                }
                if(this.form.allSiteTemplate[this.i].name == 'تبلیغات اسلایدری'){
                    if(this.form.allSiteTemplate[this.i].brand != ''){
                        this.form.allSiteTemplate[this.i].slug = JSON.parse(this.form.allSiteTemplate[this.i].brand);
                    }
                    if(this.form.allSiteTemplate[this.i].category != ''){
                        this.form.allSiteTemplate[this.i].view = JSON.parse(this.form.allSiteTemplate[this.i].category);
                    }
                }
            }
            this.i = 0;
            for ( this.i ; this.i < this.form.allSiteTemplateApp.length; this.i++) {
                if(this.form.allSiteTemplateApp[this.i].name == 'پست ویژه با تصویر'){
                    if(this.form.allSiteTemplateApp[this.i].title != ''){
                        this.form.allSiteTemplateApp[this.i].slug = JSON.parse(this.form.allSiteTemplateApp[this.i].title);
                    }
                }
            }
            this.i = 0;
        },
        sidebar(){
            this.$eventHub.emit('sidebar' , '2');
        },
    },
    mounted(){
        this.getData();
        this.sidebar();
    },
}
</script>

<style scoped>

</style>

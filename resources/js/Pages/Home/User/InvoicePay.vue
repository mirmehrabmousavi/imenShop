<template>
    <div class="allInvoicePay">
        <button @click="printButton" class="print-button">پرینت</button>
        <div class="page">
            <h1 style="text-align: center;">
                صورت حساب الکترونیکی
                فروش نیابتی
            </h1>
            <table class="header-table" style="width: 100%">
                <tr>
                    <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle;padding-bottom: 4px;">
                        <div class="header-item-wrapper">
                            <div class="portait" style="margin:5px">حق‌العمل کار</div>
                        </div>
                    </td>
                    <td style="padding: 0 4px 4px;height: 2cm;">
                        <div class="bordered grow header-item-data">
                            <table class="grow centered">
                                <tr>
                                    <td style="width: 7cm">
                                        <span class="label">فروشنده:</span>{{title }}
                                    </td>
                                    <td style="width: 5cm">
                                        <span class="label">شناسه ملی:</span>
                                        <span v-if="pay.user.user_meta.length">{{ pay.user.user_meta[0].code }}</span>
                                    </td>
                                    <td>
                                        <span class="label">شماره ثبت:</span> {{ pay.id }}
                                    </td>
                                    <td colspan="2">
                                        <span class="label">شماره سفارش:</span> {{ pay.property }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">نشانی شرکت:</span>{{address}}</td>
                                    <td colspan="2">
                                        <span class="label">ایمیل:</span> {{ email }}
                                    </td>
                                    <td colspan="2">
                                        <span class="label">تلفن و فکس:</span> {{number}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 7cm; height: 2cm; padding: 0 4px 4px 0;">
                        <div class="bordered" style="text-align: center; height: 100%; padding: 0.4cm 0.2cm;">
                            <div class="flex">
                                <div class="font-small">شماره فاکتور:</div>
                                <div class="flex-grow" style="text-align: left">{{ pay.id }}</div>
                            </div>
                            <div class="flex">
                                <div>تاریخ:</div>
                                <div class="flex-grow"
                                     style="text-align: left">{{ pay.created_at }}
                                </div>
                            </div>
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>پیگیری:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">{{ pay.property }}
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 1.8cm; height: 2cm;vertical-align: center; padding: 0 0 4px">
                        <div class="header-item-wrapper">
                            <div class="portait" style="margin: 20px">خریدار</div>
                        </div>
                    </td>
                    <td style="height: 2cm;vertical-align: center; padding: 0 4px 4px">
                        <div class="bordered header-item-data">
                            <table class="centered" style="height: 100%">
                                <tr>
                                    <td>
                                        <span class="label">خریدار:</span> {{pay.user.name}}
                                    </td>
                                    <td style="width: 6.7cm">
                                        <span class="label">شماره‌اقتصادی / شماره‌ملی:</span>
                                        <span v-if="pay.user.user_meta.length">{{pay.user.user_meta[0].code}}</span>
                                    </td>
                                    <td>
                                        <span class="label">شناسه ملی:</span>
                                        <span v-if="pay.user.user_meta.length">{{pay.user.user_meta[0].code}}</span>
                                    </td>
                                    <td>
                                        <span class="label">شماره ثبت:</span> {{pay.id}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" v-if="pay.address.length">
                                        <span class="label">نشانی:</span>{{pay.address[0].address}}
                                    </td>
                                </tr>
                                <tr v-if="pay.address.length">
                                    <td colspan="2">
                                        <span class="label">شماره تماس:</span> {{pay.address[0].number}}
                                    </td>
                                    <td colspan="2">
                                        <span class="label">کد پستی:</span> {{pay.address[0].post}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 7cm;padding: 0 4px 4px 0;">
                        <div class="bordered" style="text-align: center; height: 100%;">
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>شماره مالیاتی:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">-
                                </div>
                            </div>
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>سریال حافظه مالیاتی:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">-
                                </div>
                            </div>
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>سریال پایانه فروشگاهی:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">-
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="content-table">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>شناسه کالا یا خدمت</th>
                    <th style="width: 30%">شرح کالا یا خدمت</th>
                    <th>آمر</th>
                    <th>تعداد</th>
                    <th style="width: 2.3cm">مبلغ فعلی (تومان)</th>
                    <th style="width: 2.3cm">مبلغ کل خرید (تومان)</th>
                    <th style="width: 2.3cm">مقدار تخفیف (درصد)</th>
                    <th style="width: 2.3cm">مبلغ کل پس از تخفیف (تومان)</th>
                    <th style="width: 2.3cm"> جمع مالیات و عوارض ارزش افزوده (تومان)</th>
                    <th style="width: 2.5cm"> جمع کل پس از تخفیف و مالیات و عوارض (تومان)</th>
                </tr>
                </thead>
                <tr v-for="(item,index) in pay.pay_meta">
                    <td>{{ ++index }}</td>
                    <td>{{ item.post.product_id }}</td>
                    <td>
                        <div class="title">{{item.post.title}}
                            <span>| {{JSON.parse(item.color).name}} |</span>
                            <span> {{JSON.parse(item.size).name}} |</span>
                            <span v-if="item.guarantee.length"> {{item.guarantee[0].name}} |</span>
                        </div>
                        <div class="serials">{{ item.post.product_id }}</div>
                    </td>
                    <td><span>{{ item.post.user.name }}</span></td>
                    <td><span class="ltr">{{ item.count }}</span></td>
                    <td><span class="ltr">
                                            {{ item.post.price|NumFormat }}
                                    </span></td>

                    <td><span class="ltr">
                                            {{ (item.price)|NumFormat }}
                                        </span></td>

                    <td>
                        <span class="ltr" v-if="item.discount">
                                            {{ item.discount.percent }}
                                        </span>
                        <span class="ltr" v-else>
                                            -
                                        </span>
                    </td>

                    <td><span class="ltr">
                                            {{ (item.price)|NumFormat }}
                                        </span></td>

                    <td><span class="ltr">
                                            ۰
                                        </span></td>

                    <td><span class="ltr">
                                        {{ (item.price)|NumFormat }}
                                        </span></td>

                </tr>
                <tfoot>
                <tr>
                    <td colspan="6">
                    <td class="font-small" colspan="4">
                        جمع کل پس از کسر تخفیف با احتساب مالیات و عوارض (تومان):
                    </td>

                    <td><span class="ltr">
                                    {{ pay.price|NumFormat }}
                                </span></td>
                </tr>
                <tr>
                    <td colspan="4">
                        اعتبار مالیاتی قابل استفاده توسط خریدار
                    </td>
                    <td class="font-small" colspan="7">
                    </td>
                </tr>
                <tr style="background: #fff">
                    <td colspan="11" style="height: 2.5cm;vertical-align: top">
                        <div class="flex">
                            <div class="flex-grow">مهر و امضای فروشنده:</div>
                            <div class="flex-grow">تاریخ تحویل:</div>
                            <div class="flex-grow">ساعت تحویل:</div>

                            <div class="flex-grow">روش‌های پرداخت:</div>

                            <div class="flex-grow">مهر و امضای خریدار:</div>

                            <div class="flex-grow" style="width: 1.8cm"></div>
                        </div>
                        <div class="flex">
                            <div class="flex-grow">
                                <img class="footer-img uk-align-center"
                                     :src="logo"
                                     width="150px">
                            </div>
                            <div class="flex-grow">
                                <span v-if="pay.time">
                                    {{JSON.parse(pay.time).dayL}}
                                    {{JSON.parse(pay.time).day}}
                                    {{JSON.parse(pay.time).month}}
                                </span>
                            </div>
                            <div class="flex-grow" v-if="pay.time">
                                <span>
                                    00::{{JSON.parse(pay.time).from}}
                                    -
                                    00::{{JSON.parse(pay.time).to}}
                                </span>
                            </div>

                            <div class="flex-grow">
                                <ul>
                                    <li style="text-align: right">
                                        اعتباری : {{ pay.price|NumFormat }}
                                    </li>
                                </ul>
                            </div>
                            <div class="flex-grow"></div>

                            <div
                                style="display: flex; align-items: center; justify-content: center; margin-top: 4px; margin-left: 10px;">
                                <img
                                    class="footer-img uk-align-center"
                                    src="https://www.digikala.com/static/files/40417d6f.jpg"
                                    style="width: 1.5cm; margin-left: 4px;"
                                />
                                <span style="width: 2cm;">
                            </span>
                            </div>
                        </div>
                        <h3> به هنگام ارسال در سامانه معاملات فصلی ( ماده 169 مکرر) گزینه خرید از طریق حق العمل کار را
                            انتخاب کنید
                        </h3>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="page">
            <h1 style="text-align: center;">
                صورت حساب الکترونیکی
                فروش
            </h1>
            <table class="header-table" style="width: 100%">
                <tr>
                    <td style="width: 1.8cm; height: 2.5cm;vertical-align: middle;padding-bottom: 4px;">
                        <div class="header-item-wrapper">
                            <div class="portait" style="margin:5px">حق‌العمل کار</div>
                        </div>
                    </td>
                    <td style="padding: 0 4px 4px;height: 2cm;">
                        <div class="bordered grow header-item-data">
                            <table class="grow centered">
                                <tr>
                                    <td style="width: 7cm">
                                        <span class="label">فروشنده:</span>{{title }}
                                    </td>
                                    <td style="width: 5cm">
                                        <span class="label">شناسه ملی:</span>
                                        <span v-if="pay.user.user_meta.length">{{ pay.user.user_meta[0].code }}</span>
                                    </td>
                                    <td>
                                        <span class="label">شماره ثبت:</span> {{ pay.id }}
                                    </td>
                                    <td colspan="2">
                                        <span class="label">شماره سفارش:</span> {{ pay.property }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="label">نشانی شرکت:</span>{{address}}</td>
                                    <td colspan="2">
                                        <span class="label">ایمیل:</span> {{ email }}
                                    </td>
                                    <td colspan="2">
                                        <span class="label">تلفن و فکس:</span> {{number}}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 7cm; height: 2cm; padding: 0 4px 4px 0;">
                        <div class="bordered" style="text-align: center; height: 100%; padding: 0.4cm 0.2cm;">
                            <div class="flex">
                                <div class="font-small">شماره فاکتور:</div>
                                <div class="flex-grow" style="text-align: left">{{ pay.id }}</div>
                            </div>
                            <div class="flex">
                                <div>تاریخ:</div>
                                <div class="flex-grow"
                                     style="text-align: left">{{ pay.created_at }}
                                </div>
                            </div>
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>پیگیری:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">{{ pay.property }}
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 1.8cm; height: 2cm;vertical-align: center; padding: 0 0 4px">
                        <div class="header-item-wrapper">
                            <div class="portait" style="margin: 20px">خریدار</div>
                        </div>
                    </td>
                    <td style="height: 2cm;vertical-align: center; padding: 0 4px 4px">
                        <div class="bordered header-item-data">
                            <table class="centered" style="height: 100%">
                                <tr>
                                    <td>
                                        <span class="label">خریدار:</span> {{pay.user.name}}
                                    </td>
                                    <td style="width: 6.7cm">
                                        <span class="label" v-if="pay.user.user_meta.length">شماره‌اقتصادی / شماره‌ملی:</span>
                                        <span v-if="pay.user.user_meta.length">{{pay.user.user_meta[0].code}}</span>
                                    </td>
                                    <td>
                                        <span class="label">شناسه ملی:</span>
                                        <span v-if="pay.user.user_meta.length">{{pay.user.user_meta[0].code}}</span>
                                    </td>
                                    <td>
                                        <span class="label">شماره ثبت:</span> {{pay.id}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" v-if="pay.address.length">
                                        <span class="label">نشانی:</span>{{pay.address[0].address}}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span class="label">شماره تماس:</span> {{pay.user.number}}
                                    </td>
                                    <td colspan="2">
                                        <span class="label">کد پستی:</span>
                                        <span v-if="pay.user.user_meta.length">{{pay.user.user_meta[0].post}}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 7cm;padding: 0 4px 4px 0;">
                        <div class="bordered" style="text-align: center; height: 100%;">
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>شماره مالیاتی:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">-
                                </div>
                            </div>
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>سریال حافظه مالیاتی:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">-
                                </div>
                            </div>
                            <div class="flex" style="margin-bottom: 4px;">
                                <div>سریال پایانه فروشگاهی:</div>
                                <div class="flex-grow font-medium"
                                     style="text-align: left">-
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <table class="content-table">
                <thead>
                <tr>
                    <th>ردیف</th>
                    <th>شناسه کالا یا خدمت</th>
                    <th colspan="2.3" style="width: 30%">شرح کالا یا خدمت</th>
                    <th>تعداد</th>
                    <th style="width: 2.3cm">مبلغ واحد (تومان)</th>
                    <th style="width: 2.3cm">مبلغ کل پرداختی (تومان)</th>
                    <th style="width: 2.3cm">تخفیف (تومان)</th>
                    <th style="width: 2.3cm">مبلغ کل پس از تخفیف (تومان)</th>
                    <th style="width: 2.3cm">جمع مالیات و عوارض ارزش افزوده (تومان)</th>
                    <th style="width: 2.5cm"> جمع کل پس از تخفیف و مالیات و عوارض (تومان)</th>
                </tr>
                </thead>
                <tr v-for="(item,index) in carrier">
                    <td>{{ ++index }}</td>
                    <td>
                        {{ item.id }}
                    </td>
                    <td colspan="2">
                        <div class="title">{{ item.name }}</div>
                    </td>
                    <td>1</td>
                    <td><span class="ltr">{{ item.price|NumFormat }}</span></td>
                    <td>
                        <span class="ltr" v-if="item.limit <= pay.price">رایگان</span>
                        <span class="ltr" v-else>{{ item.price|NumFormat }}</span>
                    </td>
                    <td><span class="ltr">0</span></td>
                    <td>
                        <span class="ltr" v-if="item.limit <= pay.price">رایگان</span>
                        <span class="ltr" v-else>{{ item.price|NumFormat }}</span>
                    </td>
                    <td><span class="ltr">0</span></td>
                    <td>
                        <span class="ltr" v-if="item.limit <= pay.price">رایگان</span>
                        <span class="ltr" v-else>{{ item.price|NumFormat }}</span>
                    </td>
                </tr>
                <tfoot>
                <tr>
                    <td colspan="6">
                    <td class="font-small" colspan="4">
                        جمع کل پس از کسر تخفیف با احتساب مالیات و عوارض (تومان):
                    </td>

                    <td v-if="carrier.length">
                        <span class="ltr" v-if="carrier[0].limit <= pay.price">رایگان</span>
                        <span class="ltr" v-else>{{ carrier[0].price|NumFormat }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        اعتبار مالیاتی قابل استفاده توسط خریدار
                    </td>
                    <td class="font-small" colspan="7">
                    </td>
                </tr>
                <tr style="background: #fff">
                    <td colspan="11" style="height: 2.5cm;vertical-align: top">
                        <div class="flex">
                            <div class="flex-grow">مهر و امضای فروشنده:</div>
                            <div class="flex-grow">تاریخ تحویل:</div>
                            <div class="flex-grow">ساعت تحویل:</div>

                            <div class="flex-grow">روش‌های پرداخت:</div>

                            <div class="flex-grow">مهر و امضای خریدار:</div>

                            <div class="flex-grow" style="width: 1.8cm"></div>
                        </div>
                        <div class="flex">
                            <div class="flex-grow">
                                <img class="footer-img uk-align-center"
                                     :src="logo"
                                     width="150px">
                            </div>
                            <div class="flex-grow">{{ pay.created_at }}</div>
                            <div class="flex-grow"> -
                            </div>

                            <div class="flex-grow">
                                <ul>
                                    <li style="text-align: right">
                                        اعتباری : {{ pay.price|NumFormat }}
                                    </li>
                                </ul>
                            </div>
                            <div class="flex-grow"></div>

                            <div
                                style="display: flex; align-items: center; justify-content: center; margin-top: 4px; margin-left: 10px;">
                                <img
                                    class="footer-img uk-align-center"
                                    src="https://www.digikala.com/static/files/40417d6f.jpg"
                                    style="width: 1.5cm; margin-left: 4px;"
                                />
                                <span style="width: 2cm;">
                            </span>
                            </div>
                        </div>
                        <h3></h3>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "InvoicePay",
    props:['pay' ,'title' , 'logo' , 'email' , 'carrier' , 'address','number'],
    methods: {
        printButton: function() {
            window.print();
        },
    }
}
</script>

<style>
html, body {
    padding: 0;
    margin: 0 auto;
    max-width: 29.7cm;
    -webkit-print-color-adjust: exact;
}

body {
    padding: 0.5cm
}

* {
    box-sizing: border-box;
    -moz-box-sizing: border-box;
}

table {
    width: 100%;
    table-layout: fixed;
    border-spacing: 0;
}

.header-table {
    table-layout: fixed;
    border-spacing: 0;
}

.header-table td {
    padding: 0;
    vertical-align: top;
}

body {
    font: 9pt IRANYekanWeb;
    direction: rtl;
}

.print-button {
    cursor: pointer;
    -webkit-box-shadow: none;
    box-shadow: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    display: -webkit-inline-box;
    display: -ms-inline-flexbox;
    display: inline-flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    border-radius: 5px;
    background: none;
    -webkit-transition: all .3s ease-in-out;
    transition: all .3s ease-in-out;
    position: relative;

    outline: none;
    text-align: center;

    padding: 8px 16px;
    font-size: 12px;
    font-size: .857rem;
    line-height: 1.833;
    font-weight: 700;
    background-color: #0fabc6;
    color: #fff;
    border: 1px solid #0fabc6;
}

.page {
    background: white;
    page-break-after: always;
}

.flex {
    display: flex;
}

.flex > * {
    float: left;
}

.flex-grow {
    flex-grow: 10000000;
}

.barcode {
    text-align: center;
    margin: 12px 0 0 0;
    height: 30px;
}

.barcode span {
    font-size: 35pt;
    font-family: 'Libre Barcode 128';
}

.portait {
    transform: rotate(-90deg) translate(0, 40%);
    text-align: center;
}

.header-item-wrapper {
    border: 1px solid #000;
    width: 100%;
    height: 100%;
    background: #eee;
    display: flex;
    align-content: center;
}

thead, tfoot {
    background: #eee;
}

.header-item-data {
    height: 100%;
    width: 100%;
}

.bordered {
    border: 1px solid #000;
    padding: 0.12cm;
}

.header-table table {
    width: 100%;
    vertical-align: middle;
}

.content-table {
    border-collapse: collapse;
}

.content-table td, th {
    border: 1px solid #000;
    text-align: center;
    padding: 0.1cm;
    font-weight: normal;
}

table.centered td {
    vertical-align: middle;
}

.serials {
    direction: ltr;
    text-align: left;
}

.title {
    text-align: right;
}

.grow {
    width: 100%;
    height: 100%;
}

.font-small {
    font-size: 8pt;
}

.font-medium {
    font-size: 10pt;
}

.font-big {
    font-size: 15pt;
}

.label {
    font-weight: bold;
    padding: 0 0 0 2px;
}

@page {
    size: A4 landscape;
    margin: 0;
    margin-bottom: 0.5cm;
    margin-top: 0.5cm;
}

.ltr {
    direction: ltr;
    display: block;
}

@media print {
    .print-button {
        display: none;
        visibility: hidden;
    }
}
</style>

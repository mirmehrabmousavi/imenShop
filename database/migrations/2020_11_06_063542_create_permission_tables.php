<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        $array = [
            'فروشنده جدید',
            'ویرایش برند',
            'افزودن برند',
            'نمایش برند های خودش',
            'نمایش همه برند ها',
            'حذف برند',
            'تنظیمات دیدگاه',
            'تنظیمات سئو',
            'تنظیمات سایت',
            'فرستادن ایمیل',
            'ویرایش برگه',
            'افزودن برگه',
            'نمایش برگه',
            'حذف برگه',
            'ویرایش زمان',
            'افزودن زمان',
            'نمایش زمان',
            'حذف زمان',
            'ویرایش شارژ',
            'افزودن شارژ',
            'نمایش شارژ',
            'حذف شارژ',
            'نمایش کارمندان',
            'ویرایش تنوع',
            'افزودن تنوع',
            'نمایش تنوع',
            'حذف تنوع',
            'ویرایش پرسش و پاسخ',
            'نمایش پرسش و پاسخ',
            'حذف پرسش و پاسخ',
            'ویرایش برچسب',
            'افزودن گارانتی',
            'حذف گارانتی',
            'ویرایش گارانتی',
            'افزودن امتیاز',
            'حذف امتیاز',
            'ویرایش امتیاز',
            'افزودن برچسب',
            'افزودن کد تخفیف',
            'نمایش برچسب های خودش',
            'نمایش همه برچسب ها',
            'حذف برچسب',
            'ویرایش دسته',
            'افزودن دسته',
            'نمایش دسته های خودش',
            'نمایش همه دسته ها',
            'حذف دسته',
            'ویرایش درخواست',
            'نمایش درخواست',
            'حذف درخواست',
            'نمایش بازدید',
            'حذف بازدید',
            'نمایش بازخورد',
            'حذف بازخورد',
            'نمایش اطلاع پست',
            'حذف اطلاع پست',
            'نمایش رویداد',
            'حذف رویداد',
            'ویرایش کالا',
            'افزودن کالا',
            'نمایش همه کالا ها',
            'نمایش کالا های خودش',
            'حذف کالا',
            'ویرایش خبر',
            'افزودن خبر',
            'نمایش همه خبر ها',
            'نمایش خبر های خودش',
            'حذف خبر',
            'افزودن مقام',
            'ویرایش مقام',
            'حذف مقام',
            'نمایش مقام',
            'نمایش مدارک',
            'نمایش تسویه حساب ها',
            'نمایش فروشنده',
            'افزودن کاربر',
            'ویرایش کاربر',
            'حذف کاربر',
            'نمایش همه کاربر ها',
            'نمایش کاربر های خودش',
            'افزودن رباط',
            'فروشنده',
            'ویرایش رباط',
            'حذف رباط',
            'نمایش همه رباط ها',
            'نمایش رباط های خودش',
            'افزودن اطلاع رسانی',
            'حذف اطلاع رسانی',
            'نمایش همه اطلاع رسانی ها',
            'نمایش اطلاع رسانی های خودش',
            'افزودن تصویر',
            'حذف تصویر',
            'نمایش همه تصویر ها',
            'نمایش تصویر های خودش',
            'نمایش داشبورد',
            'تنظیمات دسته بندی ها',
            'تنظیمات قالب سایت',
            'تنظیمات قالب سایت',
            'نمایش پرداختی',
            'نمایش انبارداری',
            'خروجی اکسل',
            'ورودی اکسل',
            'حذف پرداختی',
            'رتبه بندی',
            'ویرایش دیدگاه',
            'تنظیمات درگاه',
        ];
        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });
        foreach ($array as $item) {
            DB::table('permissions')->insert(
                array(
                    'name' => $item,
                    'guard_name' => 'web',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                )
            );
        }

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames) {
            $table->unsignedBigInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['role_id', $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}

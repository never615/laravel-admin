<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 项目主体
 * Class CreateSubjectsTable
 */
class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('主体名字');
            $table->unsignedInteger("parent_id")->nullable()->comment("父id");
            $table->string('logo')->nullable()->comment('logo');
            $table->text('description')->nullable()->comment('描述');
            $table->text('board')->nullable()->comment('公告');
            $table->text('detail')->nullable()->comment('详情,富文本');
            $table->json('tel')->nullable()->comment('电话,可能有多个');
            $table->string('address')->nullable()->comment('地址');
            $table->double('member_ratio')->nullable()->comment('会员活动时,调整整体积分比率,比如设置1.5倍');
            $table->text('member_interest')->nullable()->comment('会员权益,富文本');
            $table->text('park_info')->nullable()->comment('停车场规则');
            $table->json('point_register')->nullable()->comment('注册送积分.{"10":100}用json保存,此条数据表示,送10积分的概率为100.可以配置多条');
            $table->string('domain')->nullable()->comment('域名');
            $table->double('longitude')->nullable()->comment('主体gps坐标,经度');
            $table->double('latitude')->nullable()->comment('主体gps坐标,纬度');
            $table->double('longitude_db')->nullable()->comment('主体百度坐标,经度');
            $table->double('latitude_db')->nullable()->comment('主体百度坐标,纬度');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}

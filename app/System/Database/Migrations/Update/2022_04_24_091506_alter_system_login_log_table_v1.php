<?php

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class AlterSystemLoginLogTableV1 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_login_log', function (Blueprint $table) {
            $table->addColumn('tinyInteger', 'type', ['unsigned' => true, 'default' => 0])
                ->comment('日志类型 0后台日志 1api日志');
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_login_log', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
}

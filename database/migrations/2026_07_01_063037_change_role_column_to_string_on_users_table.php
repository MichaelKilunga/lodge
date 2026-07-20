<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('Customer')->change();
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'Customer'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('Customer')->change();
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super', 'Admin', 'Customer') NOT NULL DEFAULT 'Customer'");
        }
    }
};

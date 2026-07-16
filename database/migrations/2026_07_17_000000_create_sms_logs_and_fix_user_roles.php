<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient');
            $table->text('message');
            $table->string('status');
            $table->text('response')->nullable();
            $table->integer('character_count')->default(0);
            $table->integer('page_count')->default(0);
            $table->timestamps();
        });

        // Map existing users' role_id based on their string 'role' value if role_id is null
        $roles = DB::table('roles')->get();
        foreach ($roles as $role) {
            DB::table('users')
                ->whereNull('role_id')
                ->where('role', $role->name)
                ->update(['role_id' => $role->id]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};

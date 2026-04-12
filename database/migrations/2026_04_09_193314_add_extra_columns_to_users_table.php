<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('contact_number')->nullable()->after('email');
            $table->string('postcode')->nullable()->after('contact_number');
            $table->string('gender')->nullable()->after('postcode');
            $table->json('hobbies')->nullable()->after('gender');
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete()->after('hobbies');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete()->after('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['first_name', 'last_name', 'contact_number', 'postcode', 'gender', 'hobbies', 'state_id', 'city_id']);
        });
    }
};

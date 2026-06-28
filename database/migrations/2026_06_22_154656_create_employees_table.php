<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("employees", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("position");
            $table->string("department")->nullable();
            $table->decimal("base_salary", 15, 2)->default(0);
            $table->date("join_date");
            $table->enum("status", ["active", "inactive"])->default("active");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("employees");
    }
};

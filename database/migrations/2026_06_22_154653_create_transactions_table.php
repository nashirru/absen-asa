<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("transactions", function (Blueprint $table) {
            $table->id();
            $table->enum("type", ["income", "expense", "transfer"]);
            $table->foreignId("account_id")->constrained()->cascadeOnDelete();
            $table->foreignId("category_id")->nullable()->constrained()->nullOnDelete();
            $table->decimal("amount", 15, 2);
            $table->text("description")->nullable();
            $table->date("date");
            $table->string("attachment")->nullable();
            $table->unsignedBigInteger("ref_payroll_id")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("transactions");
    }
};

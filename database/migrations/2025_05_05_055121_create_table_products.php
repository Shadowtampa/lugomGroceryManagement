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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->decimal('preco')->nullable();
            $table->integer('quantidade_estoque')->default(0);
            $table->string('foto')->nullable();
            $table->string('local_compra')->nullable();
            $table->string('departamento')->nullable();

            $table->string('local_casa')->nullable();


            $table->foreignId('families_id')->constrained()->onDelete('cascade'); // Relacionamento com usuários

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

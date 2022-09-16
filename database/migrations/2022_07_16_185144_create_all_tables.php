<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('1.jpg');
            $table->string('email')->unique();
            $table->string('password');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('banner');
            $table->text('description');
            $table->tinyInteger('visivel')->default(0);
            $table->string('slug')->unique();
            $table->dateTime('created_at');
            $table->dateTime('publish');
            $table->string('titulo_pagina');
            $table->string('descricao_pagina');
            $table->string('titulo_compartilhamento');
            $table->string('descricao_compartilhamento');
            $table->tinyInteger('posicao')->nullable();

        });

         Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('banner');
            $table->string('capa');
            $table->text('description');
            $table->tinyInteger('visivel')->default(0);
            $table->string('slug')->unique();
            $table->string('titulo_pagina');
            $table->string('descricao_pagina');
            $table->string('titulo_compartilhamento');
            $table->string('descricao_compartilhamento');
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('created_at');

        });

        Schema::create('imagens', function (Blueprint $table) {
            $table->id();
            $table->string('imagem');
            $table->unsignedBigInteger('product_id')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('visivel')->default(0);
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('created_at');

        });

        Schema::create('categories_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('visivel')->default(0);
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('created_at');

        });

        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('categorie_product_id')->nullable();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('category_id')
            ->references('id')
            ->on('categories')
            ->onDelete('set null');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('categorie_product_id')
            ->references('id')
            ->on('categories_products')
            ->onDelete('set null');
        });


        Schema::table('imagens', function (Blueprint $table) {
            $table->foreign('product_id')
            ->references('id')
            ->on('products')
            ->onDelete('set null');
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('controladora');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('imagem')->nullable();
            $table->string('imagem_responsive')->nullable();
            $table->unsignedBigInteger('largura_imagem')->nullable();
            $table->unsignedBigInteger('altura_imagem')->nullable();
            $table->unsignedBigInteger('largura_imagem_responsive')->nullable();
            $table->unsignedBigInteger('altura_imagem_responsive')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::create('parameters_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('title');
            $table->unsignedBigInteger('description');
            $table->unsignedBigInteger('imagem');
            $table->unsignedBigInteger('imagem_responsive');

        });

        Schema::table('parameters_contents', function (Blueprint $table) {
            $table->unsignedBigInteger('content_id')->nullable();
        });

        Schema::table('parameters_contents', function (Blueprint $table) {
            $table->foreign('content_id')
            ->references('id')
            ->on('contents')
            ->onDelete('set null');
        });

        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('imagem');
            $table->string('imagem_responsive')->nullable();
            $table->tinyInteger('visivel')->default(0);
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('created_at');
        });

        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('controladora');
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->text('titulo_compartilhamento')->nullable();
            $table->text('descricao_compartilhamento')->nullable();
            $table->string('imagem');
            $table->dateTime('criado')->nullable();
        });

        Schema::create('testes', function (Blueprint $table) {
            $table->id();
        });

         Schema::create('idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codigo');
            $table->string('icone');
            $table->unsignedBigInteger('padrao')->default(0);
            $table->dateTime('criado')->nullable();

        });

        Schema::create('teste_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('descricao');
           
        });

        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->unsignedBigInteger('teste_id')->nullable();
        });

        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->unsignedBigInteger('idioma_id')->nullable();
        });

        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->foreign('teste_id')
            ->references('id')
            ->on('testes')
            ->onDelete('set null');
        });

        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('imagens');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('parameters_contents');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('categories_products');
        Schema::dropIfExists('slides');
        Schema::dropIfExists('testes');
        Schema::dropIfExists('idiomas');
        Schema::dropIfExists('teste_idiomas');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('categorie_product_id');
        });
        Schema::table('imagens', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
        Schema::table('parameters_contents', function (Blueprint $table) {
            $table->dropColumn('content_id');
        });
        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->dropColumn('teste_id');
        });
        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->dropColumn('idioma_id');
        });
    }
}

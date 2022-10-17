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

        Schema::create('idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('codigo');
            $table->string('icone');
            $table->dateTime('criado')->nullable();

        });

      
        //categorias posts

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('visivel')->default(0);
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('criado');

        });

        Schema::create('categoria_post_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::table('categoria_post_idiomas', function (Blueprint $table) {
            $table->foreign('id_categoria')
            ->references('id')
            ->on('categorias')
            ->onDelete('set null');
        });

        Schema::table('categoria_post_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });


        //categorias produtos

        Schema::create('categorias_produtos', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('visivel')->default(0);
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('criado');

        });

         Schema::create('categoria_produto_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::table('categoria_produto_idiomas', function (Blueprint $table) {
            $table->foreign('id_categoria')
            ->references('id')
            ->on('categorias_produtos')
            ->onDelete('set null');
        });

        Schema::table('categoria_produto_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });

        //POSTS

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('banner');
            $table->tinyInteger('visivel')->default(0);
            $table->string('slug')->unique();
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->dateTime('publicado');
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('criado');

        });

        Schema::create('post_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->string('titulo_pagina');
            $table->string('descricao_pagina');
            $table->string('titulo_compartilhamento');
            $table->string('descricao_compartilhamento');
            $table->unsignedBigInteger('post_id')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::table('posts', function (Blueprint $table) {
           
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('id_categoria')
            ->references('id')
            ->on('categorias')
            ->onDelete('set null');
        });

        Schema::table('post_idiomas', function (Blueprint $table) {
            $table->foreign('post_id')
            ->references('id')
            ->on('posts')
            ->onDelete('set null');
        });

        Schema::table('post_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });

       
        //PRODUTOS

        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('banner');
            $table->string('capa');
            $table->tinyInteger('visivel')->default(0);
            $table->string('slug')->unique();
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('criado');

        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_categoria')->nullable();
        });

         Schema::create('produto_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->string('titulo_pagina');
            $table->string('descricao_pagina');
            $table->string('titulo_compartilhamento');
            $table->string('descricao_compartilhamento');
            $table->unsignedBigInteger('produto_id')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->foreign('id_categoria')
            ->references('id')
            ->on('categorias_produtos')
            ->onDelete('set null');
        });

        Schema::table('produto_idiomas', function (Blueprint $table) {
            $table->foreign('produto_id')
            ->references('id')
            ->on('produtos')
            ->onDelete('set null');
        });

        Schema::table('produto_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });

        //IMAGENS PRODUTOS

        Schema::create('imagens', function (Blueprint $table) {
            $table->id();
            $table->string('imagem');
            $table->unsignedBigInteger('produto_id')->nullable();
        });

        Schema::table('imagens', function (Blueprint $table) {
            $table->foreign('produto_id')
            ->references('id')
            ->on('produtos')
            ->onDelete('set null');
        });


        //CONTEUDOS

        Schema::create('conteudos', function (Blueprint $table) {
            $table->id();
            $table->string('controladora');
            $table->string('imagem')->nullable();
            $table->string('imagem_responsive')->nullable();
            $table->unsignedBigInteger('largura_imagem')->nullable();
            $table->unsignedBigInteger('altura_imagem')->nullable();
            $table->unsignedBigInteger('largura_imagem_responsive')->nullable();
            $table->unsignedBigInteger('altura_imagem_responsive')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::create('conteudo_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('conteudo_id')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::table('conteudo_idiomas', function (Blueprint $table) {
            $table->foreign('conteudo_id')
            ->references('id')
            ->on('conteudos')
            ->onDelete('set null');
        });

        Schema::table('conteudo_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });


        //PARAMETRO CONTEUDO

        Schema::create('parametros_conteudos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('titulo');
            $table->unsignedBigInteger('descricao');
            $table->unsignedBigInteger('imagem');
            $table->unsignedBigInteger('imagem_responsive');

        });

        Schema::table('parametros_conteudos', function (Blueprint $table) {
            $table->unsignedBigInteger('conteudo_id')->nullable();
        });

        Schema::table('parametros_conteudos', function (Blueprint $table) {
            $table->foreign('conteudo_id')
            ->references('id')
            ->on('conteudos')
            ->onDelete('set null');
        });


        //SLIDES

        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('imagem');
            $table->string('imagem_responsive')->nullable();
            $table->tinyInteger('visivel')->default(0);
            $table->tinyInteger('posicao')->nullable();
            $table->dateTime('criado');
        });

        Schema::create('slide_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->unsignedBigInteger('slide_id')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->dateTime('criado')->nullable();
        });

        Schema::table('slide_idiomas', function (Blueprint $table) {
            $table->foreign('slide_id')
            ->references('id')
            ->on('slides')
            ->onDelete('set null');
        });

        Schema::table('slide_idiomas', function (Blueprint $table) {
            $table->foreign('idioma_id')
            ->references('id')
            ->on('idiomas')
            ->onDelete('set null');
        });


        //TABELA PAGINAS

        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('controladora');
            $table->string('imagem');
            $table->dateTime('criado')->nullable();
        });

        Schema::create('pagina_idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->text('titulo_compartilhamento')->nullable();
            $table->text('descricao_compartilhamento')->nullable();
            $table->unsignedBigInteger('pagina_id')->nullable();
            $table->unsignedBigInteger('idioma_id')->nullable();
            $table->dateTime('criado')->nullable();
        });
       
        Schema::table('pagina_idiomas', function (Blueprint $table) {
            $table->foreign('pagina_id')
            ->references('id')
            ->on('paginas')
            ->onDelete('set null');
        });

        Schema::table('pagina_idiomas', function (Blueprint $table) {
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
        Schema::dropIfExists('idiomas');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_idiomas');
        Schema::dropIfExists('produtos');
        Schema::dropIfExists('produto_idiomas');
        Schema::dropIfExists('imagens');
        Schema::dropIfExists('conteudos');
        Schema::dropIfExists('conteudo_idiomas');
        Schema::dropIfExists('parametros_conteudos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('categoria_post_idiomas');
        Schema::dropIfExists('categorias_produtos');
        Schema::dropIfExists('categoria_produto_idiomas');
        Schema::dropIfExists('slides');
        Schema::dropIfExists('slide_idiomas');
        Schema::dropIfExists('testes');
        Schema::dropIfExists('teste_idiomas');
        Schema::dropIfExists('paginas');
        Schema::dropIfExists('pagina_idiomas');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('id_categoria');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('id_categoria');
        });
        Schema::table('imagens', function (Blueprint $table) {
            $table->dropColumn('produto_id');
        });
        Schema::table('parameters_contents', function (Blueprint $table) {
            $table->dropColumn('conteudo_id');
        });
        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->dropColumn('teste_id');
        });
        Schema::table('teste_idiomas', function (Blueprint $table) {
            $table->dropColumn('idioma_id');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('category_uuid');
            $table->foreign('category_uuid')->references('uuid')->on('categories')->cascadeOnDelete();
            $table->string('uuid')->index();
            $table->string('title');
            $table->float('price', 10, 2);
            $table->text('description');
            $table->json('metadata');

            $brand = DB::connection()->getQueryGrammar()->wrap('metadata->brand');
            $table->string('brand')->storedAs($brand);
            $table->foreign('brand')->references('uuid')->on('brands');

            $image = DB::connection()->getQueryGrammar()->wrap('metadata->image');
            $table->string('image')->storedAs($image);
            $table->foreign('image')->references('uuid')->on('files');

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
        Schema::dropIfExists('products');
    }
}

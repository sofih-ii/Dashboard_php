<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class home extends Model
{
    public function up()
{
    Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->string('producto');
        $table->integer('cantidad');
        $table->decimal('precio', 10, 2);
        $table->date('fecha');
        $table->timestamps();
    });
}
}

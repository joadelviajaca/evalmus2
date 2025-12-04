<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RubricSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rubric = Rubric::create([
            'title' => 'Rúbrica ejemplo',
            'description' => 'Rúbrica de muestra para pruebas',
        ]);

        $c1 = Criterion::create([
            'rubric_id' => $rubric->id,
            'title' => 'Calidad técnica',
            'description' => 'Calidad del código y solución',
            'weight' => 50,
            'order' => 1,
        ]);

        CriterionLevel::insert([
            ['criterion_id'=>$c1->id,'label'=>'Excelente','description'=>'Muy por encima','value'=>5,'order'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['criterion_id'=>$c1->id,'label'=>'Bueno','description'=>'Cumple bien','value'=>4,'order'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['criterion_id'=>$c1->id,'label'=>'Regular','description'=>'Mejorable','value'=>3,'order'=>3,'created_at'=>now(),'updated_at'=>now()],
        ]);

        $c2 = Criterion::create([
            'rubric_id' => $rubric->id,
            'title' => 'Presentación',
            'description' => 'Claridad y estructura',
            'weight' => 50,
            'order' => 2,
        ]);

        CriterionLevel::insert([
            ['criterion_id'=>$c2->id,'label'=>'Excelente','description'=>'Muy claro','value'=>5,'order'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['criterion_id'=>$c2->id,'label'=>'Suficiente','description'=>'Aceptable','value'=>3,'order'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['criterion_id'=>$c2->id,'label'=>'Insuficiente','description'=>'No cumple','value'=>1,'order'=>3,'created_at'=>now(),'updated_at'=>now()],
        ]);

    }
}

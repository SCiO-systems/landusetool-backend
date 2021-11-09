<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::truncate();

        $project1 = Project::create([
            'title' => 'Project 1',
            'acronym' => 'PRJCT1',
            'description' => 'A sample description.',
        ]);

        $project1->setOwner(1);
        $project1->addUser(2);

        $project2 = Project::create([
            'title' => 'Project 2',
            'acronym' => 'PRJCT2',
            'description' => 'A sample description.',
        ]);

        $project2->setOwner(2);
        $project2->addUser(1);
    }
}

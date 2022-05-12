<?php

use App\Jobs\ConsultComplaints;
use Illuminate\Database\Seeder;

class RunGetComplainsJob extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConsultComplaints::dispatchNow();
    }
}

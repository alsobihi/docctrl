<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plant;
use App\Models\Project;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Models\DocumentTemplate;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user to associate created records with
        $admin = User::where('email', 'alsobihi.ai@gmail.com')->first();
        if (!$admin) {
            $this->command->error('Admin user not found. Please run the UserSeeder first.');
            return;
        }


        // --- Create Plants ---
        $plant1 = Plant::updateOrCreate(['name' => 'Dammam Fabrication Yard'], ['location' => 'Dammam', 'created_by' => $admin->id]);
        $plant2 = Plant::updateOrCreate(['name' => 'Jubail Site Office'], ['location' => 'Jubail', 'created_by' => $admin->id]);
        $plants = Plant::all();

        // --- Create Projects ---
        $project1 = Project::updateOrCreate(['project_code' => 'APP-001'], ['name' => 'Aramco Pipeline Project', 'plant_id' => $plant1->id, 'created_by' => $admin->id]);
        $project2 = Project::updateOrCreate(['project_code' => 'NEOM-INF-01'], ['name' => 'NEOM Infrastructure', 'plant_id' => $plant2->id, 'created_by' => $admin->id]);

        // --- Create Document Templates ---
        $passportTemplate = DocumentTemplate::updateOrCreate(
            ['name' => 'Passport Details'],
            [
                'fields' => [
                    ['label' => 'Passport Number', 'name' => 'passport_number', 'type' => 'text'],
                    ['label' => 'Country of Issue', 'name' => 'country', 'type' => 'text'],
                ],
                'created_by' => $admin->id,
            ]
        );

        // --- Create Document Types ---
        $passDocType = DocumentType::updateOrCreate(['name' => 'Passport'], ['category' => 'Personal', 'template_id' => $passportTemplate->id, 'validity_rule' => ['type' => 'fixed', 'days' => 3650], 'warning_period_days' => 180, 'created_by' => $admin->id]);
        $idDocType = DocumentType::updateOrCreate(['name' => 'National ID / Iqama'], ['category' => 'Personal', 'validity_rule' => ['type' => 'fixed', 'days' => 1825], 'warning_period_days' => 90, 'created_by' => $admin->id]);
        $licenseDocType = DocumentType::updateOrCreate(['name' => 'Driving License'], ['category' => 'Personal', 'validity_rule' => ['type' => 'fixed', 'days' => 3650], 'warning_period_days' => 90, 'created_by' => $admin->id]);
        $aramcoIdDocType = DocumentType::updateOrCreate(['name' => 'Aramco ID'], ['category' => 'Project', 'validity_rule' => ['type' => 'dependent', 'logic' => 'min', 'dependencies' => [['document_type_id' => $idDocType->id]]], 'warning_period_days' => 60, 'created_by' => $admin->id]);
        $docTypes = DocumentType::all();

        // --- Create a large number of dummy employees and documents ---
        $this->command->info('Creating 300 dummy employees...');
        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 300; $i++) {
            $employee = Employee::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'employee_code' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'plant_id' => $faker->randomElement($plants)->id,
                'created_by' => $admin->id,
            ]);

            // Assign a random subset of documents to each employee
            if ($docTypes->isNotEmpty()) {
                foreach ($docTypes->random(rand(1, $docTypes->count())) as $docType) {
                    $issueDate = Carbon::now()->subDays(rand(1, 365 * 5));
                    $expiryDate = null;

                    if (isset($docType->validity_rule['type']) && $docType->validity_rule['type'] === 'fixed') {
                        $expiryDate = $issueDate->copy()->addDays($docType->validity_rule['days']);
                    } else {
                        // For dependent or no rule, let's just set a random future date for testing
                        // This will create a mix of valid, expiring, and expired documents.
                        $expiryDate = Carbon::now()->addDays(rand(-90, 1825));
                    }

                    $employee->documents()->create([
                        'document_type_id' => $docType->id,
                        'issue_date' => $issueDate,
                        'expiry_date' => $expiryDate,
                        'file_path' => 'dummy/file.pdf',
                        'created_by' => $admin->id,
                    ]);
                }
            }
        }


        $this->command->info('Dummy data has been seeded successfully!');
    }
}

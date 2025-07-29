<?php

namespace Database\Seeders;

use App\Models\TrainingEnrollment;
use App\Models\Training;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TrainingEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $trainings = Training::all();
        $employees = Employee::all();

        $enrollments = [];

        // Create varied enrollments for each training
        foreach ($trainings as $training) {
            // Randomly select 5-15 employees for each training
            $participantCount = rand(5, min(15, $employees->count()));
            $selectedEmployees = $employees->random($participantCount);

            foreach ($selectedEmployees as $employee) {
                $enrolledAt = $faker->dateTimeBetween('-6 months', 'now');
                $status = $faker->randomElement(['enrolled', 'attended', 'completed', 'cancelled']);
                
                $enrollment = [
                    'training_id' => $training->id,
                    'employee_id' => $employee->id,
                    'status' => $status,
                    'enrolled_at' => $enrolledAt,
                ];

                // Add completion data if status is completed
                if ($status === 'completed') {
                    $enrollment['completed_at'] = $faker->dateTimeBetween($enrolledAt, 'now');
                    $enrollment['attended_at'] = $faker->dateTimeBetween($enrolledAt, $enrollment['completed_at']);
                    $enrollment['is_certified'] = $faker->boolean(70); // 70% chance of certification
                    $enrollment['evaluation_data'] = json_encode([
                        'completion_score' => $faker->randomFloat(2, 60, 100),
                        'feedback' => $faker->randomElement([
                            'Excellent training, very informative',
                            'Good content, well presented',
                            'Very helpful for my role',
                            'Clear explanations and practical examples',
                            'Engaging instructor and interactive sessions',
                            'Valuable insights and applicable knowledge',
                            'Well structured and comprehensive'
                        ]),
                        'rating' => $faker->numberBetween(3, 5)
                    ]);
                }

                // Add cancellation data if status is cancelled
                if ($status === 'cancelled') {
                    // Just use notes for cancellation reason
                    $enrollment['notes'] = 'Training cancelled by participant';
                }

                $enrollments[] = $enrollment;
            }
        }

        // Additional specific enrollments to ensure data variety
        $specificEnrollments = [
            [
                'training_id' => $trainings->first()->id,
                'employee_id' => $employees->first()->id,
                'status' => 'completed',
                'enrolled_at' => now()->subDays(30),
                'attended_at' => now()->subDays(28),
                'completed_at' => now()->subDays(25),
                'is_certified' => true,
                'evaluation_data' => json_encode([
                    'completion_score' => 95.5,
                    'feedback' => 'Outstanding training program. Exceeded my expectations!',
                    'rating' => 5
                ])
            ],
            [
                'training_id' => $trainings->skip(1)->first()->id,
                'employee_id' => $employees->skip(1)->first()->id,
                'status' => 'completed',
                'enrolled_at' => now()->subDays(45),
                'attended_at' => now()->subDays(43),
                'completed_at' => now()->subDays(40),
                'is_certified' => true,
                'evaluation_data' => json_encode([
                    'completion_score' => 87.0,
                    'feedback' => 'Very practical and relevant to daily work tasks.',
                    'rating' => 4
                ])
            ],
            [
                'training_id' => $trainings->skip(2)->first()->id,
                'employee_id' => $employees->skip(2)->first()->id,
                'status' => 'enrolled',
                'enrolled_at' => now()->subDays(15),
            ],
            [
                'training_id' => $trainings->skip(3)->first()->id,
                'employee_id' => $employees->skip(3)->first()->id,
                'status' => 'cancelled',
                'enrolled_at' => now()->subDays(20),
            ],
            [
                'training_id' => $trainings->skip(4)->first()->id,
                'employee_id' => $employees->skip(4)->first()->id,
                'status' => 'completed',
                'enrolled_at' => now()->subDays(60),
                'attended_at' => now()->subDays(58),
                'completed_at' => now()->subDays(55),
                'is_certified' => true,
                'evaluation_data' => json_encode([
                    'completion_score' => 92.5,
                    'feedback' => 'Comprehensive course with excellent real-world examples.',
                    'rating' => 5
                ])
            ]
        ];

        // Insert all enrollments
        foreach (array_merge($enrollments, $specificEnrollments) as $enrollment) {
            // Check if enrollment already exists to avoid duplicates
            $exists = TrainingEnrollment::where('training_id', $enrollment['training_id'])
                ->where('employee_id', $enrollment['employee_id'])
                ->exists();
                
            if (!$exists) {
                TrainingEnrollment::create($enrollment);
            }
        }
    }
}

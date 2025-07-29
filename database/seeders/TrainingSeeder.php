<?php

namespace Database\Seeders;

use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user to be the creator
        $adminUser = User::where('email', 'admin@company.com')->first();
        $hrUser = User::where('email', 'hr@company.com')->first();

        // Fallback to first available user if admin not found
        if (!$adminUser) {
            $adminUser = User::first();
        }
        if (!$hrUser) {
            $hrUser = User::first();
        }

        $trainings = [
            [
                'title' => 'Web Development Fundamentals',
                'description' => 'Learn the basics of HTML, CSS, and JavaScript. This comprehensive course covers front-end web development essentials including responsive design, DOM manipulation, and modern JavaScript features.',
                'trainer_name' => 'John Smith',
                'trainer_email' => 'john.smith@company.com',
                'training_date' => '2024-03-15',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'location' => 'Training Room A',
                'max_participants' => 20,
                'created_by' => $adminUser->id,
                'additional_info' => json_encode(['duration_hours' => 8, 'category' => 'Technical'])
            ],
            [
                'title' => 'Database Management with MySQL',
                'description' => 'Master MySQL database administration, query optimization, and advanced database design principles. Includes hands-on practice with real-world scenarios.',
                'trainer_name' => 'Sarah Johnson',
                'trainer_email' => 'sarah.johnson@company.com',
                'training_date' => '2024-04-10',
                'start_time' => '10:00:00',
                'end_time' => '16:00:00',
                'location' => 'Computer Lab 1',
                'max_participants' => 15,
                'created_by' => $adminUser->id,
                'additional_info' => json_encode(['duration_hours' => 6, 'category' => 'Database'])
            ],
            [
                'title' => 'Leadership and Team Management',
                'description' => 'Develop essential leadership skills, learn effective team communication strategies, and understand how to motivate and manage diverse teams in modern workplace environments.',
                'trainer_name' => 'Michael Brown',
                'trainer_email' => 'michael.brown@company.com',
                'training_date' => '2024-05-20',
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'location' => 'Conference Room B',
                'max_participants' => 25,
                'created_by' => $hrUser->id,
                'additional_info' => json_encode(['duration_hours' => 4, 'category' => 'Soft Skills'])
            ],
            [
                'title' => 'Cybersecurity Awareness',
                'description' => 'Essential cybersecurity training covering threat identification, password security, phishing prevention, and best practices for data protection in corporate environments.',
                'trainer_name' => 'Emily Davis',
                'trainer_email' => 'emily.davis@company.com',
                'training_date' => '2024-06-08',
                'start_time' => '14:00:00',
                'end_time' => '17:00:00',
                'location' => 'Main Auditorium',
                'max_participants' => 30,
                'created_by' => $adminUser->id,
                'additional_info' => json_encode(['duration_hours' => 3, 'category' => 'Security'])
            ],
            [
                'title' => 'Project Management Essentials',
                'description' => 'Introduction to project management methodologies including Agile, Scrum, and Waterfall. Learn project planning, risk management, and stakeholder communication.',
                'trainer_name' => 'David Wilson',
                'trainer_email' => 'david.wilson@company.com',
                'training_date' => '2024-07-15',
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'location' => 'Training Room C',
                'max_participants' => 18,
                'created_by' => $hrUser->id,
                'additional_info' => json_encode(['duration_hours' => 7, 'category' => 'Management'])
            ],
            [
                'title' => 'Financial Analysis and Reporting',
                'description' => 'Advanced financial analysis techniques, budget planning, financial reporting standards, and using Excel for financial modeling and data analysis.',
                'trainer_name' => 'Jessica Garcia',
                'trainer_email' => 'jessica.garcia@company.com',
                'training_date' => '2024-08-22',
                'start_time' => '10:00:00',
                'end_time' => '15:00:00',
                'location' => 'Finance Department',
                'max_participants' => 12,
                'created_by' => $adminUser->id,
                'additional_info' => json_encode(['duration_hours' => 5, 'category' => 'Finance'])
            ],
            [
                'title' => 'Digital Marketing Strategy',
                'description' => 'Comprehensive digital marketing training covering SEO, social media marketing, content strategy, email marketing, and analytics tools for measuring campaign success.',
                'trainer_name' => 'Daniel Martinez',
                'trainer_email' => 'daniel.martinez@company.com',
                'training_date' => '2024-09-10',
                'start_time' => '09:00:00',
                'end_time' => '15:00:00',
                'location' => 'Marketing Hub',
                'max_participants' => 22,
                'created_by' => $hrUser->id,
                'additional_info' => json_encode(['duration_hours' => 6, 'category' => 'Marketing'])
            ],
            [
                'title' => 'Cloud Computing with AWS',
                'description' => 'Introduction to Amazon Web Services, cloud architecture principles, EC2, S3, and basic DevOps practices for cloud deployment and management.',
                'trainer_name' => 'Amanda Anderson',
                'trainer_email' => 'amanda.anderson@company.com',
                'training_date' => '2024-10-05',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'location' => 'IT Lab',
                'max_participants' => 16,
                'created_by' => $adminUser->id,
                'additional_info' => json_encode(['duration_hours' => 8, 'category' => 'Cloud'])
            ]
        ];

        foreach ($trainings as $training) {
            Training::create($training);
        }
    }
}

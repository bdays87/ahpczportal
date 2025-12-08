<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $journals = [
            [
                'title' => 'Advances in Healthcare Technology: A Comprehensive Review',
                'author' => 'Dr. Sarah Johnson',
                'published_date' => '2025-01-20',
                'link' => 'https://example.com/journals/healthcare-technology-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Professional Ethics in Modern Healthcare Practice',
                'author' => 'Prof. Michael Chen',
                'published_date' => '2025-02-15',
                'link' => 'https://example.com/journals/professional-ethics-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Continuing Professional Development: Best Practices',
                'author' => 'Dr. Emily Rodriguez',
                'published_date' => '2025-03-10',
                'link' => 'https://example.com/journals/cpd-best-practices-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Patient Safety and Quality Improvement Initiatives',
                'author' => 'Dr. James Wilson',
                'published_date' => '2025-04-05',
                'link' => 'https://example.com/journals/patient-safety-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Digital Health Transformation: Opportunities and Challenges',
                'author' => 'Dr. Lisa Anderson',
                'published_date' => '2025-05-18',
                'link' => 'https://example.com/journals/digital-health-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Evidence-Based Practice in Clinical Decision Making',
                'author' => 'Prof. David Thompson',
                'published_date' => '2025-06-12',
                'link' => 'https://example.com/journals/evidence-based-practice-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Interprofessional Collaboration in Healthcare Teams',
                'author' => 'Dr. Maria Garcia',
                'published_date' => '2025-07-25',
                'link' => 'https://example.com/journals/interprofessional-collaboration-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Mental Health and Wellbeing in Healthcare Professionals',
                'author' => 'Dr. Robert Brown',
                'published_date' => '2025-08-08',
                'link' => 'https://example.com/journals/mental-health-healthcare-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Regulatory Compliance and Professional Standards',
                'author' => 'Dr. Jennifer Lee',
                'published_date' => '2025-09-14',
                'link' => 'https://example.com/journals/regulatory-compliance-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Innovation in Healthcare Delivery Models',
                'author' => 'Prof. Christopher Martinez',
                'published_date' => '2025-10-30',
                'link' => 'https://example.com/journals/healthcare-delivery-innovation-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Research Methods and Clinical Studies in Healthcare',
                'author' => 'Dr. Amanda White',
                'published_date' => '2025-11-22',
                'link' => 'https://example.com/journals/research-methods-healthcare-2025',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Future of Healthcare: Trends and Predictions for 2026',
                'author' => 'Dr. Kevin Taylor',
                'published_date' => '2025-12-10',
                'link' => 'https://example.com/journals/future-healthcare-2026',
                'created_by' => $admin?->id,
            ],
        ];

        foreach ($journals as $journal) {
            Journal::create($journal);
        }
    }
}

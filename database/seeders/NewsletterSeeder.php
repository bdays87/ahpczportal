<?php

namespace Database\Seeders;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();

        $newsletters = [
            [
                'title' => 'Welcome to Our Professional Portal - January 2025',
                'link' => 'https://example.com/newsletters/january-2025',
                'published_date' => '2025-01-15',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Continuing Professional Development Updates - February 2025',
                'link' => 'https://example.com/newsletters/february-2025',
                'published_date' => '2025-02-10',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Registration Renewal Reminder - March 2025',
                'link' => 'https://example.com/newsletters/march-2025',
                'published_date' => '2025-03-05',
                'is_broadcasted' => false,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'New Regulations and Compliance Updates - April 2025',
                'link' => 'https://example.com/newsletters/april-2025',
                'published_date' => '2025-04-12',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Professional Excellence Awards - May 2025',
                'link' => 'https://example.com/newsletters/may-2025',
                'published_date' => '2025-05-20',
                'is_broadcasted' => false,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Summer Training Programs - June 2025',
                'link' => 'https://example.com/newsletters/june-2025',
                'published_date' => '2025-06-08',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Mid-Year Review and Statistics - July 2025',
                'link' => 'https://example.com/newsletters/july-2025',
                'published_date' => '2025-07-15',
                'is_broadcasted' => false,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Technology Integration in Healthcare - August 2025',
                'link' => 'https://example.com/newsletters/august-2025',
                'published_date' => '2025-08-22',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Ethics and Professional Conduct - September 2025',
                'link' => 'https://example.com/newsletters/september-2025',
                'published_date' => '2025-09-10',
                'is_broadcasted' => false,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Annual Conference Announcement - October 2025',
                'link' => 'https://example.com/newsletters/october-2025',
                'published_date' => '2025-10-05',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Year-End Review and 2026 Preview - November 2025',
                'link' => 'https://example.com/newsletters/november-2025',
                'published_date' => '2025-11-18',
                'is_broadcasted' => false,
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Holiday Greetings and Year-End Message - December 2025',
                'link' => 'https://example.com/newsletters/december-2025',
                'published_date' => '2025-12-15',
                'is_broadcasted' => true,
                'created_by' => $admin?->id,
            ],
        ];

        foreach ($newsletters as $newsletter) {
            Newsletter::create($newsletter);
        }
    }
}

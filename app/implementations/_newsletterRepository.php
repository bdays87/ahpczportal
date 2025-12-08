<?php

namespace App\implementations;

use App\Interfaces\inewsletterInterface;
use App\Models\Customer;
use App\Models\Newsletter;
use App\Notifications\NewsletterNotification;
use Illuminate\Support\Facades\Auth;

class _newsletterRepository implements inewsletterInterface
{
    protected $newsletter;

    protected $customer;

    public function __construct(Newsletter $newsletter, Customer $customer)
    {
        $this->newsletter = $newsletter;
        $this->customer = $customer;
    }

    public function getAll($year = null)
    {
        $query = $this->newsletter
            ->with('creator');

        if ($year) {
            $query->whereYear('published_date', $year);
        } else {
            $query->whereYear('published_date', date('Y'));
        }

        return $query
            ->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getLatest($limit = 5)
    {
        return $this->newsletter
            ->with('creator')
            ->whereYear('published_date', date('Y'))
            ->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function get($id)
    {
        return $this->newsletter->with('creator')->find($id);
    }

    public function create(array $data)
    {
        try {
            $data['created_by'] = Auth::id();

            $newsletter = $this->newsletter->create($data);

            return ['status' => 'success', 'message' => 'Newsletter created successfully', 'data' => $newsletter];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $newsletter = $this->newsletter->find($id);
            if (! $newsletter) {
                return ['status' => 'error', 'message' => 'Newsletter not found'];
            }

            $newsletter->update($data);

            return ['status' => 'success', 'message' => 'Newsletter updated successfully', 'data' => $newsletter];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete(int $id)
    {
        try {
            $newsletter = $this->newsletter->find($id);
            if (! $newsletter) {
                return ['status' => 'error', 'message' => 'Newsletter not found'];
            }

            $newsletter->delete();

            return ['status' => 'success', 'message' => 'Newsletter deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function broadcast(int $id)
    {
        try {
            $newsletter = $this->newsletter->find($id);
            if (! $newsletter) {
                return ['status' => 'error', 'message' => 'Newsletter not found'];
            }

            if ($newsletter->is_broadcasted) {
                return ['status' => 'error', 'message' => 'Newsletter has already been broadcasted'];
            }

            // Get all customers with email addresses
            $customers = $this->customer->whereNotNull('email')->get();

            $sentCount = 0;
            $failedCount = 0;

            foreach ($customers as $customer) {
                try {
                    // Get user associated with customer
                    $user = $customer->customeruser?->user;
                    if ($user) {
                        $user->notify(new NewsletterNotification($newsletter, $customer));
                        $sentCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                }
            }

            // Mark newsletter as broadcasted
            $newsletter->update(['is_broadcasted' => true]);

            return [
                'status' => 'success',
                'message' => "Newsletter broadcasted successfully. Sent: {$sentCount}, Failed: {$failedCount}",
                'sent' => $sentCount,
                'failed' => $failedCount,
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

<?php

namespace App\implementations;

use App\Interfaces\ijournalInterface;
use App\Models\Journal;
use Illuminate\Support\Facades\Auth;

class _journalRepository implements ijournalInterface
{
    protected $journal;

    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }

    public function getAll($search = null)
    {
        return $this->journal
            ->with('creator')
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                        ->orWhere('author', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getLatest($limit = 5)
    {
        return $this->journal
            ->with('creator')
            ->orderBy('published_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function get($id)
    {
        return $this->journal->with('creator')->find($id);
    }

    public function getBySlug($slug)
    {
        return $this->journal->with('creator')->where('slug', $slug)->first();
    }

    public function create(array $data)
    {
        try {
            $data['created_by'] = Auth::id();

            // Generate slug if not provided
            if (empty($data['slug']) && ! empty($data['title'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
            }

            $journal = $this->journal->create($data);

            return ['status' => 'success', 'message' => 'Journal created successfully', 'data' => $journal];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $journal = $this->journal->find($id);
            if (! $journal) {
                return ['status' => 'error', 'message' => 'Journal not found'];
            }

            // Generate slug if title changed and slug not provided
            if (isset($data['title']) && $journal->title !== $data['title'] && empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
            }

            $journal->update($data);

            return ['status' => 'success', 'message' => 'Journal updated successfully', 'data' => $journal];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete(int $id)
    {
        try {
            $journal = $this->journal->find($id);
            if (! $journal) {
                return ['status' => 'error', 'message' => 'Journal not found'];
            }

            $journal->delete();

            return ['status' => 'success', 'message' => 'Journal deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

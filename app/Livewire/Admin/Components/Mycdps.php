<?php

namespace App\Livewire\Admin\Components;

use App\Interfaces\imycdpInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

class Mycdps extends Component
{
    use Toast,WithFileUploads;

    public $year;

    public $customerprofession;

    protected $mycdprepo;

    public $mycdp = null;

    public $modal = false;

    public $addmodal = false;

    public $points;

    public $title;

    public $description;

    public $type;

    public $duration;

    public $durationunit;

    public $id;

    public $file;

    public $attachmentmodal = false;

    public $attachments = [];

    public $documenturl;

    public $viewattachmentmodal = false;

    public $viewmodal = false;

    public $customerprofession_id;

    public function mount($customerprofession)
    {
        $this->customerprofession = $customerprofession;
        $this->year = date('Y');
        $this->points = new Collection;
    }

    public function boot(imycdpInterface $mcdprepo)
    {
        $this->mycdprepo = $mcdprepo;
    }

    public function getdata()
    {
        if ($this->customerprofession) {
            $data = $this->mycdprepo->getbycustomerprofession($this->customerprofession->id, $this->customerprofession->applications->last()->year);
            $this->points = $data;

        }
    }

    public function getcdps()
    {
        $this->getdata();
        $this->viewmodal = true;

    }

    public function save()
    {
        $this->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'duration' => 'required',
            'durationunit' => 'required',
        ]);
        if ($this->id) {
            $this->update();
        } else {
            $this->create();
        }

        $this->reset('title', 'description', 'type', 'duration', 'durationunit');
        $this->addmodal = false;
    }

    public function create()
    {
        $respomse = $this->mycdprepo->create([
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'duration' => $this->duration,
            'durationunit' => $this->durationunit,
            'customerprofession_id' => $this->customerprofession->id,
            'year' => $this->year,
            'user_id' => Auth::user()->id,
            'status' => 'AWAITING',
            'attachments' => $this->attachments,
        ]);
        if ($respomse['status'] == 'success') {
            $this->success($respomse['message']);
            $this->getdata();
            $this->attachments = [];
        } else {
            $this->error($respomse['message']);
        }

    }

    public function update()
    {
        $respomse = $this->mycdprepo->update($this->id, [
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'duration' => $this->duration,
            'durationunit' => $this->durationunit,
            'customerprofession_id' => $this->customerprofession_id,
            'year' => $this->year,
            'user_id' => Auth::user()->id,
            'attachments' => $this->attachments,
        ]);
        if ($respomse['status'] == 'success') {
            $this->success($respomse['message']);
            $this->getdata();
        } else {
            $this->error($respomse['message']);
        }

    }

    public function delete($id)
    {
        $respomse = $this->mycdprepo->delete($id);
        if ($respomse['status'] == 'success') {
            $this->success($respomse['message']);
            $this->getdata();
        } else {
            $this->error($respomse['message']);
        }
    }

    public function edit($id)
    {
        $this->id = $id;
        $payload = $this->mycdprepo->get($this->id);
        $this->attachments = $payload->attachments->map(function ($attachment) {
            return [
                'type' => $attachment->type,
                'file' => Storage::url($attachment->file),
            ];
        });
        $this->title = $payload->title;
        $this->description = $payload->description;
        $this->type = $payload->type;
        $this->duration = $payload->duration;
        $this->durationunit = $payload->durationunit;
        $this->addmodal = true;
    }

    public function openattachmentmodal($id)
    {
        $this->id = $id;
        $this->mycdp = $this->mycdprepo->get($this->id);
        $this->attachmentmodal = true;
    }

    public function openadocmodal($file)
    {
        $this->documenturl = Storage::url($file);
        $this->viewattachmentmodal = true;
    }

    public function saveattachment()
    {
        $this->validate([
            'type' => 'required',
            'file' => 'required',
        ]);
        $file = $this->file->store('mycdp', 's3');

        $this->attachments[] = [
            'type' => $this->type,
            'file' => $file,
        ];

    }

    public function deleteattachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function submitforassessment($id)
    {
        $respomse = $this->mycdprepo->submitforassessment($id);
        if ($respomse['status'] == 'success') {
            $this->success($respomse['message']);
            $this->getdata();
        } else {
            $this->error($respomse['message']);
        }
    }

    public function render()
    {
        return view('livewire.admin.components.mycdps');
    }
}

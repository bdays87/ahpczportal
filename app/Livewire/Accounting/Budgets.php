<?php

namespace App\Livewire\Accounting;

use App\Interfaces\ibudgetInterface;
use App\Interfaces\ichartofaccountsInterface;
use App\Interfaces\icostcenterInterface;
use App\Interfaces\icurrencyInterface;
use Livewire\Component;
use Mary\Traits\Toast;

class Budgets extends Component
{
    use Toast;

    public $breadcrumbs  = [];
    public $modal        = false;
    public $viewModal    = false;
    public $vaModal      = false;  // budget vs actual
    public $id;
    public $name;
    public $year;
    public $description;
    public $currency_id;
    public $lines         = [];
    public $selectedBudget;
    public $vaData        = [];

    protected $budgetRepo;
    protected $coaRepo;
    protected $ccRepo;
    protected $currencyRepo;

    public function boot(
        ibudgetInterface $budgetRepo,
        ichartofaccountsInterface $coaRepo,
        icostcenterInterface $ccRepo,
        icurrencyInterface $currencyRepo
    ) {
        $this->budgetRepo  = $budgetRepo;
        $this->coaRepo     = $coaRepo;
        $this->ccRepo      = $ccRepo;
        $this->currencyRepo = $currencyRepo;
    }

    public function mount()
    {
        $this->year = date('Y');
        $this->breadcrumbs = [
            ['label' => 'Dashboard', 'icon' => 'o-home', 'link' => route('dashboard')],
            ['label' => 'Accounting'],
            ['label' => 'Budgets'],
        ];
    }

    public function getBudgets()
    {
        return $this->budgetRepo->getAll($this->year);
    }

    public function addLine()
    {
        $this->lines[] = [
            'account_id'      => null,
            'cost_center_id'  => null,
            'month'           => 1,
            'budgeted_amount' => 0,
        ];
    }

    public function removeLine($index)
    {
        array_splice($this->lines, $index, 1);
        $this->lines = array_values($this->lines);
    }

    public function save()
    {
        $this->validate([
            'name'        => 'required|string|max:200',
            'year'        => 'required|integer|min:2000|max:2100',
            'currency_id' => 'required',
        ]);

        if (empty($this->lines)) {
            $this->error('Add at least one budget line');
            return;
        }

        $data = [
            'name'        => $this->name,
            'year'        => $this->year,
            'description' => $this->description,
            'currency_id' => $this->currency_id,
        ];

        $response = $this->id
            ? $this->budgetRepo->update($this->id, $data, $this->lines)
            : $this->budgetRepo->create($data, $this->lines);

        if ($response['status'] === 'success') {
            $this->success($response['message']);
            $this->resetForm();
            $this->modal = false;
        } else {
            $this->error($response['message']);
        }
    }

    public function approve($id)
    {
        $response = $this->budgetRepo->approve($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    public function edit($id)
    {
        $b                = $this->budgetRepo->get($id);
        $this->id         = $b->id;
        $this->name       = $b->name;
        $this->year       = $b->year;
        $this->description = $b->description;
        $this->currency_id = $b->currency_id;
        $this->lines      = $b->lines->map(fn($l) => [
            'account_id'      => $l->account_id,
            'cost_center_id'  => $l->cost_center_id,
            'month'           => $l->month,
            'budgeted_amount' => $l->budgeted_amount,
        ])->toArray();
        $this->modal = true;
    }

    public function viewBudget($id)
    {
        $this->selectedBudget = $this->budgetRepo->get($id);
        $this->viewModal      = true;
    }

    public function viewVariance($id)
    {
        $this->vaData  = $this->budgetRepo->getBudgetVsActual($id);
        $this->vaModal = true;
    }

    public function delete($id)
    {
        $response = $this->budgetRepo->delete($id);
        $response['status'] === 'success' ? $this->success($response['message']) : $this->error($response['message']);
    }

    private function resetForm()
    {
        $this->reset(['id', 'name', 'description', 'currency_id', 'lines']);
        $this->year = date('Y');
    }

    public function headers(): array
    {
        return [
            ['key' => 'name',       'label' => 'Budget Name'],
            ['key' => 'year',       'label' => 'Year'],
            ['key' => 'currency.name', 'label' => 'Currency'],
            ['key' => 'status',     'label' => 'Status'],
            ['key' => 'action',     'label' => ''],
        ];
    }

    public function render()
    {
        return view('livewire.accounting.budgets', [
            'budgets'     => $this->getBudgets(),
            'headers'     => $this->headers(),
            'accounts'    => $this->coaRepo->getPostable(),
            'costCenters' => $this->ccRepo->getAll('active'),
            'currencies'  => $this->currencyRepo->getAll('active'),
            'months'      => collect(range(1, 12))->map(fn($m) => ['id' => $m, 'name' => date('F', mktime(0,0,0,$m,1))]),
        ]);
    }
}

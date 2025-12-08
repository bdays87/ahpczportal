<div>
  @if(auth()->user()->customer)
  <x-hr/>
  <livewire:admin.components.customerprofessions :customer="auth()->user()->customer->customer"/>
 <livewire:components.otherapplications :customer="auth()->user()->customer->customer"/>
  @endif
</div>

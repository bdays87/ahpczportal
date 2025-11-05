<div>
   
    <x-button icon="o-bell" class="btn-ghost btn-sm indicator" wire:click="$set('modal', true)">
        <span class="indicator-item badge badge-xs badge-error">{{ auth()->user()->notifications->count() }}</span>
    </x-button>
    <x-modal title="Notifications" wire:model="modal">
        @forelse(auth()->user()->notifications as $notification)
        <div>
            @if(array_key_exists('message',$notification->data))
            <p>{{ $notification->data['message'] }}</p>
            <p >{{ $notification->created_at->diffForHumans() }}</p>
            <hr/>

            @endif
        </div>
        @empty
        <div>
            <p class="text-center text-red-500">No notifications</p>
        </div>
        @endforelse
        </x-modal>
        
</div>

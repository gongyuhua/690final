@component('mail::message')
    # Introduction

    Thank you {{ $order->name }}.  We just shipped {{ $order->item_count }} items.



    Thanks,
    {{ config('app.name') }}
@endcomponent

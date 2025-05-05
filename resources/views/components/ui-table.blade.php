@props(['headers' => [], 'rows' => [], 'actions' => false])

<div class="rounded-md border">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b bg-gray-50 transition-colors">
                @foreach($headers as $header)
                    <th class="h-12 px-4 text-left align-middle font-medium text-gray-500">
                        {{ $header }}
                    </th>
                @endforeach
                @if($actions)
                    <th class="h-12 px-4 text-left align-middle font-medium text-gray-500">
                        Actions
                    </th>
                @endif
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div> 
@extends('mobile')
@section('title', 'Logs')
@section('content-mobile')
    <div class="container">
        <div style="height: 100vh" class="p-3">

            <h5 class="mb-3"><a href="/" style="text-decoration: none; color:#333">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                </a>Logs</h5>
            <div class="table-responsive">
                <table class="table table-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Product ID') }}</th>
                            <th>{{ __('Variation ID') }}</th>
                            <th>{{ __('WC Stock') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('WC Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($logs as $item)
                            <tr class="@if($item->type === 'OUT') tbl-red @else tbl-green @endif">
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->type }}</td>
                                <td>{{ $item->getProduct()->product_name }}</td>
                                <td>{{ $item->variation_id }} | {{ $item->variation_code }}</td>
                                <td>{{ $item->wc_stock }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->balance }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                {{ $logs->links() }}
            </div>

        </div>
    </div>
@endsection

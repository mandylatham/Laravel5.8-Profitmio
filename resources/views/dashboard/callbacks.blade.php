@if ($callbacks->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover datatable">
            <thead>
            <tr>
                <td>Called</td>
                <td>Name</td>
                <td>Called At</td>
            </tr>
            </thead>
            <tbody>
            @foreach ($callbacks as $callback)
                <tr>
                    <td><input type="checkbox" class="callback-button" data-callback_id="{{ $callback->id }}"></td>
                    <td>
                        <div>{{ $callback->name }}</div>
                        <small>{{ $callback->email }}</small>
                        <div style="text-transform: uppercase; font-size: small; color: #555;">{{ $callback->vehicle }}</div>
                        <div>
                            <button class="btn btn-primary btn-pure button-link" data-url="tel:{{ $callback->phone_number}}">
                                <i class="icon md-phone" aria-hidden="true"></i>
                                {{ $callback->phone_number }}
                            </button>
                        </div>
                    </td>
                    <td>{{ show_date($callback->created_at) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info">None Found</div>
@endif

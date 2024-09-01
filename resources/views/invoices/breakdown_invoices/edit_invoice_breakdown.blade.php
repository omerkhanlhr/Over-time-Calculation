@extends('admin.admin_dashboard')

@section('admin')
    <div class="page-content">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Edit Breakdown - {{ $laborType }}</h6>

                    <form method="POST" action="{{ route('invoice.breakdown.update', ['invoiceId' => $invoice->id, 'laborType' => $laborType]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <input type="text" class="form-control" id="rate" name="rate" value="{{ old('rate', $breakdown->rate) }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

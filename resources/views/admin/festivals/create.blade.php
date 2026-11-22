@extends('admin.layouts.app')

@section('title', 'Add Festival')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Poster & Template Management</div>
            <h2 class="page-title">Add New Festival / Special Day</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.festivals.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Festivals
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.festivals.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Festival / Event Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Dev Deepawali, Constitution Day" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Festival Date</label>
                    <input type="date" name="festival_date" class="form-control @error('festival_date') is-invalid @enderror" value="{{ old('festival_date', now()->format('Y-m-d')) }}" required>
                    @error('festival_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Category</label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', 'religious_festival') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Active Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Yes (Active in Studio & Scheduler)</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>No (Inactive)</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Ready Caption / Greeting Wish</label>
                    <textarea name="ready_caption" rows="3" class="form-control @error('ready_caption') is-invalid @enderror" placeholder="Default social media caption or Hindi/English wishing text...">{{ old('ready_caption') }}</textarea>
                    @error('ready_caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Suggested Hashtags</label>
                    <input type="text" name="hashtags" class="form-control @error('hashtags') is-invalid @enderror" value="{{ old('hashtags') }}" placeholder="e.g. #DevDeepawali #Deepotsav #Varanasi #SubhDeepawali">
                    @error('hashtags')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.festivals.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Festival
            </button>
        </div>
    </form>
</div>
@endsection

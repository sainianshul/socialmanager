@extends('admin.layouts.app')

@section('title', 'Add Readymade Poster')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Readymade Posters</div>
            <h2 class="page-title">Add New Readymade Poster</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.posters.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Posters
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.posters.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Political Party</label>
                    <select name="party_id" class="form-select @error('party_id') is-invalid @enderror">
                        <option value="">All Parties (Universal)</option>
                        @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ old('party_id', request('party_id')) == $party->id ? 'selected' : '' }}>
                                {{ $party->short_code }} - {{ $party->name }} ({{ $party->hindi_name }})
                            </option>
                        @endforeach
                    </select>
                    @error('party_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Poster Title / Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Poster Title" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">Poster Artwork Image</label>
                    <input type="file" name="poster_image" class="form-control @error('poster_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp" required>
                    @error('poster_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Default Caption & Hashtags</label>
                    <textarea name="default_caption" class="form-control @error('default_caption') is-invalid @enderror" rows="3" placeholder="Write default caption & hashtags...">{{ old('default_caption') }}</textarea>
                    @error('default_caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pin to Top of List</label>
                    <select name="show_on_top" class="form-select @error('show_on_top') is-invalid @enderror">
                        <option value="0" {{ old('show_on_top', '0') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('show_on_top') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                    @error('show_on_top')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Auto Show Date (Occasion Date)</label>
                    <input type="date" name="auto_show_date" class="form-control @error('auto_show_date') is-invalid @enderror" value="{{ old('auto_show_date') }}">
                    @error('auto_show_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0" placeholder="0">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label required">Status</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.posters.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Readymade Poster
            </button>
        </div>
    </form>
</div>
@endsection

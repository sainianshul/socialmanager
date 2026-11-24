@extends('admin.layouts.app')

@section('title', 'Add Poster Category')

@section('content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Readymade Posters</div>
            <h2 class="page-title">Add New Poster Category</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="{{ route('admin.poster-categories.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Categories
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form action="{{ route('admin.poster-categories.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label required">Category Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. सामाजिक जागरूकता, दैनिक सुविचार" required>
                    <small class="form-hint">Hindi or English category title for grouping readymade posters.</small>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Slug (URL Identifier)</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="e.g. social-awareness, suvichar">
                    <small class="form-hint">Unique identifier. Leave blank to auto-generate from name.</small>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tabler Icon Class</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-icons" id="icon-preview"></i></span>
                        <input type="text" name="icon" id="field-icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', 'ti ti-template') }}" placeholder="e.g. ti ti-heart-handshake, ti ti-sun, ti ti-candle">
                    </div>
                    <small class="form-hint">Tabler icon name (e.g. <code>ti ti-sun</code>, <code>ti ti-heart-handshake</code>, <code>ti ti-candle</code>).</small>
                    @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Display Sort Order</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0">
                    <small class="form-hint">Lower numbers appear first in the categories list.</small>
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Show as Top Collection</label>
                    <select name="show_as_collection" class="form-select @error('show_as_collection') is-invalid @enderror">
                        <option value="0" {{ old('show_as_collection', '0') == '0' ? 'selected' : '' }}>No (Standard Category)</option>
                        <option value="1" {{ old('show_as_collection') == '1' ? 'selected' : '' }}>Yes (Pinned in Top Collections Banner)</option>
                    </select>
                    <small class="form-hint">Display prominent featured collection banner in the app/drawer.</small>
                    @error('show_as_collection')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
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
            <a href="{{ route('admin.poster-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i> Save Category
            </button>
        </div>
    </form>
</div>
@endsection

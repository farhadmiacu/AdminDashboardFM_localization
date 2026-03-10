@extends('backend.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Brands</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Brand Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Brand Create</h4>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-primary">Back</a>
                </div>

                <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row gy-4">

                            {{-- Locale Tabs --}}
                            <div class="col-12">
                                <ul class="nav nav-tabs mb-3" id="localeTabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#locale-en">English</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#locale-es">Spanish</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    {{-- English Tab --}}
                                    <div class="tab-pane fade show active" id="locale-en">
                                        <div class="mb-3">
                                            <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                                            <input type="text" name="name[en]" id="name_en" class="form-control" placeholder="Enter brand name in English" value="{{ old('name.en') }}">
                                            @error('name.en')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Spanish Tab --}}
                                    <div class="tab-pane fade" id="locale-es">
                                        <div class="mb-3">
                                            <label class="form-label">Name (Spanish)</label>
                                            <input type="text" name="name[es]" class="form-control" placeholder="Enter brand name in Spanish" value="{{ old('name.es') }}">
                                            @error('name.es')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Slug Field --}}
                            <div class="col-12">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control" placeholder="Auto-generated from English name" value="{{ old('slug') }}" readonly>
                            </div>

                            {{-- Image Field --}}
                            <div class="col-12">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" id="image" class="form-control dropify" data-allowed-file-extensions="jpg jpeg png webp">
                                @error('image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Status Field --}}
                            <div class="col-12">
                                <label class="form-label" for="statusSelect">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" name="status" id="statusSelect">
                                    <option value="" disabled selected>Choose...</option>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Published</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Unpublished</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Submit Button --}}
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('name_en').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '') // remove special chars
                .replace(/\s+/g, '-') // replace spaces with -
                .replace(/-+/g, '-'); // remove multiple -
            document.getElementById('slug').value = slug;
        });
    </script>
@endpush

@extends('layouts.dashboard')

@section('title')
    Store Dashboard Product Detail
@endsection

@section('content')
    <!-- section content -->
    <div class="section-content section-dashboard-home" data-aos="fade-up">
      <div class="container-fluid">
        <div class="dashboard-heading">
          <h2 class="dashboard-title">Shirup Marzzan</h2>
          <p class="dashboard-subtitle">
            Peoduct Details
          </p>
        </div>
        <div class="dashboard-content">
          <div class="row">
            <div class="col-12">
                {{-- if errors --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>                    
                    @endforeach
                  </ul>
                </div>
              @endif
              {{-- end if errors --}}
              <form action="{{ route('dashboard-product-update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{--  hidden input --}}
                <input type="hidden" name="users_id" value="{{ Auth::user()->id }}">
                {{-- end:hidden --}}
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Product Name</label>
                          <input  type="text" 
                                  class="form-control"
                                  name="name" 
                                  value="{{ $product->name }}" 
                          />
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Price</label>
                          <input  type="number" 
                                  class="form-control"
                                  name="price" 
                                  value="{{ $product->price }}" 
                          />
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Kategori</label>
                          <select name="categories_id" 
                                  class="form-control"
                          >
                                  
                           <option value="{{ $product->categories_id }}"> Tidak diganti ({{ $product->category->name }}) </option>
                            @foreach($categories as $category)
                              <option value="{{ $category->id }}"> {{ $category->name }} </option> 
                            @endforeach

                          </select>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Description</label>
                          <textarea 
                              name="description" 
                              id="editor"
                           >
                            {!! $product->description !!}
                          </textarea>
                        </div>
                      </div>

                    </div>
                    <div class="row">
                      <div class="col text-right">
                        <button type="submit" 
                                class="btn btn-success px-5 btn-block">
                                Save Now
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              {{-- END FORM --}}
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    @foreach ($product->galleries as $gallery)
                      <div class="col-md-4">
                        <div class="gallery-container">
                          <img src="{{ Storage::url($gallery->photos ?? '') }}" class="w-100" alt="">
                          <a href="{{ route('dashboard-product-gallery-delete', $gallery->id) }}" class="delete-gallery">
                            <img src="/images/icon-delete.svg" alt="">
                          </a>
                        </div>
                      </div>
                    @endforeach
                    <div class="col-12">
                      <form action="{{ route('dashboard-product-gallery-upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="products_id" value="{{ $product->id }}">
                        <input  type="file" 
                                name="photos"
                                id="file" 
                                style="display: none;" 
                                onchange="form.submit()"
                        />
                        <button class="btn btn-secondary btn-block mt-3" 
                                onclick="thisFileUpload();"
                                type="button"
                        >  
                                Add Photo
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- end --}}
@endsection

@push('addon-script')
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
<script>
  AOS.init();
</script>
<script>
  // EDITOR INSTALL
  CKEDITOR.replace('editor');

  // JQUERY TOGGLE RESPONSIVE
  $("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#wrapper").toggleClass('toggled');
  })

  // UPLOAD FUNCTION
  function thisFileUpload() {
    document.getElementById("file").click();
  }

</script>
@endpush
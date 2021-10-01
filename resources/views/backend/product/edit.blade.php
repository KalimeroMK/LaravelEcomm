@extends('backend.layouts.master')

@section('content')

    <div class="card">
        <h5 class="card-header">Edit Product</h5>
        <div class="card-body">
            <form method="post" action="{{route('products.update',$product->id)}}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                           value="{{$product->title}}" class="form-control">
                    @error('title')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary">{{$product->summary}}</textarea>
                    @error('summary')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Description</label>
                    <textarea class="form-control" id="description"
                              name="description">{{$product->description}}</textarea>
                    @error('description')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="is_featured">Is Featured</label><br>
                    <input type="checkbox" name='is_featured' id='is_featured'
                           value='{{$product->is_featured}}' {{(($product->is_featured) ? 'checked' : '')}}> Yes
                </div>
                {{-- {{$categories}} --}}

                <div class="form-group">
                    <label for="cat_id">Category <span class="text-danger">*</span></label>
                    <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                            multiple="multiple">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">@for ($i = 0; $i < $category->depth; $i++)
                                    - @endfor {{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group {{(($product->child_cat_id)? '' : 'd-none')}}" id="child_cat_div">
                    <label for="child_cat_id">Sub Category</label>
                    <select name="child_cat_id" id="child_cat_id" class="form-control">
                        <option value="">--Select any sub category--</option>

                    </select>
                </div>

                <div class="form-group">
                    <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
                    <input id="price" type="number" name="price" placeholder="Enter price" value="{{$product->price}}"
                           class="form-control">
                    @error('price')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount" class="col-form-label">Discount(%)</label>
                    <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"
                           value="{{$product->discount}}" class="form-control">
                    @error('discount')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="size">Size</label>
                    <select name="size[]" class="form-control selectpicker" multiple data-live-search="true">
                        <option value="">--Select any size--</option>
                        @foreach($items as $item)
                            @php
                                $data=explode(',',$item->size);
                                // dd($data);
                            @endphp
                            <option value="S" @if( in_array( "S",$data ) ) selected @endif>Small</option>
                            <option value="M" @if( in_array( "M",$data ) ) selected @endif>Medium</option>
                            <option value="L" @if( in_array( "L",$data ) ) selected @endif>Large</option>
                            <option value="XL" @if( in_array( "XL",$data ) ) selected @endif>Extra Large</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="color" class="col-form-label">Color</label>
                    <input id="color" type="text" name="color[]" placeholder="Enter color" value="{{ $product->color }}"
                           class="form-control">
                    @error('color')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="brand_id">Brand</label>
                    <select name="brand_id" class="form-control">
                        <option value="">--Select Brand--</option>
                        @foreach($brands as $brand)
                            <option
                                    value="{{$brand->id}}" {{(($product->brand_id==$brand->id)? 'selected':'')}}>{{$brand->title}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="condition">Condition</label>
                    <select name="condition" class="form-control">
                        <option value="">--Select Condition--</option>
                        <option value="default" {{(($product->condition=='default')? 'selected':'')}}>Default</option>
                        <option value="new" {{(($product->condition=='new')? 'selected':'')}}>New</option>
                        <option value="hot" {{(($product->condition=='hot')? 'selected':'')}}>Hot</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stock">Quantity <span class="text-danger">*</span></label>
                    <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"
                           value="{{$product->stock}}" class="form-control">
                    @error('stock')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
                    <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                  <i class="fas fa-image"></i> Choose
                  </a>
              </span>
                        <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$product->photo}}">
                    </div>
                    <div id="holder" style="margin-top:15px;max-height:100px;"></div>
                    @error('photo')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{(($product->status=='active')? 'selected' : '')}}>Active</option>
                        <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>Inactive</option>
                    </select>
                    @error('status')
                    <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css"
          rel="stylesheet"/>
@endpush
@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.js-example-basic-multiple').select2();
        });
    </script>
    <!-- select2 -->
    <script type="text/javascript">
        $('#category').select2().val({!! json_encode($product->categories()->allRelatedIds()) !!}).trigger('change');
    </script>
@endpush

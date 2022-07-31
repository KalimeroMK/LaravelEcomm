@if ($product->exists)
    <form class="form-horizontal" method="POST" action="{{ route('products.update', $product->id) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('products.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="title" placeholder="Enter title"
                           value="{{ $product->title ?? '' }}"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="summary" class="col-form-label">Summary <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="summary" name="summary">{{$product->summary ?? ''}}</textarea>
                </div>

                <div class="form-group">
                    <label for="description" class="col-form-label">Description</label>
                    <textarea class="form-control" id="description"
                              name="description">{{ $product->description ??''}}</textarea>

                </div>


                <div class="form-group">
                    <label for="is_featured">Is Featured</label><br>
                    <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
                </div>
                <div class="form-group">
                    <label for="cat_id">Category <span class="text-danger">*</span></label>
                    <select class="form-control js-example-basic-multiple" id="category" name="category[]"
                            multiple="multiple">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
                    <input id="price" type="number" name="price" placeholder="Enter price"
                           value="{{ $product->price ?? '' }}"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="discount" class="col-form-label">Discount(%)</label>
                    <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"
                           value="{{ $product->discount ?? ''}}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="size">Size</label>
                    <select class="form-control js-example-basic-multiple" id="size" name="size[]"
                            multiple="multiple">
                        @foreach ($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="color" class="col-form-label">Color</label>
                    <input id="color" type="text" name="color[]" placeholder="Enter color"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="brand_id">Brand</label>
                    <select name="brand_id" class="form-control">
                        <option value="">--Select Brand--</option>
                        @foreach($brands as $brand)
                            <option value="{{$brand->id}}" @if (!empty($product->brand->id))
                                {{($brand->id==$product->brand->id)? 'selected':'' }}
                                    @endif>{{$brand->title}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="condition_id">Condition</label>
                    <select name="condition_id" class="form-control">
                        <option value="">--Select condition--</option>
                        @foreach($conditions as $condition)
                            <option value="{{$condition->id}}"@if (!empty($product->condition->id))
                                {{($condition->id==$product->condition->id)? 'selected':'' }}
                                    @endif>{{$condition->status}}
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group">
                    <label for="stock">Quantity <span class="text-danger">*</span></label>
                    <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"
                           value="{{ $product->stock ?? ''}}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="image">
                            <label><h4>Add image</h4></label>
                            <input type="file" class="form-control" name="photo">
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control">
                                <option @checked($product->status =='active') value="active">Active</option>
                                <option @checked($product->status =='inactive') value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <button type="reset" class="btn btn-warning">Reset</button>
                            <button class="btn btn-success" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
    </form>

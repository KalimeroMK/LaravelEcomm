{{-- Filter Component --}}
<div class="sidebar-filter">
    <h4>Filter Products</h4>
    <form method="POST">
        @csrf
        <div class="form-group">
            <label>Price Range</label>
            <input type="range" name="price" class="form-control">
        </div>
        <button type="submit" class="btn btn-default btn-block">Apply Filter</button>
    </form>
</div>

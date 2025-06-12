@props(['productId'])
<form method="POST" action="{{ route('products.compare.add', $productId) }}" style="display:inline">
    @csrf
    <button type="submit" class="btn btn-outline-primary btn-sm">
        <i class="fa fa-balance-scale"></i> Compare
    </button>
</form>

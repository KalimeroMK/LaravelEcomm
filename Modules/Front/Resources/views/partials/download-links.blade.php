@php
$user = Auth::user();
$hasDownloads = false;
@endphp

@foreach($order->carts as $cartItem)
    @php
        $product = $cartItem->product;
        if (!$product || !$product->isDownloadable()) {
            continue;
        }
        $hasDownloads = true;
    @endphp
@endforeach

@if($hasDownloads)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="ti-download"></i> Digital Downloads</h5>
        </div>
        <div class="card-body">
            @foreach($order->carts as $cartItem)
                @php
                    $product = $cartItem->product;
                    if (!$product || !$product->isDownloadable()) {
                        continue;
                    }
                    
                    // Get or create order downloads
                    $orderDownloads = [];
                    foreach ($product->activeDownloads as $download) {
                        $orderDownload = \Modules\Product\Models\OrderDownload::firstOrCreate(
                            [
                                'order_id' => $order->id,
                                'product_download_id' => $download->id,
                                'user_id' => $user->id,
                            ],
                            [
                                'expires_at' => $product->download_expiry_days 
                                    ? now()->addDays($product->download_expiry_days)
                                    : null,
                            ]
                        );
                        $orderDownloads[] = ['download' => $download, 'orderDownload' => $orderDownload];
                    }
                @endphp
                
                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="font-weight-bold">{{ $product->title }}</h6>
                    
                    @foreach($orderDownloads as $item)
                        @php
                            $download = $item['download'];
                            $orderDownload = $item['orderDownload'];
                            $canDownload = $order->payment_status === 'paid' && $orderDownload->canDownload();
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <i class="ti-file"></i> {{ $download->file_name }}
                                <small class="text-muted">({{ $download->formatted_file_size }})</small>
                            </div>
                            <div>
                                @if($order->payment_status !== 'paid')
                                    <span class="badge badge-warning">Payment Required</span>
                                @elseif($orderDownload->isExpired())
                                    <span class="badge badge-danger">Expired</span>
                                @elseif($orderDownload->isLimitReached())
                                    <span class="badge badge-danger">Limit Reached</span>
                                @else
                                    <a href="{{ $download->getDownloadUrl($order->id, $user->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="ti-download"></i> Download
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="small text-muted">
                            Downloads: {{ $orderDownload->downloads_count }} 
                            @if($product->max_downloads)
                                / {{ $product->max_downloads }}
                            @endif
                            @if($orderDownload->expires_at)
                                | Expires: {{ $orderDownload->expires_at->format('M d, Y') }}
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
            
            <div class="alert alert-info mt-3 mb-0">
                <small>
                    <i class="ti-info-alt"></i> 
                    Downloads are available after payment is confirmed. 
                    @if($product->max_downloads)
                        Maximum {{ $product->max_downloads }} downloads per file.
                    @endif
                    @if($product->download_expiry_days)
                        Links expire after {{ $product->download_expiry_days }} days.
                    @endif
                </small>
            </div>
        </div>
    </div>
@endif

{{-- Modern Theme Newsletter --}}
<section class="light-gray-bg pv-30 clearfix">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="call-to-action text-center">
                    <h2 class="mb-20">Subscribe to Our Newsletter</h2>
                    <p class="lead font-20 mb-30">Get 10% off your first purchase when you subscribe</p>
                    <form action="{{ route('subscribe') }}" method="post" class="form-inline">
                        @csrf
                        <div class="form-group">
                            <input type="email" name="email" class="form-control input-lg" placeholder="Enter your email address" required>
                        </div>
                        <button type="submit" class="btn btn-default-transparent btn-animated btn-lg">Subscribe <i class="fa fa-envelope-o"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

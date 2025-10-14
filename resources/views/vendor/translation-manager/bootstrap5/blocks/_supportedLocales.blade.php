{{-- TODO: Update markup/classes for Bootstrap 5 as needed --}}
<div class="card mt-2">
    <div class="card-body">
        <fieldset>
            <legend>Supported locales</legend>
            <p>Current supported locales:</p>
            <form class="form-remove-locale" method="POST" role="form" action="{{action($controller.'@postRemoveLocale')}}"
                  data-confirm="Are you sure to remove this locale and all of data?">
                @csrf()
                <ul class="list-locales list-unstyled">
                    @foreach($locales as $locale)
                        <li class="mb-2 d-flex align-items-center">
                            <span>{{$locale}}</span>
                            <button type="submit" name="remove-locale[{{$locale}}]" class="btn btn-danger btn-sm ms-2" data-disable-with="...">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </form>
            <form class="form-add-locale" method="POST" role="form" action="{{action($controller.'@postAddLocale')}}">
                @csrf()
                <div class="mb-3">
                    <p>Enter new locale key:</p>
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <input type="text" name="new-locale" class="form-control"/>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-outline-success w-100" data-disable-with="Adding..">Add new locale</button>
                        </div>
                    </div>
                </div>
            </form>
        </fieldset>
    </div>
</div>

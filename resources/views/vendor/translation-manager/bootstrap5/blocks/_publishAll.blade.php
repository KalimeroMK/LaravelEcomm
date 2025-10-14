{{-- TODO: Update markup/classes for Bootstrap 5 as needed --}}
<div class="card mt-2">
    <div class="card-body">
        <fieldset>
            <legend>Export all translations</legend>
            <form class="row g-2 align-items-center form-publish-all" method="POST" action="{{action($controller.'@postPublish', '*') }}" data-remote="true" role="form"
                  data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
                @csrf()
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary w-100" data-disable-with="Publishing..">Publish all</button>
                </div>
            </form>
        </fieldset>
    </div>
</div>

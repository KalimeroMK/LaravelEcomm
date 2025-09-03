@php
  $actionBtnIcon = $actionBtnIcon ?? '';
  $actionBtnIcon = $actionBtnIcon ? $actionBtnIcon . ' fa-fw' : '';
  $modalClass = $modalClass ?? '';
  $btnSubmitText = $btnSubmitText ?? trans('LaravelLogger::laravel-logger.modals.shared.btnConfirm');
@endphp
<div class="modal fade modal-{{ $modalClass }}" id="{{ $formTrigger }}" role="dialog" aria-labelledby="{{ $formTrigger }}Label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header {{ $modalClass }}">
        <h5 class="modal-title">Confirm</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline pull-left btn-flat" type="button" data-dismiss="modal">
          <i class="fa fa-fw fa-close" aria-hidden="true"></i> {{ trans('LaravelLogger::laravel-logger.modals.shared.btnCancel') }}
        </button>
        <button class="btn btn-{{ $modalClass }} pull-right btn-flat" type="button" id="confirm">
          <i class="fa {{ $actionBtnIcon }}" aria-hidden="true"></i> {{ $btnSubmitText }}
        </button>
      </div>
    </div>
  </div>
</div>

<?php use Modules\Core\Helpers\Helper; ?>
<div id="messages">
    <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown"
       aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-envelope fa-fw"></i>
        <!-- Counter - Messages -->
        <?php if(count(Helper::messageList())>5): ?>
            <span data-count="5" class="badge badge-danger badge-counter">5+</span>
        <?php else: ?>

            <span data-count="<?php echo e(count(Helper::messageList())); ?>"
                  class="badge badge-danger badge-counter"><?php echo e(count(Helper::messageList())); ?></span>

        <?php endif; ?>
    </a>
    <!-- Dropdown - Messages -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
         aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">
            Message Center
        </h6>
        <div id="message-items">
            <?php $__currentLoopData = Helper::messageList(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo e(route('messages.show',$message->id)); ?>">
                    <div class="dropdown-list-image mr-3">
                        <?php if($message->photo): ?>
                            <img class="rounded-circle" src="<?php echo e($message->photo); ?>" alt="profile">
                        <?php else: ?>
                            <img class="rounded-circle" src="<?php echo e(asset('backend/img/avatar.png')); ?>" alt="default img">
                        <?php endif; ?>
                        
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate"><?php echo e($message->subject); ?></div>
                        <div class="small text-gray-500"><?php echo e($message->name); ?>

                            · <?php echo e($message->created_at->diffForHumans()); ?></div>
                    </div>
                </a>
                <?php if($loop->index+1==5): ?>
                    <?php
                        break
                    ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <a class="dropdown-item text-center small text-gray-500" href="<?php echo e(route('messages.index')); ?>">Read More
            Messages</a>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        $(document).ready(function () {

            Echo.channel('message')
                .listen('MessageSent', (e) => {

                    const message_container = $('#message-items');
                    const message_counter_area = $('#messages .count');
                    const message_counter = parseInt($(message_counter_area).attr('data-count')) + 1;
                    const message_length = parseInt($('#message-items>.dropdown-item').length);
                    $(message_counter_area).attr('data-count', message_counter);

                    const data = `
      <a class="dropdown-item d-flex align-items-center message-item" href="${e.message.url}">
        <div class="dropdown-list-image mr-3">
          <img class="rounded-circle" src="${e.message.photo}" alt="${e.message.name}">
        </div>
        <div class="font-weight-bold">
          <div class="text-truncate">${e.message.subject}</div>
          <div class="small text-gray-500">${e.message.name} · ${e.message.date}</div>
        </div>
      </a>
      `;

                    $(message_container).prepend(data);

                    if (message_counter <= 5) {
                        $(message_counter_area).text(message_counter);
                    } else {
                        $(message_counter_area).text('5+');
                    }


                    if (message_length >= 5) $(message_container).find('.message-item').last().remove();

                });

        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/Modules/Message/Resources/views/message.blade.php ENDPATH**/ ?>
<h3>Hi <?php echo e($user->name); ?></h3>

<h4>A new ticket has been logged. Ticket Number: <?php echo e($ticket->id); ?></h4>

<h3>Ticket Details</h3>
<h4>Subject: <?php echo e($ticket->subject); ?></h4>
<p>Description: <?php echo nl2br(e($ticket->description)); ?></p>

<hr>

<ul>
  <li>Logged by: <?php echo e($ticket->user->name); ?></li>
  <li>Location: <?php echo e($ticket->location->location_name); ?></li>
  <li>Status: <?php echo e($ticket->ticket_status->status); ?></li>
  <li>Type: <?php echo e($ticket->ticket_type->type); ?></li>
  <li>Priority: <?php echo e($ticket->ticket_priority->priority); ?></li>
</ul>

<hr>

<a href="<?php echo e(url('/tickets')); ?>/<?php echo e($ticket->id); ?>">View The Ticket Online</a>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views\emails\new-ticket.blade.php ENDPATH**/ ?>
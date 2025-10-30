<h3>Hi <?php echo e($user->name); ?></h3>

<h4>A new ticket note has been added to Ticket Number: <?php echo e($ticket->id); ?></h4>

<p><b>Note:</b> <?php echo nl2br(e($ticketEntry->note)); ?></p>

<hr>

<h3>Ticket Details</h3>
<h4>Subject: <?php echo e($ticket->subject); ?></h4>
<p>Description: <?php echo nl2br(e($ticket->description)); ?></p>

<hr>

<a href="<?php echo e(url('/tickets')); ?>/<?php echo e($ticket->id); ?>">View The Ticket Online</a>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views\emails\new-ticket-note.blade.php ENDPATH**/ ?>
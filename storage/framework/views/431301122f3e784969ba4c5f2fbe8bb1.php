

<?php $__env->startSection('title'); ?>
Daily Activities Calendar
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-calendar"></i> Daily Activities Calendar</h3>
                <div class="box-tools pull-right">
                    <!-- Filter by user (admin only) -->
                    <?php if(Auth::user()->hasRole(['admin', 'super-admin']) && $users->count() > 0): ?>
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <select class="form-control" id="userFilter">
                            <option value="">All Users</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(is_object($user) && isset($user->id) && isset($user->name)): ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-info btn-flat" onclick="filterCalendar()">
                                <i class="fa fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box-body">
                <!-- Activity Legend -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-12">
                        <div class="activity-legend">
                            <small><strong>Activity Types:</strong></small>
                            <span class="legend-item"><i class="fa fa-wrench" style="color: #f39c12;"></i> Maintenance</span>
                            <span class="legend-item"><i class="fa fa-download" style="color: #3c8dbc;"></i> Installation</span>
                            <span class="legend-item"><i class="fa fa-support" style="color: #00a65a;"></i> Support</span>
                            <span class="legend-item"><i class="fa fa-graduation-cap" style="color: #605ca8;"></i> Training</span>
                            <span class="legend-item"><i class="fa fa-users" style="color: #dd4b39;"></i> Meeting</span>
                            <span class="legend-item"><i class="fa fa-file-text" style="color: #00c0ef;"></i> Documentation</span>
                            <span class="legend-item"><i class="fa fa-tools" style="color: #e74c3c;"></i> Repair</span>
                            <span class="legend-item"><i class="fa fa-arrow-up" style="color: #9b59b6;"></i> Upgrade</span>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Detail Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-calendar-o"></i> Activities for <span id="modalDate"></span></h4>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Activities will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a href="<?php echo e(route('daily-activities.create')); ?>" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add Activity
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Create Activity Modal -->
<div class="modal fade" id="createActivityModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Add New Activity</h4>
            </div>
            <form id="createActivityForm" method="POST" action="<?php echo e(route('daily-activities.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Activity Date</label>
                        <input type="date" name="activity_date" class="form-control" id="activityDate" required>
                    </div>
                    <div class="form-group">
                        <label>Activity Type</label>
                        <select name="activity_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="installation">Installation</option>
                            <option value="support">Support</option>
                            <option value="training">Training</option>
                            <option value="meeting">Meeting</option>
                            <option value="documentation">Documentation</option>
                            <option value="repair">Repair</option>
                            <option value="upgrade">Upgrade</option>
                            <option value="monitoring">Monitoring</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="Describe what you did..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration_minutes" class="form-control" min="1" placeholder="60">
                    </div>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
<!-- FullCalendar CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<style>
.activity-legend {
    padding: 10px;
    background-color: #f4f4f4;
    border-radius: 3px;
}
.legend-item {
    margin-right: 15px;
    font-size: 12px;
}
.legend-item i {
    margin-right: 5px;
}
.fc-event {
    cursor: pointer;
}
.activity-item {
    padding: 8px;
    margin: 5px 0;
    border-left: 4px solid #3c8dbc;
    background-color: #f9f9f9;
}
.activity-item h5 {
    margin: 0 0 5px 0;
    font-weight: bold;
}
.activity-item small {
    color: #666;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var currentUserId = null;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {
            url: '<?php echo e(route("daily-activities.calendar-events")); ?>',
            method: 'GET',
            extraParams: function() {
                return {
                    user_id: currentUserId
                };
            },
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        eventClick: function(info) {
            loadDateActivities(info.event.startStr);
        },
        dateClick: function(info) {
            // Open create modal with selected date
            document.getElementById('activityDate').value = info.dateStr;
            $('#createActivityModal').modal('show');
        },
        eventDisplay: 'block',
        dayMaxEvents: 3,
        moreLinkClick: function(info) {
            loadDateActivities(info.date.toISOString().split('T')[0]);
        }
    });

    calendar.render();

    // Filter by user
    window.filterCalendar = function() {
        currentUserId = document.getElementById('userFilter').value;
        calendar.refetchEvents();
    };

    // Load activities for specific date
    function loadDateActivities(date) {
        var url = '<?php echo e(route("daily-activities.date-activities")); ?>';
        var params = new URLSearchParams({
            date: date,
            user_id: currentUserId || ''
        });

        fetch(url + '?' + params)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalDate').textContent = data.date;
                
                var modalBody = document.getElementById('modalBody');
                modalBody.innerHTML = '';

                if (data.activities.length > 0) {
                    data.activities.forEach(function(activity) {
                        var activityHtml = `
                            <div class="activity-item">
                                <h5><i class="${activity.icon}"></i> ${activity.description}</h5>
                                <small><strong>Type:</strong> ${activity.activity_type.charAt(0).toUpperCase() + activity.activity_type.slice(1)}</small><br>
                                <small><strong>User:</strong> ${activity.user_name}</small><br>
                                <small><strong>Duration:</strong> ${activity.duration_minutes || 'N/A'} minutes</small><br>
                                <small><strong>Time:</strong> ${activity.created_at}</small>
                                ${activity.notes ? '<br><small><strong>Notes:</strong> ' + activity.notes + '</small>' : ''}
                            </div>
                        `;
                        modalBody.innerHTML += activityHtml;
                    });
                } else {
                    modalBody.innerHTML = '<p class="text-muted">No activities found for this date.</p>';
                }

                $('#activityModal').modal('show');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading activities');
            });
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/daily-activities/calendar.blade.php ENDPATH**/ ?>
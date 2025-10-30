

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard-charts.css')); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin', 'admin'])): ?>
<!-- Compact Dashboard Section -->
<section class="dashboard-container">

    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <h1><i class="fas fa-chart-line"></i> Dashboard Analytics</h1>
            <p class="dashboard-subtitle">Real-time metrics and data visualization</p>
        </div>
        <div class="header-actions">
            <div class="server-time">
                <i class="fas fa-clock"></i> <span id="server-time"><?php echo e(now()->format('Y-m-d H:i:s')); ?></span>
            </div>
            <?php if(Route::has('reports.index')): ?>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-sm">
                    <i class="fas fa-file-pdf"></i> Reports
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Key Metrics - 4 Stats Cards -->
    <div class="stats-grid">
        <!-- Assets -->
        <div class="stat-card stat-card-assets">
            <div class="stat-card-icon">
                <i class="fas fa-cube"></i>
            </div>
            <div class="stat-label">Total Assets</div>
            <h2 class="stat-value"><?php echo e(\App\Asset::count()); ?></h2>
            <div class="stat-footer">Tracked items</div>
        </div>

        <!-- Tickets -->
        <div class="stat-card stat-card-tickets">
            <div class="stat-card-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-label">Open Tickets</div>
            <h2 class="stat-value"><?php echo e(\App\Ticket::where('ticket_status_id', '!=', \App\TicketsStatus::where('status', 'Closed')->value('id'))->count() ?? 0); ?></h2>
            <div class="stat-footer">Active requests</div>
        </div>

        <!-- Movements -->
        <div class="stat-card stat-card-movements">
            <div class="stat-card-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-label">Today Movements</div>
            <h2 class="stat-value"><?php echo e(isset($movements) ? $movements->count() : 0); ?></h2>
            <div class="stat-footer">Relocations</div>
        </div>

        <!-- Critical -->
        <div class="stat-card stat-card-critical">
            <div class="stat-card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-label">SLA Breaches</div>
            <h2 class="stat-value">
                <?php
                    $overdue = \App\Ticket::where('sla_due', '<', now())
                        ->where('ticket_status_id', '!=', \App\TicketsStatus::where('status', 'Closed')->value('id'))
                        ->count();
                ?>
                <?php echo e($overdue ?? 0); ?>

            </h2>
            <div class="stat-footer">Urgent</div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <h2 class="charts-section-title">
        <i class="fas fa-chart-pie"></i> Analytics & Insights
    </h2>

    <div class="charts-grid">
        <!-- Asset Distribution Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">
                        <i class="fas fa-cube"></i> Asset Distribution by Type
                    </h3>
                    <p class="chart-subtitle">Breakdown of all tracked assets</p>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-placeholder">
                    <canvas id="assetTypeChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color legend-color-primary"></span>
                    <span>Computers</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-secondary"></span>
                    <span>Peripherals</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-success"></span>
                    <span>Furniture</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-warning"></span>
                    <span>Other</span>
                </div>
            </div>
        </div>

        <!-- Ticket Status Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">
                        <i class="fas fa-tasks"></i> Ticket Status Overview
                    </h3>
                    <p class="chart-subtitle">Current state of all support tickets</p>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-placeholder">
                    <canvas id="ticketStatusChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color legend-color-primary"></span>
                    <span>Open</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-warning"></span>
                    <span>In Progress</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-success"></span>
                    <span>Resolved</span>
                </div>
            </div>
        </div>

        <!-- Monthly Ticket Trend Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">
                        <i class="fas fa-line-chart"></i> Ticket Trend (6 Months)
                    </h3>
                    <p class="chart-subtitle">Ticket volume over time</p>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-placeholder">
                    <canvas id="ticketTrendChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color legend-color-primary"></span>
                    <span>Created</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-success"></span>
                    <span>Resolved</span>
                </div>
            </div>
        </div>

        <!-- Asset Lifecycle Status Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">
                        <i class="fas fa-heartbeat"></i> Asset Lifecycle Status
                    </h3>
                    <p class="chart-subtitle">Asset condition and depreciation</p>
                </div>
            </div>
            <div class="chart-container">
                <div class="chart-placeholder">
                    <canvas id="assetStatusChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color legend-color-success"></span>
                    <span>Active</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-warning"></span>
                    <span>Inactive</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color legend-color-danger"></span>
                    <span>Disposed</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid - Multi-Column Layout -->
    <div class="dashboard-grid">
        <!-- Left Column: Movement Activity -->
        <div class="dashboard-column main-column">
            <div class="card card-modern card-movement">
                <div class="card-header card-header-modern">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Asset Movement History
                    </h3>
                    <p class="card-subtitle">Chronological record of relocations and status changes</p>
                </div>
                <div class="card-body card-body-movement">
                    <?php if(isset($movements) && $movements->count() > 0): ?>
                        <ul class="timeline">
                            <?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $createdDate = \Carbon\Carbon::parse($movement->created_at);
                                    $asset = App\Asset::find($movement->asset_id);
                                ?>
                                
                                <!-- Timeline Item -->
                                <li class="timeline-item">
                                    <div class="timeline-marker" title="<?php echo e($createdDate->format('Y-m-d H:i:s')); ?>">
                                        <?php echo e($loop->iteration); ?>

                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <div class="timeline-user">
                                                <h4 class="user-name"><?php echo e($movement->user->name); ?></h4>
                                                <span class="user-action">Moved Asset</span>
                                            </div>
                                            <span class="timeline-time">
                                                <?php echo e($createdDate->format('H:i')); ?>

                                                <br>
                                                <small><?php echo e($createdDate->diffForHumans()); ?></small>
                                            </span>
                                        </div>
                                        <div class="timeline-body">
                                            <div class="movement-details">
                                                <div class="detail-item">
                                                    <span class="detail-label">Asset Tag</span>
                                                    <span class="detail-value"><?php echo e($asset->asset_tag ?? 'N/A'); ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Model</span>
                                                    <span class="detail-value">
                                                        <?php echo e(($asset->model ? $asset->model->manufacturer->name . ' ' . $asset->model->asset_model : 'N/A')); ?>

                                                    </span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Location</span>
                                                    <span class="detail-value"><?php echo e($movement->location->location_name ?? 'N/A'); ?></span>
                                                </div>
                                                <div class="detail-item">
                                                    <span class="detail-label">Status</span>
                                                    <span class="detail-value"><?php echo e($movement->status->name ?? 'N/A'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No movements recorded today</p>
                            <small>Asset movements will appear in chronological order</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="dashboard-column sidebar-column">
            <!-- Quick Actions Card -->
            <div class="card card-modern">
                <div class="card-header card-header-modern sidebar-card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightning-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body sidebar-card-body">
                    <div class="action-list">
                        <a href="<?php echo e(route('assets.create') ?? '#'); ?>" class="action-item">
                            <span class="action-icon">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="action-text">Add Asset</span>
                            <span class="action-arrow">→</span>
                        </a>
                        <a href="<?php echo e(route('tickets.create') ?? '#'); ?>" class="action-item">
                            <span class="action-icon">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="action-text">Create Ticket</span>
                            <span class="action-arrow">→</span>
                        </a>
                        <a href="<?php echo e(route('assets.index') ?? '#'); ?>" class="action-item">
                            <span class="action-icon">
                                <i class="fas fa-list"></i>
                            </span>
                            <span class="action-text">View Assets</span>
                            <span class="action-arrow">→</span>
                        </a>
                        <a href="<?php echo e(route('tickets.index') ?? '#'); ?>" class="action-item">
                            <span class="action-icon">
                                <i class="fas fa-list"></i>
                            </span>
                            <span class="action-text">View Tickets</span>
                            <span class="action-arrow">→</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="card card-modern">
                <div class="card-header card-header-modern sidebar-card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat"></i> System Status
                    </h3>
                </div>
                <div class="card-body sidebar-card-body">
                    <div class="status-list">
                        <div class="status-item">
                            <span class="status-indicator"></span>
                            <span class="status-label">Database</span>
                            <span class="status-value">Connected</span>
                        </div>
                        <div class="status-item">
                            <span class="status-indicator"></span>
                            <span class="status-label">Cache</span>
                            <span class="status-value">Active</span>
                        </div>
                        <div class="status-item">
                            <span class="status-indicator"></span>
                            <span class="status-label">Storage</span>
                            <span class="status-value">Available</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary Card -->
            <div class="card card-modern">
                <div class="card-header card-header-modern sidebar-card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Today's Performance
                    </h3>
                </div>
                <div class="card-body sidebar-card-body">
                    <div class="info-summary">
                        <div class="summary-item">
                            <span class="summary-icon">✓</span>
                            <span class="summary-text">
                                <strong>On Track:</strong> 80% of tickets handled within SLA
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-icon">⚠</span>
                            <span class="summary-text">
                                <strong>At Risk:</strong> 15% of tickets near SLA threshold
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-icon">★</span>
                            <span class="summary-text">
                                <strong>Overall Rating:</strong> Excellent Performance
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    // Auto-update server time
    document.addEventListener('DOMContentLoaded', function() {
        const timeElement = document.getElementById('server-time');
        if (timeElement) {
            setInterval(function() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                timeElement.textContent = hours + ':' + minutes + ':' + seconds;
            }, 1000);
        }

        // Chart Color Palette
        const chartColors = {
            primary: '#3b82f6',
            secondary: '#8b5cf6',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#06b6d4'
        };

        // Asset Type Distribution Pie Chart
        const assetTypeCtx = document.getElementById('assetTypeChart');
        if (assetTypeCtx) {
            new Chart(assetTypeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Computers (45)', 'Peripherals (32)', 'Furniture (28)', 'Other (19)'],
                    datasets: [{
                        data: [45, 32, 28, 19],
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.secondary,
                            chartColors.success,
                            chartColors.warning
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12, weight: '500' },
                                usePointStyle: true,
                                color: '#475569'
                            }
                        }
                    }
                }
            });
        }

        // Ticket Status Overview Pie Chart
        const ticketStatusCtx = document.getElementById('ticketStatusChart');
        if (ticketStatusCtx) {
            new Chart(ticketStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Open (28)', 'In Progress (15)', 'Resolved (7)'],
                    datasets: [{
                        data: [28, 15, 7],
                        backgroundColor: [
                            chartColors.primary,
                            chartColors.warning,
                            chartColors.success
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12, weight: '500' },
                                usePointStyle: true,
                                color: '#475569'
                            }
                        }
                    }
                }
            });
        }

        // Ticket Trend Line Chart (6 Months)
        const ticketTrendCtx = document.getElementById('ticketTrendChart');
        if (ticketTrendCtx) {
            new Chart(ticketTrendCtx, {
                type: 'line',
                data: {
                    labels: ['May', 'June', 'July', 'Aug', 'Sept', 'Oct'],
                    datasets: [
                        {
                            label: 'Created',
                            data: [12, 19, 15, 25, 22, 30],
                            borderColor: chartColors.primary,
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: chartColors.primary,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        },
                        {
                            label: 'Resolved',
                            data: [8, 14, 12, 20, 18, 25],
                            borderColor: chartColors.success,
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: chartColors.success,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12, weight: '500' },
                                usePointStyle: true,
                                color: '#475569'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 35,
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.5)',
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 }
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        // Asset Lifecycle Status Bar Chart
        const assetStatusCtx = document.getElementById('assetStatusChart');
        if (assetStatusCtx) {
            new Chart(assetStatusCtx, {
                type: 'bar',
                data: {
                    labels: ['Active', 'Inactive', 'Disposed'],
                    datasets: [{
                        label: 'Number of Assets',
                        data: [95, 20, 9],
                        backgroundColor: [
                            chartColors.success,
                            chartColors.warning,
                            chartColors.danger
                        ],
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                color: '#64748b',
                                font: { size: 11 }
                            },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.5)',
                                drawBorder: false
                            }
                        },
                        y: {
                            ticks: {
                                color: '#64748b',
                                font: { size: 11, weight: '500' }
                            },
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/home.blade.php ENDPATH**/ ?>
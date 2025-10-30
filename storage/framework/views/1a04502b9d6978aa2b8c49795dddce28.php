

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => $pageTitle ?? 'Edit Asset',
    'subtitle' => 'Update asset information - ' . ($asset->asset_tag ?? ''),
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Edit']
    ],
    'actions' => '<a href="'.route('assets.show', $asset->id).'" class="btn btn-info">
        <i class="fa fa-eye"></i> View Asset
    </a>
    <a href="'.route('assets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Asset Information</h3>
        </div>
        <div class="box-body">
          
          <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <i class="icon fa fa-check"></i> <?php echo e(session('success')); ?>

            </div>
          <?php endif; ?>

          <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <i class="icon fa fa-ban"></i> <?php echo e(session('error')); ?>

            </div>
          <?php endif; ?>

          
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Asset Info:</strong>
            Created: <?php echo e($asset->created_at ? $asset->created_at->format('d M Y H:i') : 'N/A'); ?> |
            Last Updated: <?php echo e($asset->updated_at ? $asset->updated_at->format('d M Y H:i') : 'N/A'); ?>

          </div>

          <form method="POST" action="/assets/<?php echo e($asset->id); ?>" id="asset-edit-form">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            
            
            <fieldset>
              <legend><i class="fa fa-info-circle"></i> Basic Information</legend>

              <div class="form-group">
                <label for="asset_tag">Kode Assets <span class="text-red">*</span></label>
                <input type="text" name="asset_tag" id="asset_tag" class="form-control <?php $__errorArgs = ['asset_tag'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('asset_tag', $asset->asset_tag)); ?>" required maxlength="50">
                <small class="text-muted">Unique identifier for this asset (max 50 characters)</small>
                <?php $__errorArgs = ['asset_tag'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="asset_type_id">Kategori (Tipe Asset) <span class="text-red">*</span></label>
                <select name="asset_type_id" id="asset_type_id" class="form-control asset_type_id <?php $__errorArgs = ['asset_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                  <option value="">-- Pilih Kategori (Tipe) --</option>
                  <?php $__currentLoopData = $asset_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $atype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($atype->id); ?>" <?php echo e((old('asset_type_id', $asset->model->asset_type_id ?? '') == $atype->id) ? 'selected' : ''); ?>><?php echo e($atype->type_name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Category determines available models (PC, Laptop, Printer, etc.)</small>
                <?php $__errorArgs = ['asset_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="model_id">Model (optional)</label>
                <select name="model_id" id="model_id" class="form-control model_id <?php $__errorArgs = ['model_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                  <option value="">-- Pilih Model (optional) --</option>
                  <?php $__currentLoopData = $asset_models; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset_model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($asset_model->id); ?>" data-asset-type="<?php echo e($asset_model->asset_type_id); ?>" <?php echo e((old('model_id', $asset->model_id) == $asset_model->id) ? 'selected' : ''); ?>><?php echo e($asset_model->manufacturer->name ?? ''); ?> - <?php echo e($asset_model->asset_model); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Select model after choosing asset type above</small>
                <?php $__errorArgs = ['model_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="serial_number">S/N</label>
                <input type="text" name="serial_number" id="serial_number" class="form-control <?php $__errorArgs = ['serial_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('serial_number', $asset->serial_number)); ?>">
                <small class="text-muted">Manufacturer's serial number (optional)</small>
                <small id="serial-feedback" class="text-muted" style="display:none"></small>
                <?php $__errorArgs = ['serial_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="notes">Spesifikasi <span class="text-red">*</span></label>
                <textarea name="notes" id="notes" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" required><?php echo e(old('notes', $asset->notes)); ?></textarea>
                <small class="text-muted">Detailed specifications (e.g., RAM, CPU, Storage for computers)</small>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="status_id">Status <span class="text-red">*</span></label>
                <select class="form-control status_id <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="status_id" id="status_id" required>
                  <option value="">Select Status</option>
                  <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status->id); ?>" <?php echo e(($asset->status_id == $status->id) ? 'selected' : ''); ?>><?php echo e($status->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Current operational status</small>
                <?php $__errorArgs = ['status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </fieldset>

            
            <fieldset>
              <legend><i class="fa fa-map-marker"></i> Location & Assignment</legend>

              <div class="form-group">
                <label for="location_id">Lokasi <span class="text-red">*</span></label>
                <select class="form-control location_id <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="location_id" id="location_id" required>
                  <option value="">-- Pilih Lokasi --</option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>" <?php echo e((old('location_id', $asset->location_id) == $location->id) ? 'selected' : ''); ?>><?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Physical location where asset is deployed</small>
                <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="assigned_to">User / PIC <span class="text-red">*</span></label>
                <select name="assigned_to" id="assigned_to" class="form-control assigned_to <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                  <option value="">-- Pilih User / PIC --</option>
                  <?php $activeUsers = \App\User::where('is_active', 1)->orderBy('name')->get(); ?>
                  <?php $__currentLoopData = $activeUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($u->id); ?>" <?php echo e((old('assigned_to', $asset->assigned_to) == $u->id) ? 'selected' : ''); ?>><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Person responsible for this asset</small>
                <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </fieldset>

            
            <fieldset>
              <legend><i class="fa fa-shopping-cart"></i> Purchase & Warranty Information</legend>

              <div class="form-group">
                <label for="purchase_date">Tanggal Beli <span class="text-red">*</span></label>
                <input type="date" name="purchase_date" class="form-control <?php $__errorArgs = ['purchase_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="purchase_date" value="<?php echo e(old('purchase_date', optional($asset->purchase_date)->format('Y-m-d'))); ?>" required>
                <small class="text-muted">Date when asset was purchased</small>
                <?php $__errorArgs = ['purchase_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="supplier_id">Suplier <span class="text-red">*</span></label>
                <select class="form-control supplier_id <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="supplier_id" id="supplier_id" required>
                  <option value="">-- Pilih Supplier --</option>
                  <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->id); ?>" <?php echo e((old('supplier_id', $asset->supplier_id) == $supplier->id) ? 'selected' : ''); ?>><?php echo e($supplier->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Vendor who supplied this asset</small>
                <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="purchase_order_id">Purchase Order (Optional)</label>
                <select class="form-control purchase_order_id <?php $__errorArgs = ['purchase_order_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="purchase_order_id" id="purchase_order_id">
                  <option value="">-- No Purchase Order --</option>
                  <?php $__currentLoopData = $purchaseOrders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($po->id); ?>" <?php echo e((old('purchase_order_id', $asset->purchase_order_id) == $po->id) ? 'selected' : ''); ?>>
                      <?php echo e($po->po_number); ?> - <?php echo e($po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('Y-m-d') : ''); ?> - <?php echo e($po->supplier ? $po->supplier->name : ''); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Link to existing purchase order if applicable</small>
                <?php $__errorArgs = ['purchase_order_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="warranty_type_id">Jenis Garansi <span class="text-red">*</span></label>
                <select class="form-control warranty_type_id <?php $__errorArgs = ['warranty_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="warranty_type_id" id="warranty_type_id" required>
                  <option value="">-- Pilih Jenis Garansi --</option>
                  <?php $__currentLoopData = $warranty_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warranty_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($warranty_type->id); ?>" <?php echo e((old('warranty_type_id', $asset->warranty_type_id) == $warranty_type->id) ? 'selected' : ''); ?>><?php echo e($warranty_type->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Warranty coverage type</small>
                <?php $__errorArgs = ['warranty_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </fieldset>

            
            <fieldset>
              <legend><i class="fa fa-network-wired"></i> Network & Additional Details</legend>

              <div class="form-group">
                <label for="ip_address">IP Address</label>
                <input type="text" name="ip_address" class="form-control <?php $__errorArgs = ['ip_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="ip_address" value="<?php echo e(old('ip_address', $asset->ip_address)); ?>" placeholder="e.g., 192.168.1.100">
                <small class="text-muted">Only applicable for network devices</small>
                <?php $__errorArgs = ['ip_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="form-group">
                <label for="mac_address">MAC Address</label>
                <input type="text" name="mac_address" class="form-control <?php $__errorArgs = ['mac_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="mac_address" value="<?php echo e(old('mac_address', $asset->mac_address)); ?>" placeholder="e.g., 00:1B:44:11:3A:B7">
                <small class="text-muted">Hardware address for network identification</small>
                <?php $__errorArgs = ['mac_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </fieldset>

            
            <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-save"></i> <b>Update Asset</b>
              </button>
              <a href="<?php echo e(route('assets.show', $asset->id)); ?>" class="btn btn-info btn-lg">
                <i class="fa fa-eye"></i> View
              </a>
              <a href="<?php echo e(route('assets.index')); ?>" class="btn btn-secondary btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>

      
      <?php if(count($errors)): ?>
        <div class="alert alert-danger">
          <h4><i class="icon fa fa-ban"></i> Validation Errors!</h4>
          <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>


<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    // Form loading state
    $('#asset-edit-form').on('submit', function() {
      showLoading('Updating asset...');
    });

    // Serial number uniqueness check (AJAX) for edit form
    $(function(){
      $('#serial_number').on('blur', function(){
        var serial = $(this).val().trim();
        var excludeId = '<?php echo e($asset->id); ?>';
        if (!serial) {
          $('#serial-feedback').hide();
          return;
        }
        $.getJSON('<?php echo e(route("api.assets.checkSerial")); ?>', { serial: serial, exclude_id: excludeId })
          .done(function(resp){
            if (resp && resp.success) {
              if (resp.exists) {
                $('#serial-feedback').show().removeClass('text-muted text-success').addClass('text-danger').text('Serial number already exists in the system.');
              } else {
                $('#serial-feedback').show().removeClass('text-danger text-muted').addClass('text-success').text('Serial number available.');
              }
            }
          }).fail(function(){
            $('#serial-feedback').hide();
          });
      });
    });

    $(":input").keypress(function(event){
      if (event.which == '10' || event.which == '13') {
        event.preventDefault();
      }
    });
  </script>

  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2 for all dropdowns
      $(".model_id").select2();
      $(".division_id").select2();
      $(".supplier_id").select2();
      $(".invoice_id").select2();
      $(".warranty_type_id").select2();
      $(".status_id").select2();
      $(".location_id").select2();
      $(".assigned_to").select2();
      $(".asset_type_id").select2();
      $(".purchase_order_id").select2();

      // When asset type changes in edit form, filter model options and toggle PC fields
      $('#asset_type_id').on('change', function() {
        var selectedText = $(this).find('option:selected').text();
        var selectedId = $(this).val();
        $('.pc-laptop-fields').hide();
        if (selectedText && (selectedText.toLowerCase().includes('pc') || selectedText.toLowerCase().includes('laptop') || selectedText.toLowerCase().includes('computer'))) {
          $('.pc-laptop-fields').show();
        }
        $('#model_id option').each(function() {
          var mt = $(this).data('asset-type') ? String($(this).data('asset-type')) : '';
          if (!selectedId || mt === '' || mt === selectedId) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
        if ($('#model_id option:selected').is(':hidden')) {
          $('#model_id').val('').trigger('change');
        }
      });

      // Trigger change on load to apply filtering if an asset type is already selected
      if ($('#asset_type_id').val()) {
        $('#asset_type_id').trigger('change');
      }
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/assets/edit.blade.php ENDPATH**/ ?>
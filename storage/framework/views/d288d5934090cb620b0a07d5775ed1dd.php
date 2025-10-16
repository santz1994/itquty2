

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-qrcode"></i> Scan QR Code
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Scan Options:</strong>
                            <ul style="margin-bottom: 0; margin-top: 10px;">
                                <li>Use your device camera to scan a QR code</li>
                                <li>Upload an image containing a QR code</li>
                                <li>Manually enter the asset tag or serial number</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Camera Scan Section -->
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fa fa-camera"></i> Camera Scan</h4>
                        <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                        <div id="qr-reader-results" class="alert alert-success" style="display: none; margin-top: 20px;">
                            <strong>Scanned Successfully!</strong>
                            <p id="scanned-text"></p>
                            <a href="#" id="view-asset-link" class="btn btn-success">
                                <i class="fa fa-eye"></i> View Asset Details
                            </a>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Upload Image Section -->
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fa fa-upload"></i> Upload QR Code Image</h4>
                        <form id="qr-upload-form" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Select QR Code Image</label>
                                <input type="file" name="qr_image" id="qr-image-input" class="form-control" accept="image/*">
                                <small class="help-block">Supported formats: JPG, PNG, GIF</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Process Image
                            </button>
                        </form>
                        <div id="upload-result" style="margin-top: 20px;"></div>
                    </div>
                </div>

                <hr>

                <!-- Manual Search Section -->
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fa fa-keyboard-o"></i> Manual Search</h4>
                        <form id="manual-search-form">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Asset Tag or Serial Number</label>
                                <input type="text" name="search_term" id="search-term-input" class="form-control" placeholder="Enter asset tag or serial number">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Search Asset
                            </button>
                        </form>
                        <div id="search-result" style="margin-top: 20px;"></div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- Include Html5-QRCode library from CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
$(document).ready(function() {
    let html5QrcodeScanner = null;

    // Initialize QR Code Scanner
    function initializeScanner() {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", 
            { 
                fps: 10, 
                qrbox: {width: 250, height: 250},
                rememberLastUsedCamera: true
            },
            /* verbose= */ false
        );
        
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    // On successful scan
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Code matched = ${decodedText}`, decodedResult);
        
        // Stop scanning
        html5QrcodeScanner.clear();
        
        // Show result
        $('#scanned-text').text('Asset Tag/Serial: ' + decodedText);
        $('#qr-reader-results').show();
        
        // Search for asset
        searchAsset(decodedText, function(assetId) {
            $('#view-asset-link').attr('href', '/assets/' + assetId);
        });
    }

    // On scan failure
    function onScanFailure(error) {
        // Handle scan failure, usually better to ignore
        // console.warn(`Code scan error = ${error}`);
    }

    // Search for asset by tag or serial
    function searchAsset(searchTerm, callback) {
        $.ajax({
            url: '/assets/search-by-qr',
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                search_term: searchTerm
            },
            success: function(response) {
                if (response.success && response.asset) {
                    callback(response.asset.id);
                } else {
                    alert('Asset not found!');
                }
            },
            error: function() {
                alert('Error searching for asset. Please try manual search.');
            }
        });
    }

    // Manual search form
    $('#manual-search-form').on('submit', function(e) {
        e.preventDefault();
        
        let searchTerm = $('#search-term-input').val();
        if (!searchTerm) {
            alert('Please enter an asset tag or serial number');
            return;
        }

        $.ajax({
            url: '/assets/search-by-qr',
            method: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                search_term: searchTerm
            },
            success: function(response) {
                if (response.success && response.asset) {
                    let asset = response.asset;
                    $('#search-result').html(`
                        <div class="alert alert-success">
                            <h4><i class="fa fa-check-circle"></i> Asset Found!</h4>
                            <p><strong>Asset Tag:</strong> ${asset.asset_tag}</p>
                            <p><strong>Name:</strong> ${asset.name}</p>
                            <p><strong>Serial:</strong> ${asset.serial || 'N/A'}</p>
                            <a href="/assets/${asset.id}" class="btn btn-success">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                        </div>
                    `);
                } else {
                    $('#search-result').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-times-circle"></i> Asset not found. Please check the asset tag or serial number.
                        </div>
                    `);
                }
            },
            error: function() {
                $('#search-result').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> Error searching for asset. Please try again.
                    </div>
                `);
            }
        });
    });

    // Initialize scanner on page load
    initializeScanner();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/assets/scan-qr.blade.php ENDPATH**/ ?>
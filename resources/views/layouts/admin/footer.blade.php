<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col my-1">
                <p class="m-0 fw-semibold">© Copyright {{ date('Y') }} · {{ config_get('site_name', 'ShopGame') }}</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts from old theme (to ensure functionality) -->
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewMultipleImages(input, previewId) {
        var preview = document.getElementById(previewId);
        preview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '200px';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>

<!-- New theme Scripts -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="/cmsbvq/template/frontend/js/plugins/popper.min.js"></script>
<script src="/cmsbvq/template/frontend/js/plugins/simplebar.min.js"></script>
<script src="/cmsbvq/template/frontend/js/plugins/bootstrap.min.js"></script>
<script src="/cmsbvq/template/frontend/js/icon/custom-font.js"></script>
<script src="/cmsbvq/template/frontend/js/script.js"></script>
<script src="/cmsbvq/template/frontend/js/theme.js"></script>
<script src="/cmsbvq/template/frontend/js/plugins/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.min.js"></script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const body = document.body;
        body.setAttribute('data-pc-preset', 'preset-1');
        body.setAttribute('data-pc-sidebar-caption', 'true');
        body.setAttribute('data-pc-layout', 'vertical');
        body.setAttribute('data-pc-direction', 'ltr');
        body.setAttribute('data-pc-theme_contrast', '');
        body.setAttribute('data-pc-theme', 'dark');
        body.setAttribute('style', '');

        // SweetAlert2 Global Notifications
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                html: {!! json_encode(session('success')) !!},
                confirmButtonText: 'Đóng',
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Thất bại!',
                html: {!! json_encode(session('error')) !!},
                confirmButtonText: 'Đóng',
                customClass: {
                   
                    confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });
        @endif

        // Hide legacy bootstrap alerts globally
        document.querySelectorAll('.alert-success, .alert-danger').forEach(alert => {
            alert.style.display = 'none';
        });

        // Xử lý xem trước và kéo thả cho tất cả .image-upload trong Admin
        document.querySelectorAll('.image-upload').forEach(function(wrapper) {
            var input = wrapper.querySelector('input[type="file"]');
            if (!input) return;

            var defaultContent = wrapper.querySelector('.image-uploads');
            var previewContainer = document.createElement('div');
            previewContainer.className = 'image-preview-container';
            previewContainer.style.display = 'none';
            wrapper.appendChild(previewContainer);

            function updatePreviews(files) {
                if (!files || files.length === 0) return;
                previewContainer.innerHTML = '';
                
                Array.from(files).forEach(function(file) {
                    if (!file.type.startsWith('image/')) return;
                    var item = document.createElement('div');
                    item.className = 'image-preview-item';

                    var img = document.createElement('img');
                    img.src = URL.createObjectURL(file);

                    var meta = document.createElement('span');
                    meta.className = 'image-preview-meta';
                    var sizeKB = (file.size / 1024).toFixed(1);
                    var sizeText = sizeKB > 1024 ? (sizeKB / 1024).toFixed(2) + ' MB' : sizeKB + ' KB';
                    meta.textContent = '✓ ' + file.name + ' (' + sizeText + ')';

                    item.appendChild(img);
                    item.appendChild(meta);
                    previewContainer.appendChild(item);
                });

                if (previewContainer.children.length > 0) {
                    previewContainer.style.display = 'flex';
                    if (defaultContent) {
                        defaultContent.innerHTML = '<span class="badge bg-success mb-2"><i class="ti ti-check me-1"></i>Đã chọn ' + files.length + ' ảnh</span><p class="text-muted small mb-0">Click hoặc kéo thả để đổi ảnh khác</p>';
                    }
                }
            }

            input.addEventListener('change', function() {
                updatePreviews(this.files);
            });

            ['dragenter', 'dragover'].forEach(function(eventName) {
                wrapper.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    wrapper.classList.add('is-dragover');
                }, false);
            });

            ['dragleave', 'dragend', 'drop'].forEach(function(eventName) {
                wrapper.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    wrapper.classList.remove('is-dragover');
                }, false);
            });

            wrapper.addEventListener('drop', function(e) {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    input.files = e.dataTransfer.files;
                    updatePreviews(e.dataTransfer.files);
                }
            });
        });

        // Thêm trạng thái loading cho các form submit có upload ảnh
        document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function(form) {
            form.addEventListener('submit', function() {
                var submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    setTimeout(function() {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang lưu & tải ảnh lên...';
                    }, 50);
                }
            });
        });
    });
</script>

@stack('scripts')

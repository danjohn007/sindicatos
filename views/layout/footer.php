    <?php if (is_logged_in()): ?>
            </main>
        </div>
    </div>
    <?php else: ?>
    </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                var closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);

        // Confirm delete actions
        function confirmDelete(message) {
            return confirm(message || '¿Está seguro de que desea eliminar este registro?');
        }

        // CSRF token for AJAX requests
        const csrfToken = '<?php echo generate_csrf_token(); ?>';

        // Worker search functionality
        function searchWorkers(input, targetDiv) {
            const term = input.value;
            if (term.length < 2) {
                document.getElementById(targetDiv).innerHTML = '';
                return;
            }

            fetch('index.php?page=api&action=search_workers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'term=' + encodeURIComponent(term) + '&csrf_token=' + csrfToken
            })
            .then(response => response.json())
            .then(data => {
                let html = '';
                data.forEach(worker => {
                    html += `<div class="list-group-item list-group-item-action" onclick="selectWorker(${worker.id}, '${worker.full_name}', '${worker.whatsapp}', '${worker.department}')">
                        <strong>${worker.full_name}</strong> (${worker.worker_number})<br>
                        <small class="text-muted">${worker.department} - ${worker.whatsapp}</small>
                    </div>`;
                });
                document.getElementById(targetDiv).innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
        }

        function selectWorker(id, name, whatsapp, department) {
            document.getElementById('worker_id').value = id;
            document.getElementById('worker_name').value = name;
            document.getElementById('worker_whatsapp').value = whatsapp;
            document.getElementById('department').value = department;
            document.getElementById('search_results').innerHTML = '';
            document.getElementById('worker_search').value = name;
        }

        // File upload preview
        function previewFiles(input) {
            const preview = document.getElementById('file_preview');
            preview.innerHTML = '';
            
            if (input.files) {
                for (let file of input.files) {
                    const div = document.createElement('div');
                    div.className = 'border p-2 mb-2 rounded';
                    div.innerHTML = `
                        <i class="fas fa-file"></i> ${file.name} 
                        <small class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</small>
                    `;
                    preview.appendChild(div);
                }
            }
        }

        // Auto-save form data to localStorage
        function autoSaveForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return;

            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    localStorage.setItem(formId + '_' + this.name, this.value);
                });

                // Restore saved values
                const savedValue = localStorage.getItem(formId + '_' + input.name);
                if (savedValue && !input.value) {
                    input.value = savedValue;
                }
            });

            // Clear saved data on successful submit
            form.addEventListener('submit', function() {
                inputs.forEach(input => {
                    localStorage.removeItem(formId + '_' + input.name);
                });
            });
        }

        // Initialize auto-save for request forms
        document.addEventListener('DOMContentLoaded', function() {
            autoSaveForm('request_form');
            autoSaveForm('public_request_form');
        });
    </script>

    <?php if (isset($additional_js)): ?>
        <?php echo $additional_js; ?>
    <?php endif; ?>
</body>
</html>
function showStatusModal(recordId, status) {
    document.getElementById('record_id').value = recordId;
    document.getElementById('new_status').value = status;
    document.getElementById('modalTitle').textContent = status === 'APPROVED' ? 'Approve Record' : 'Reject Record';
    document.getElementById('confirmBtn').textContent = status === 'APPROVED' ? 'Approve' : 'Reject';
    document.getElementById('confirmBtn').className = status === 'APPROVED' ? 'modal-btn btn-approve' : 'modal-btn btn-reject';
    document.getElementById('statusModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function viewDocument(documentPath, title) {
    document.getElementById('docModalTitle').textContent = title;
    const docContainer = document.getElementById('docContainer');
    
    // Check file extension
    const fileExt = documentPath.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
        // Image file
        docContainer.innerHTML = `<img src="${documentPath}" alt="${title}" style="max-width: 100%;">`;
    } else if (['pdf'].includes(fileExt)) {
        // PDF file
        docContainer.innerHTML = `<embed src="${documentPath}" type="application/pdf" width="100%" height="600px">`;
    } else {
        docContainer.innerHTML = 'Unsupported file type';
    }
    
    document.getElementById('documentModal').style.display = 'block';
}

function viewDetails(recordId) {
    // Fetch record details using AJAX
    fetch(`get_marriage_details.php?id=${recordId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('detailsContainer').innerHTML = data;
            document.getElementById('detailsModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching record details');
        });
}

// Add form submission handling
document.addEventListener('DOMContentLoaded', function() {
    const statusForm = document.getElementById('statusForm');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Submit form using fetch
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Close the modal
                closeModal('statusModal');
                // Reload the page to show updated status
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status');
            });
        });
    }
});
// Function to show status modal
function showStatusModal(recordId, status) {
    document.getElementById('record_id').value = recordId;
    document.getElementById('new_status').value = status;

    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');

    if (status === 'APPROVED') {
        modalTitle.textContent = 'Approve Death Certificate';
        confirmBtn.textContent = 'Approve';
        confirmBtn.className = 'modal-btn btn-approve';
    } else {
        modalTitle.textContent = 'Reject Death Certificate';
        confirmBtn.textContent = 'Reject';
        confirmBtn.className = 'modal-btn btn-reject';
    }

    document.getElementById('statusModal').style.display = 'flex';
}

// Function to view document
function viewDocument(documentPath, documentTitle) {
    const docContainer = document.getElementById('docContainer');
    const docModalTitle = document.getElementById('docModalTitle');

    docModalTitle.textContent = documentTitle;

    // Check file extension
    const fileExt = documentPath.split('.').pop().toLowerCase();

    if (fileExt === 'pdf') {
        docContainer.innerHTML = `<embed src="${documentPath}" type="application/pdf" width="100%" height="600px" />`;
    } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
        docContainer.innerHTML = `<img src="${documentPath}" alt="${documentTitle}" />`;
    } else {
        docContainer.innerHTML = `<p>Unable to preview this file type. <a href="${documentPath}" target="_blank">Download instead</a></p>`;
    }

    document.getElementById('documentModal').style.display = 'flex';
}

// Function to view details
function viewDetails(recordId) {
    // Fetch record details via AJAX with full path
    fetch(`get_death_record.php?id=${recordId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Format the details with improved styling and organization
            let detailsHtml = `
                <div class="details-container">
                    <div class="details-grid">
                        <!-- Personal Information Section -->
                        <div class="details-section">
                            <h3>Personal Information</h3>
                            <p><strong>Name:</strong> ${data.name_of_deceased || 'N/A'}</p>
                            <p><strong>Gender:</strong> ${data.gender || 'N/A'}</p>
                            <p><strong>Date Of Birth:</strong> ${data.birth_date || 'N/A'}</p>
                            <p><strong>Aadhaar Number:</strong> ${data.aadhaar_number || 'N/A'}</p>
                            <p><strong>Address:</strong> ${data.address || 'N/A'}</p>
                        </div>

                        <!-- Death Details Section -->
                        <div class="details-section">
                            <h3>Death Details</h3>
                            <p><strong>Date of Death:</strong> ${data.date_of_death ? formatDate(data.date_of_death) : 'N/A'}</p>
                            <p><strong>Place of Death:</strong> ${data.place_of_death || 'N/A'}</p>
                            <p><strong>Cause of Death:</strong> ${data.cause_of_death || 'N/A'}</p>
                        </div>

                        <!-- Certificate Information Section -->
                        <div class="details-section">
                            <h3>Certificate Information</h3>
                            <p><strong>Certificate Number:</strong> ${data.certificate_number || 'N/A'}</p>
                            <p><strong>Registration Date:</strong> ${data.registration_date ? formatDate(data.registration_date) : 'N/A'}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-${(data.status || 'pending').toLowerCase()}">${data.status || 'PENDING'}</span></p>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="details-section">
                            <h3>Additional Information</h3>
                            <p><strong>Created At:</strong> ${data.created_at ? formatDateTime(data.created_at) : 'N/A'}</p>
                            ${data.updated_at ? `<p><strong>Updated At:</strong> ${formatDateTime(data.updated_at)}</p>` : ''}
                            ${data.remarks ? `<p><strong>Remarks:</strong> ${data.remarks}</p>` : ''}
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('detailsContainer').innerHTML = detailsHtml;
            document.getElementById('detailsModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching record details:', error);
            alert('Failed to load record details');
        });
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid date';
        return date.toLocaleDateString('en-IN');
    } catch (e) {
        return 'Invalid date format';
    }
}

// Helper function to format datetime
function formatDateTime(dateTimeString) {
    if (!dateTimeString) return 'N/A';
    try {
        const date = new Date(dateTimeString);
        if (isNaN(date.getTime())) return 'Invalid date';
        return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN');
    } catch (e) {
        return 'Invalid date format';
    }
}

// Function to close modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modals when clicking outside the content
window.onclick = function (event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}
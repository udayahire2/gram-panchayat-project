// Format date to DD/MM/YYYY
function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Close modal and reset form if applicable
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        if (modalId === 'statusModal') {
            document.getElementById('statusForm')?.reset();
        }
    }
}

// Display document preview in modal
function viewDocument(filePath, docType) {
    const fileExt = filePath.split('.').pop().toLowerCase();
    const docContainer = document.getElementById('docContainer');
    const docModalTitle = document.getElementById('docModalTitle');
    
    if (!docContainer || !docModalTitle) return;

    let docContent = '';
    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
        docContent = `<img src="${filePath}" alt="${docType}" class="img-fluid" onerror="this.src='../assets/default-image.png';">`;
    } else if (fileExt === 'pdf') {
        docContent = `<iframe src="${filePath}" width="100%" height="500px" style="border: none;"></iframe>`;
    } else {
        docContent = `<p>Cannot preview this file type. <a href="${filePath}" target="_blank" class="btn btn-primary btn-sm">Download file</a></p>`;
    }

    docContainer.innerHTML = docContent;
    docModalTitle.textContent = `${docType} Preview`;
    document.getElementById('documentModal').style.display = 'flex';
}

// Fetch and display detailed record information
function viewDetails(recordId) {
    fetch(`get_marriage_record.php?id=${recordId}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            const status = data.status || 'PENDING';
            const detailsHtml = `
                <div class="details-container">
                    <h3>Marriage Certificate Details</h3>
                    
                    <div class="details-section">
                        <h4>Husband Details</h4>
                        <div class="details-grid">
                            <div class="detail-item"><strong>Name:</strong> ${data.husband_name || 'N/A'}</div>
                            <div class="detail-item"><strong>Address:</strong> ${data.husband_address || 'N/A'}</div>
                            <div class="detail-item"><strong>Date of Birth:</strong> ${data.husband_dob ? formatDate(data.husband_dob) : 'N/A'}</div>
                            <div class="detail-item"><strong>Age:</strong> ${data.husband_age || 'N/A'}</div>
                            <div class="detail-item"><strong>Caste:</strong> ${data.husband_cast || 'N/A'}</div>
                            <div class="detail-item"><strong>Aadhar Number:</strong> ${data.husband_aadhaar || 'N/A'}</div>
                        </div>
                        ${data.husband_photo ? `
                            <div class="document-item">
                                <strong>Photo:</strong>
                                <div class="document-preview">
                                    <img src="../uploads/marriage_certificates/${data.husband_photo}" alt="Husband Photo" class="preview-thumbnail" onerror="this.src='../assets/profile-icon.png';">
                                    <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.husband_photo}', 'Husband Photo'); return false;">View Photo</a>
                                </div>
                            </div>` : ''}
                        ${data.husband_aadhar_doc ? `
                            <div class="document-item">
                                <strong>Aadhar Document:</strong>
                                <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.husband_aadhar_doc}', 'Husband Aadhar'); return false;">View Document</a>
                            </div>` : ''}
                    </div>

                    <div class="details-section">
                        <h4>Wife Details</h4>
                        <div class="details-grid">
                            <div class="detail-item"><strong>Name:</strong> ${data.wife_name || 'N/A'}</div>
                            <div class="detail-item"><strong>Address:</strong> ${data.wife_address || 'N/A'}</div>
                            <div class="detail-item"><strong>Date of Birth:</strong> ${data.wife_dob ? formatDate(data.wife_dob) : 'N/A'}</div>
                            <div class="detail-item"><strong>Age:</strong> ${data.wife_age || 'N/A'}</div>
                            <div class="detail-item"><strong>Caste:</strong> ${data.wife_cast || 'N/A'}</div>
                            <div class="detail-item"><strong>Aadhar Number:</strong> ${data.wife_aadhaar || 'N/A'}</div>
                        </div>
                        ${data.wife_photo ? `
                            <div class="document-item">
                                <strong>Photo:</strong>
                                <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.wife_photo}', 'Wife Photo'); return false;">View Photo</a>
                            </div>` : ''}
                        ${data.wife_aadhar_doc ? `
                            <div class="document-item">
                                <strong>Aadhar Document:</strong>
                                <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.wife_aadhar_doc}', 'Wife Aadhar'); return false;">View Document</a>
                            </div>` : ''}
                    </div>

                    <div class="details-section">
                        <h4>Marriage Details</h4>
                        <div class="details-grid">
                            <div class="detail-item"><strong>Marriage Date:</strong> ${data.marriage_date ? formatDate(data.marriage_date) : 'N/A'}</div>
                            <div class="detail-item"><strong>Place of Marriage:</strong> ${data.place_of_marriage || 'N/A'}</div>
                            <div class="detail-item"><strong>Registration Date:</strong> ${data.registration_date ? formatDate(data.registration_date) : 'N/A'}</div>
                            <div class="detail-item"><strong>Certificate Number:</strong> ${data.certificate_no || 'N/A'}</div>
                            <div class="detail-item"><strong>Status:</strong> <span class="status-badge status-${status.toLowerCase()}">${status}</span></div>
                            <div class="detail-item"><strong>Remarks:</strong> ${data.remarks || 'N/A'}</div>
                        </div>
                    </div>

                    <div class="details-section">
                        <h4>Witness Details</h4>
                        <div class="details-grid">
                            <div class="detail-item"><strong>Witness 1:</strong> ${data.witness1_name || 'N/A'}</div>
                            <div class="detail-item"><strong>Witness 2:</strong> ${data.witness2_name || 'N/A'}</div>
                            <div class="detail-item"><strong>Witness 3:</strong> ${data.witness3_name || 'N/A'}</div>
                        </div>
                        ${data.witness1_aadhar_doc ? `
                            <div class="document-item">
                                <strong>Witness 1 Document:</strong>
                                <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.witness1_aadhar_doc}', 'Witness 1 Document'); return false;">View Document</a>
                            </div>` : ''}
                        ${data.witness2_aadhar_doc ? `
                            <div class="document-item">
                                <strong>Witness 2 Document:</strong>
                                <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.witness2_aadhar_doc}', 'Witness 2 Document'); return false;">View Document</a>
                            </div>` : ''}
                        ${data.witness3_aadhar_doc ? `
                            <div class="document-item">
                                <strong>Witness 3 Document:</strong>
                                <a href="#" onclick="viewDocument('../uploads/marriage_certificates/${data.witness3_aadhar_doc}', 'Witness 3 Document'); return false;">View Document</a>
                            </div>` : ''}
                    </div>
                </div>
            `;

            const detailsContainer = document.getElementById('detailsContainer');
            if (detailsContainer) {
                detailsContainer.innerHTML = detailsHtml;
                document.getElementById('detailsModal').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error fetching details:', error);
            alert(`Failed to load record details: ${error.message}`);
        });
}

// Show status update modal
function showStatusModal(recordId, status) {
    const modal = document.getElementById('statusModal');
    if (!modal) return;

    const title = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');
    const recordInput = document.getElementById('record_id');
    const statusInput = document.getElementById('new_status');

    recordInput.value = recordId;
    statusInput.value = status;
    title.textContent = status === 'APPROVED' ? 'Approve Certificate' : 'Reject Certificate';
    confirmBtn.className = `modal-btn btn-${status === 'APPROVED' ? 'success' : 'danger'}`;
    confirmBtn.textContent = status === 'APPROVED' ? 'Approve' : 'Reject';

    modal.style.display = 'flex';
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', () => {
    const modals = document.querySelectorAll('.modal');
    const statusForm = document.getElementById('statusForm');

    // Close modals when clicking outside
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal.id);
        });
    });

    // Close modals with ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modals.forEach(modal => {
                if (modal.style.display === 'flex') closeModal(modal.id);
            });
        }
    });

    // Handle form submission
    if (statusForm) {
        statusForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(statusForm);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error('Form submission failed');
                    return response.text();
                })
                .then(() => {
                    closeModal('statusModal');
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error submitting form:', error);
                    alert('Failed to update status. Please try again.');
                });
        });
    }
});
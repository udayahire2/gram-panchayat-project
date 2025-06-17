function showStatusModal(recordId, status) {
    document.getElementById('record_id').value = recordId;
    document.getElementById('new_status').value = status;
    const modalTitle = document.getElementById('modalTitle');
    const confirmBtn = document.getElementById('confirmBtn');
    
    if (status === 'APPROVED') {
        modalTitle.textContent = 'Approve Marriage Certificate';
        confirmBtn.textContent = 'Approve';
        confirmBtn.className = 'modal-btn btn-approve';
    } else {
        modalTitle.textContent = 'Reject Marriage Certificate';
        confirmBtn.textContent = 'Reject';
        confirmBtn.className = 'modal-btn btn-reject';
    }
    
    document.getElementById('statusModal').style.display = 'flex';
}

function viewDocument(documentPath, documentTitle) {
    const docContainer = document.getElementById('docContainer');
    const docModalTitle = document.getElementById('docModalTitle');
    
    docModalTitle.textContent = documentTitle;
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
function viewDetails(recordId) {
    fetch(`get_marriage_record.php?id=${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            // Set default values for undefined properties
            const status = data.status || 'PENDING';
            
            let detailsHtml = `
                <div class="details-container">
                    <h3>Marriage Certificate Details</h3>
                    
                    <div class="details-section">
                        <h4>Husband Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <strong>Name:</strong> ${data.husband_name || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Address:</strong> ${data.husband_address || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Date of Birth:</strong> ${data.husband_dob ? formatDate(data.husband_dob) : 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Age:</strong> ${data.husband_age || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Caste:</strong> ${data.husband_cast || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Aadhar Number:</strong> ${data.husband_aadhar_no || 'N/A'}
                            </div>
                        </div>
                        ${data.husband_photo ? `
                        <div class="document-item">
                            <strong>Photo:</strong>
                            <div class="document-preview">
                                <img src="/CPP WEB/villager/uploads/marriage_certificates${data.husband_photo}" alt="Husband Photo" class="preview-thumbnail" onerror="this.onerror=null; this.src='../assets/profile-icon.png';">
                                <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.husband_photo}', 'Husband Photo')">View Photo</a>
                            </div>
                        </div>` : ''}
                        ${data.husband_aadhar_doc ? `
                        <div class="document-item">
                            <strong>Aadhar Document:</strong>
                            <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.husband_aadhar_doc}', 'Husband Aadhar')">View Document</a>
                        </div>` : ''}
                    </div>
                    
                    <div class="details-section">
                        <h4>Wife Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <strong>Name:</strong> ${data.wife_name || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Address:</strong> ${data.wife_address || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Date of Birth:</strong> ${data.wife_dob ? formatDate(data.wife_dob) : 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Age:</strong> ${data.wife_age || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Caste:</strong> ${data.wife_cast || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Aadhar Number:</strong> ${data.wife_aadhar_no || 'N/A'}
                            </div>
                        </div>
                        ${data.wife_photo ? `
                        <div class="document-item">
                            <strong>Photo:</strong>
                            <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.wife_photo}', 'Wife Photo')">View Photo</a>
                        </div>` : ''}
                        ${data.wife_aadhar_doc ? `
                        <div class="document-item">
                            <strong>Aadhar Document:</strong>
                            <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.wife_aadhar_doc}', 'Wife Aadhar')">View Document</a>
                        </div>` : ''}
                    </div>
                    
                    <div class="details-section">
                        <h4>Marriage Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <strong>Marriage Date:</strong> ${data.marriage_date ? formatDate(data.marriage_date) : 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Place of Marriage:</strong> ${data.marriage_place || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Registration Date:</strong> ${data.registration_date ? formatDate(data.registration_date) : 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Certificate Number:</strong> ${data.certificate_no || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Status:</strong> <span class="status-badge status-${status.toLowerCase()}">${status}</span>
                            </div>
                            <div class="detail-item">
                                <strong>Remarks:</strong> ${data.remarks || 'N/A'}
                            </div>
                        </div>
                    </div>
                    
                    <div class="details-section">
                        <h4>Witness Details</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <strong>Witness 1:</strong> ${data.witness1_name || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Witness 2:</strong> ${data.witness2_name || 'N/A'}
                            </div>
                            <div class="detail-item">
                                <strong>Witness 3:</strong> ${data.witness3_name || 'N/A'}
                            </div>
                        </div>
                        ${data.witness1_aadhar_doc ? `
                        <div class="document-item">
                            <strong>Witness 1 Document:</strong>
                            <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.witness1_aadhar_doc}', 'Witness 1 Document')">View Document</a>
                        </div>` : ''}
                        ${data.witness2_aadhar_doc ? `
                        <div class="document-item">
                            <strong>Witness 2 Document:</strong>
                            <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.witness2_aadhar_doc}', 'Witness 2 Document')">View Document</a>
                        </div>` : ''}
                        ${data.witness3_aadhar_doc ? `
                        <div class="document-item">
                            <strong>Witness 3 Document:</strong>
                            <a href="#" onclick="viewDocument('/CPP WEB/villager/uploads/marriage_certificates/${data.witness3_aadhar_doc}', 'Witness 3 Document')">View Document</a>
                        </div>` : ''}
                    </div>
                </div>
            `;
            
            document.getElementById('detailsContainer').innerHTML = detailsHtml;
            document.getElementById('detailsModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load record details: ' + error.message);
        });
}
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
}
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN');
}
function viewAllDocuments(recordId) {
    fetch(`get_marriage_documents.php?id=${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            let documentsHtml = '<div class="documents-grid">';
            
            data.documents.forEach(doc => {
                const filePath = `${doc.path}${doc.file}`;
                const fileExt = doc.file.split('.').pop().toLowerCase();
                
                documentsHtml += `
                    <div class="document-item">
                        <h4>${doc.type}</h4>
                        <div class="document-preview-large">
                            ${['jpg', 'jpeg', 'png'].includes(fileExt) 
                                ? `<img src="${filePath}" alt="${doc.type}" onerror="this.onerror=null; this.src='../assets/document-icon.png';">`
                                : `<img src="../assets/document-icon.png" alt="Document Icon">`
                            }
                            <div class="document-actions">
                                <button onclick="viewDocument('${filePath}', '${doc.type}')" class="btn btn-primary btn-sm">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            documentsHtml += '</div>';
            
            document.getElementById('allDocumentsContainer').innerHTML = documentsHtml;
            document.getElementById('allDocumentsModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load documents: ' + error.message);
        });
}
       
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN');
}
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('en-IN') + ' ' + date.toLocaleTimeString('en-IN');
}
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}